<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\BaseController;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends BaseController
{
    protected ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    /**
     * Отображение конкретного продукта
     */
    public function show(int $id)
    {
        $this->shareCommonData(); // вызываем один раз

        // Сначала ищем товар, проверяем существование
        $product = $this->service->find($id);
        if (!$product) {
            abort(404, 'Товар не найден');
        }

        // Затем проверяем условия через отдельный запрос
        $productWithConditions = Product::with(['seller.legalDetails', 'prices', 'images'])
            ->where('id', $id) // уточняем, что ищем именно этот товар
            ->whereHas('seller', function ($sellerQuery) {
                $sellerQuery->whereHas('legalDetails')
                    ->whereHas('contacts', function ($contractQuery) {
                        $contractQuery->where('status', true)
                            ->where('signed_status', true);
                    });
            })
            ->whereHas('prices', function ($priceQuery) {
                $priceQuery->byPriceType('Розничная цена');
            })
            ->whereHas('images', function ($imageQuery) {
                $imageQuery->where('mainImage', true);
            })
            ->first();

        if (!$productWithConditions) {
            abort(404, 'Товар не соответствует условиям отображения');
        }

        $title = 'Купить '.$productWithConditions->getProductName()." с доставкой по России | Брауни Арт — маркетплейс качественных товаров с доставкой по России";
        $description = substr(
            strip_tags($productWithConditions->getProductDescription()),
            0,
            300
        );

        $productSchema = $this->generateProductSchema($productWithConditions);
        return view('index', ['view' => 'products.product', 'title'=>$title, 'meta_description'=>$description, 'productSchema'=>$productSchema, 'product' => compact('productWithConditions')]);
    }

    /**
     * Список всех продуктов
     */
    public function index(Request $request)
    {
        $this->shareCommonData(); // вызываем один раз

        $filters = [
            'category' => $request->input('category'),
            'price_type' => $request->input('price_type', 'Розничная цена'),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'search' => $request->input('search'),
            'article' => $request->input('article'),
        ];

        // Валидация цен (как ранее)
        $minPrice = is_numeric($filters['min_price']) ? (float)$filters['min_price'] : null;
        $maxPrice = is_numeric($filters['max_price']) ? (float)$filters['max_price'] : null;

        if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
            [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
        }

        $filters['min_price'] = $minPrice;
        $filters['max_price'] = $maxPrice;

        // Получаем продукты
        $perPage = $request->input('perPage', 16);
        $validPerPage = in_array($perPage, [8, 12, 16, 20, 24]) ? $perPage : 16;

        // Применяем фильтр условий ДО вызова сервиса
        $products = Product::query()
            ->withActiveSellerAndRetailPriceAndMainImage()
            ->filter($filters)
            ->paginate($validPerPage);

        // Категории и типы цен для фильтров
        $categories = Product::select('productGroupId')
            ->distinct()
            ->orderBy('productGroupId')
            ->pluck('productGroupId');
        $FilterGroup = ProductGroup::where('id', $request->input('category'))->first();
        $priceTypes = ['Розничная цена', 'Оптовая цена'];

        // --- НАЧАЛО ДОБАВЛЕННОГО КОДА ДЛЯ SEO ---

        // Формируем title с учётом фильтров
        $titleParts = ['Товары'];

        if (!empty($filters['search'])) {
            $titleParts[] = 'по запросу «' . $filters['search'] . '»';
        }

        if (!empty($filters['category']) && $FilterGroup) {
            $titleParts[] = 'в категории «' . $FilterGroup->name . '»';
        }

        $title = implode(' ', $titleParts) . ' | Брауни Арт — маркетплейс качественных товаров с доставкой по России';

        // Формируем description с учётом фильтров
        $descriptionParts = ['Широкий выбор товаров'];

        if (!empty($filters['search'])) {
            $descriptionParts[] = 'по запросу «' . $filters['search'] . '»';
        }

        if (!empty($filters['category']) && $FilterGroup) {
            $descriptionParts[] = 'в категории «' . $FilterGroup->name . '»';
        }

        $descriptionParts[] = 'Быстрая доставка по России. Гарантия качества. Актуальные цены.';
        $description = implode(' ', $descriptionParts);

        // --- КОНЕЦ ДОБАВЛЕННОГО КОДА ДЛЯ SEO ---

        return view('index', [
            'view' => 'products.products',
            'products' => compact('products', 'filters', 'categories', 'priceTypes', 'perPage'),
            'FilterGroup' => $FilterGroup,
            'title' => $title,
            'meta_description' => $description
        ]);
    }


    /**
     * Генерирует микроразметку Schema.org для товара (JSON-LD)
     * @param Product $product
     * @return string JSON-строка с микроразметкой
     */
    private function generateProductSchema(Product $product): string
    {
        $schema = [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $product->getProductName(),
            'description' => substr(
                strip_tags($product->getProductDescription()),
                0,
                300
            ),
            'image' => [],
            'brand' => [
                '@type' => 'Brand',
                'name' => $product->brand ?? 'Не указан'
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => url('/products/' . $product->getProductId()),
                'priceCurrency' => 'RUB',
                'availability' => 'https://schema.org/InStock',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => $product->getSeller()->name ?? 'Не указан',
                    'legalName' => $product->getSeller()->legalDetail()->legal_name ?? null,
                    'address' => [
                        '@type' => 'PostalAddress',
                        'addressCountry' => 'RU'
                    ]
                ]
            ]
        ];

        // Добавляем изображения (главное — первым)
        $mainImage = $product->getMainImage();
        if ($mainImage) {
            $schema['image'][] = url('https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/'.$mainImage);
        }
        foreach ($product->getProductImages() as $image) {
            if (!$image->mainImage) {
                $schema['image'][] = url('https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/'.$image->url);
            }
        }

        // Добавляем цену (розничную)
        $retailPrice = $product->prices->firstWhere('price_type', 'Розничная цена');
        if ($retailPrice) {
            $schema['offers']['price'] = number_format($retailPrice->amount, 2, '.', '');
        } else {
            $schema['offers']['price'] = 0;
        }

        return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

}
