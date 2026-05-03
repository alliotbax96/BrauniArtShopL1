<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = [
        'productName',
        'productQuantity',
        'productDescription',
        'productGroupId',
        'productSeller',
        'productCode',
        'productWeight',
    ];

    // Отношения
    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class, 'productSeller');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'productId')->orderBy('order', 'asc');
    }

    public function ProductPrice(): HasMany
    {
        return $this->hasMany(ProductPrice::class, 'productId');
    }

    public function ProductGroup(): HasMany
    {
        return $this->hasMany(ProductGroup::class, 'id', 'productGroupId');
    }

    public function prices()
    {
        return $this->hasMany(ProductPrice::class, 'productId');
    }

    public function quantity(): HasMany
    {
        return $this->hasMany(ProductQuantity::class, 'product_id');
    }


    /**
     * Фильтр: только товары с активным продавцом, розничной ценой и главной картинкой
     */
    public function scopeWithActiveSellerAndRetailPriceAndMainImage(Builder $query)
    {
        return $query->whereHas('seller', function ($sellerQuery) {
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
            });
    }

    /**
     * Фильтр по категории (включая дочерние категории для родительских)
     */
    public function scopeByCategory(Builder $query, ?int $category)
    {
        if (is_null($category) || empty($category)) {
            return $query;
        }

        // Получаем категорию по ID
        $group = ProductGroup::find($category);

        if (!$group) {
            return $query; // Если категория не найдена, возвращаем исходный запрос
        }

        // Проверяем, является ли категория родительской (parent_id = null)
        if ($group->parent_id === null) {
            // Получаем все ID дочерних категорий рекурсивно
            $childCategoryIds = $this->getChildCategoryIds($group);
            // Добавляем ID самой родительской категории
            $categoryIds = array_merge([$category], $childCategoryIds);
            // Фильтруем товары по всем ID категорий
            return $query->whereIn('productGroupId', $categoryIds);
        } else {
            // Если категория не родительская, фильтруем только по ней
            return $query->where('productGroupId', $category);
        }
    }

    /**
     * Рекурсивно получает все ID дочерних категорий
     * @param ProductGroup $group
     * @return array
     */
    private function getChildCategoryIds(ProductGroup $group): array
    {
        $ids = [];

        foreach ($group->childrenRecursive as $child) {
            $ids[] = $child->id;
            // Рекурсивно добавляем ID дочерних категорий следующего уровня
            $ids = array_merge($ids, $this->getChildCategoryIds($child));
        }

        return $ids;
    }


    /**
     * Фильтр по диапазону цен (без учёта типа цены)
     */
    public function scopeByPriceRange(Builder $query, ?float $minPrice, ?float $maxPrice)
    {
        if ((is_null($minPrice) || $minPrice <= 0) && (is_null($maxPrice) || $maxPrice <= 0)) {
            return $query; // Нет условий по цене — не фильтруем
        }

        return $query->whereHas('prices', function ($subQuery) use ($minPrice, $maxPrice) {
            if (!is_null($minPrice)) {
                $subQuery->where('price', '>=', $minPrice);
            }
            if (!is_null($maxPrice)) {
                $subQuery->where('price', '<=', $maxPrice);
            }
        });
    }

    /**
     * Поиск по названию (частичное совпадение)
     */
    public function scopeSearchByName(Builder $query, ?string $search)
    {
        if (is_null($search) || empty(trim($search))) {
            return $query;
        }

        return $query->where('productName', 'LIKE', '%' . trim($search) . '%');
    }

    /**
     * Поиск по коду/артикулу (точное совпадение)
     */
    public function scopeSearchByArticle(Builder $query, ?string $article)
    {
        if (is_null($article) || empty(trim($article))) {
            return $query;
        }

        return $query->where('productCode', 'LIKE', '%' . trim($article) . '%');
    }

    /**
     * Комбинированный фильтр
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        return $query
            ->byCategory($filters['category'] ?? null)
            ->byPriceRange($filters['min_price'] ?? null, $filters['max_price'] ?? null)
            ->searchByName($filters['search'] ?? null)
            ->searchByArticle($filters['article'] ?? null);
    }


    // Геттеры (можно оставить как методы или использовать атрибуты)
    public function getProductId(): int
    {
        return $this->id;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getProductPrice(string $type = 'Розничная цена'): float
    {
        $prices = ProductPrice::where('productId', $this->id)
            ->byPriceType($type)
            ->get();
        if(!isset($prices[0]->price)){
            return 0;
        }
        return $prices[0]->price;
    }

    public function getProductQuantity(): int
    {
        if(count($this->quantity)>0) {
            return $this->quantity[0]->quantity;
        }
        return 0;
    }

    public function getProductQuantityWarehouse(): Warehouse
    {
        if(count($this->quantity)>0){
            return $this->quantity[0]->getWarehouse();
        }
        return Warehouse::where('id', 1)->first();
    }


    public function getProductImages()
    {
        return $this->images;
    }

    public function getMainImage(): ?string
    {
        foreach ($this->images as $image) {
            if($image->mainImage){
                return $image->url;
            }
        }
        return null;
    }

    public function getProductDescription(): string
    {
        return $this->productDescription;
    }

    public function getProductGroup()
    {
        return $this->productGroup;
    }


    public function getSeller()
    {
        return $this->seller;
    }
    // App\Models\Product.php

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

// Методы для получения статистики (можно оставить как есть или обновить)
    public function getProductReviews()
    {
        return $this->reviews()->with('user')->latest()->get();
    }

    public function getProductEstimation(): float
    {
        return $this->reviews()->avg('estimation') ?? 0;
    }

    public function isNew(): bool
    {
        return $this->created_at >= now()->subWeek();
    }



    public function getProductSellerId(): int
    {
        return $this->productSeller;
    }

}
