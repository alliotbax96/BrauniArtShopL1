<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    protected ProductRepository $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function find(int $id): ?Product
    {
        return $this->repository->find($id);
    }

    public function getAll(int $perPage = 12): LengthAwarePaginator
    {
        return $this->repository->all($perPage);
    }

    public function getFilteredProducts(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        return $this->repository->filterProducts($filters, $perPage);
    }

    public function create(array $data): Product
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
