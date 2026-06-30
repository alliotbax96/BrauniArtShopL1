<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\BaseController;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cookie;

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
    public function show(int $id, Request $request)
    {
        $this->shareCommonData($request); // вызываем один раз
        Cookie::queue('ShopMode', 1);

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
        return view('index', ['view' => 'products.product', 'shopMode' => 1, 'title'=>$title, 'meta_description'=>$description, 'productSchema'=>$productSchema, 'product' => compact('productWithConditions')]);
    }

    /**
     * Список всех продуктов
     */
    public function index(Request $request)
    {
        $this->shareCommonData($request); // вызываем один раз
        Cookie::queue('ShopMode', 1);

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
        $perPage = $request->input('perPage', 18);
        $validPerPage = in_array($perPage, [9, 12, 18, 21, 24]) ? $perPage : 18;

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
            'shopMode' => 1,
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


    /**
     * API: Получение списка продуктов (JSON)
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $filters = [
            'category' => $request->input('category'),
            'price_type' => $request->input('price_type', 'Розничная цена'),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'search' => $request->input('search'),
            'article' => $request->input('article'),
        ];

        // Валидация цен
        $minPrice = is_numeric($filters['min_price']) ? (float)$filters['min_price'] : null;
        $maxPrice = is_numeric($filters['max_price']) ? (float)$filters['max_price'] : null;

        if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
            [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
        }

        $filters['min_price'] = $minPrice;
        $filters['max_price'] = $maxPrice;

        $perPage = $request->input('per_page', 20);
        $validPerPage = in_array($perPage, [10, 20, 30, 50]) ? $perPage : 20;

        $products = Product::query()
            ->withActiveSellerAndRetailPriceAndMainImage()
            ->filter($filters)
            ->with(['seller', 'images', 'prices'])
            ->paginate($validPerPage);

        return response()->json([
            'success' => true,
            'data' => $products->map(function ($product) {
                // Получаем все изображения продукта
                $allImages = $product->images->map(function ($image) use ($product) {
                    return [
                        'url' => $image->url,
                        'isMain' => $image->main
                    ];
                })->toArray();

                return [
                    'id' => $product->getProductId(),
                    'name' => $product->getProductName(),
                    'description' => $product->getProductDescription(),
                    'price' => $product->getProductPrice('Розничная цена'), // используем розничную цену как основную
                    'quantity' => $product->getProductQuantity(),
                    'images' => $allImages,
                    'rating' => $product->getProductEstimation() ?? 0.0 // добавляем рейтинг, если есть, иначе 0.0
                ];
            }),
            'meta' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage()
            ]
        ]);
    }


    /**
     * API: Получение детальной информации о продукте
     */
    public function apiShow(int $id): JsonResponse
    {
        $product = Product::with(['seller.legalDetails', 'prices', 'images', 'reviews.user'])
            ->where('id', $id)
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

        if (!$product) {
            return response()->json([
                'success' => false,
                'error' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $product->getProductId(),
                'name' => $product->getProductName(),
                'description' => $product->getProductDescription(),
                'price' => $product->getProductPrice(),
                'retail_price' => $product->getProductPrice('Розничная цена'),
                'wholesale_price' => $product->getProductPrice('Оптовая цена'),
                'quantity' => $product->getProductQuantity(),
                'main_image' => $product->getMainImage(),
                'images' => $product->getProductImages()->map(function ($image) {
                    return [
                        'url' => $image->url,
                        'main' => $image->mainImage
                    ];
                }),
                'reviews' => $product->getProductReviews()->map(function ($review) {
                    return [
                        'user_name' => $review->user->name,
                        'estimation' => $review->estimation,
                        'comment' => $review->comment
                    ];
                }),
                'average_rating' => $product->getProductEstimation(),
                'seller' => [
                    'id' => $product->getSellerId(),
                    'name' => $product->getSeller()->name ?? null,
                    'legal_details' => $product->getSeller()->legalDetails ?? null
                ],
                'group' => [
                    'id' => $product->productGroupId,
                    'name' => $product->getProductGroup()->name ?? null
                ]
            ]
        ]);
    }

    /**
     * API: Получение категорий продуктов
     */
    public function apiCategories(): JsonResponse
    {
        $categories = ProductGroup::rootGroups()
            ->with('childrenRecursive')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories->map(function ($group) {
                return $this->formatCategoryTree($group);
            })
        ]);
    }

    /**
     * Рекурсивное форматирование дерева категорий
     */
    private function formatCategoryTree(ProductGroup $group): array
    {
        return [
            'id' => $group->id,
            'name' => $group->name,
            'image' => $group->image,
            'parent_id' => $group->parent_id,
            'children' => $group->childrenRecursive->map(function ($child) {
                return $this->formatCategoryTree($child);
            })->toArray()
        ];
    }

}
