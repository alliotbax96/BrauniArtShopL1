<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository
{
    public function find(int $id): ?Product
    {
        return Product::find($id);
    }

    public function all(int $perPage = 12): LengthAwarePaginator
    {
        return Product::paginate($perPage);
    }

    // Новый метод — получение всех товаров с условиями
//    public function allWithConditions(int $perPage = 12): LengthAwarePaginator
//    {
//        return Product::query()
//            ->withActiveSellerAndRetailPriceAndMainImage()
//            ->paginate($perPage);
//    }

    public function allWithConditions(int $perPage = 12): LengthAwarePaginator
    {
        $query = Product::query()
            ->with([
                'seller',
                'seller.sellerPvz',
                'seller.contacts',
                'prices',
                'images',
                'quantity',
            ]);

        // Применяем scope-фильтр (он делает whereHas внутри себя)
        $query->withActiveSellerAndRetailPriceAndMainImage();

        // Фильтр: остаток > 0
        $query->whereHas('quantity', function ($q) {
            $q->where('quantity', '>', 0);
        });

        // Фильтр: у продавца есть контракт со статусами
        $query->whereHas('seller.contacts', function ($q) {
            $q->where('status', true)
                ->where('signed_status', true);
        });

        // Фильтр: у продавца есть PVZ
        $query->whereHas('seller.sellerPvz');

        $query->orderByDesc('created_at');

        return $query->paginate($perPage);
    }

    public function filterProducts(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        // Здесь остаётся существующая логика фильтрации
        // (можно добавить вызов ->withActiveSellerAndRetailPriceAndMainImage(),
        // если нужно применять условия и к отфильтрованным данным)
        return Product::filter($filters)->paginate($perPage);
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
