<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\ProductGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends ApiBaseController
{
    /**
     * Список товаров с фильтрацией
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'nullable|integer|exists:productGroups,id',
            'search' => 'nullable|string|max:255',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'sort' => 'nullable|string|in:price_asc,price_desc,newest,popular',
            'per_page' => 'nullable|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $filters = [
            'category' => $request->category,
            'min_price' => $request->min_price,
            'max_price' => $request->max_price,
            'search' => $request->search,
            'article' => $request->article
        ];

        $perPage = $request->per_page ?? 20;

        $query = Product::query()
            ->withActiveSellerAndRetailPriceAndMainImage()
            ->filter($filters)
            ->orderBy('created_at', 'desc')
            ->with([
                'seller:id,name',
                'images',
                'prices',
                'quantity'
            ]);

        // Сортировка
        switch ($request->sort) {
            case 'price_asc':
                $query->whereHas('prices', function($q) {
                    $q->byPriceType('Розничная цена')->orderBy('price', 'asc');
                });
                break;
            case 'price_desc':
                $query->whereHas('prices', function($q) {
                    $q->byPriceType('Розничная цена')->orderBy('price', 'desc');
                });
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'popular':
                $query->withAvg('reviews', 'estimation')
                    ->orderByDesc('reviews_avg_estimation');
                break;
            default:
                $query->orderBy('id', 'desc');
        }

        $products = $query->paginate($perPage);

        $formattedProducts = $products->map(function ($product) {
            return $this->formatProductListItem($product);
        });

        return $this->paginatedResponse($products->setCollection($formattedProducts));
    }

    /**
     * Детальная информация о товаре
     */
    public function show(int $id)
    {
        $product = Product::with([
            'seller.legalDetails',
            'prices',
            'images',
            'quantity.warehouse',
            'reviews' => function($q) {
                $q->with('user:id,name')->latest()->limit(10);
            },
            'ProductGroup'
        ])
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
            return $this->errorResponse('Товар не найден', 404);
        }

        return $this->successResponse($this->formatProductDetail($product));
    }

    /**
     * Форматирование товара для списка
     */
    private function formatProductListItem(Product $product): array
    {
        $mainImage = $product->images->where('mainImage', true)->first();

        return [
            'id' => $product->id,
            'name' => $product->productName,
            'code' => $product->productCode,
            'price' => (float) ($product->prices->where('priceType', 'Розничная цена')->first()->price ?? 0),
            'old_price' => null, // Можно добавить логику скидок
            'main_image' => $mainImage ? $this->getImageUrl($mainImage->url) : null,
            'rating' => round($product->reviews->avg('estimation') ?? 0, 1),
            'reviews_count' => $product->reviews->count(),
            'in_stock' => $product->getProductQuantity() > 0,
            'is_new' => $product->created_at >= now()->subWeek(),
            'seller_name' => $product->seller->name ?? null,
            'category_id' => $product->productGroupId
        ];
    }

    /**
     * Форматирование детальной информации о товаре
     */
    private function formatProductDetail(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->productName,
            'code' => $product->productCode,
            'description' => $product->productDescription,
            'weight' => $product->productWeight,
            'prices' => [
                'retail' => (float) ($product->prices->where('priceType', 'Розничная цена')->first()->price ?? 0),
                'wholesale' => (float) ($product->prices->where('priceType', 'Оптовая цена')->first()->price ?? 0),
            ],
            'images' => $product->images->map(function($image) {
                return [
                    'id' => $image->id,
                    'url' => $this->getImageUrl($image->url),
                    'is_main' => (bool) $image->mainImage,
                    'order' => $image->order
                ];
            }),
            'quantity' => $product->getProductQuantity(),
            'rating' => round($product->reviews->avg('estimation') ?? 0, 1),
            'reviews_count' => $product->reviews->count(),
            'reviews' => $product->reviews->map(function($review) {
                return [
                    'id' => $review->id,
                    'user_name' => $review->user->name ?? 'Аноним',
                    'rating' => $review->estimation,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at->format('d.m.Y')
                ];
            }),
            'seller' => [
                'id' => $product->seller->id ?? null,
                'name' => $product->seller->name ?? null,
                'rating' => 4.5 // Можно добавить рейтинг продавца
            ],
            'category' => [
                'id' => $product->productGroupId,
                'name' => $product->ProductGroup->first()->name ?? null
            ],
            'is_new' => $product->created_at >= now()->subWeek(),
            'in_stock' => $product->getProductQuantity() > 0
        ];
    }

    /**
     * Получение полного URL изображения
     */
    private function getImageUrl(?string $url): ?string
    {
        if (!$url) return null;

        return 'https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/' . $url;
    }
}
