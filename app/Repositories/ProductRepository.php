<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository
{
    public function find(int $id): ?Product
    {
        return Product::with([
            'seller',
            'images',
//            'quantity',
//            'parameters'
        ])->find($id);
    }

    public function all(int $perPage = 12): LengthAwarePaginator
    {
        return Product::with([
            'seller',
            'images',
//            'quantity',
//            'parameters'
        ])->paginate($perPage);
    }

    public function filterProducts(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $priceType = $filters['price_type'] ?? 'Розничная цена'; // Тип цены по умолчанию
        $minPrice = $filters['min_price'] ?? null;
        $maxPrice = $filters['max_price'] ?? null;

        return Product::query()
            ->byCategory($filters['category'] ?? null)
            ->byPriceWithType($minPrice, $maxPrice, $priceType)
            ->searchByName($filters['search'] ?? null)
            ->searchByArticle($filters['article'] ?? null)
            ->paginate($perPage);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $product = Product::find($id);
        if (!$product) {
            return false;
        }
        return $product->update($data);
    }

    public function delete(int $id): bool
    {
        $product = Product::find($id);
        if (!$product) {
            return false;
        }
        return $product->delete();
    }
}
