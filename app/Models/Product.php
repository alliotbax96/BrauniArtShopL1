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
//        'productQuantity',
        'productDescription',
        'productGroupId',
        'productSeller',
        'productCode'
    ];

    // Отношения
    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class, 'productSeller');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'productId');
    }

    public function ProductPrice(): HasMany
    {
        return $this->hasMany(ProductPrice::class, 'productId');
    }

    public function ProductGroup(): HasMany
    {
        return $this->hasMany(ProductGroup::class, 'productGroupId');
    }

    public function prices()
    {
        return $this->hasMany(ProductPrice::class, 'productId');
    }


//    public function reviews(): HasMany
//    {
//        return $this->hasMany(Review::class, 'id');
//    }

    public function quantity(): HasMany
    {
        return $this->hasMany(ProductQuantity::class, 'product_id');
    }


    /**
     * Фильтр по категории
     */
    public function scopeByCategory(Builder $query, ?int $category)
    {
        if (is_null($category) || empty($category)) {
            return $query;
        }

        return $query->where('productGroupId', $category);
    }

    /**
     * Фильтр по диапазону цен
     */

    public function scopeByPriceWithType(Builder $query, ?float $minPrice, ?float $maxPrice, string $priceType = 'Розничная цена')
    {
        if ((is_null($minPrice) || $minPrice <= 0) && (is_null($maxPrice) || $maxPrice <= 0)) {
            return $query; // Нет условий по цене — не фильтруем
        }

        return $query->whereHas('prices', function ($subQuery) use ($minPrice, $maxPrice, $priceType) {
            $subQuery->byPriceType($priceType);

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
        return $prices[0]->price;
    }

    public function getProductQuantity(): int
    {
        return $this->product_quantity;
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

    public function getProductGroup(): string
    {
        return $this->productGroup;
    }


    public function getSeller()
    {
        return $this->seller;
    }

//    public function getProductReviews()
//    {
//        return $this->reviews->load('user'); // Загружаем связанные данные пользователя
//    }

//    public function getProductEstimation(): float
//    {
//        return $this->reviews()->avg('estimation') ?? 0.0;
//    }


    public function getProductSellerId(): int
    {
        return $this->productSeller;
    }

//    public function getProductWarehouseId(): ?int
//    {
//        return $this->quantity()->first()?->warehouse_id;
//    }
}
