<?php

namespace App\Http\Controllers\Api;

use App\Models\ProductGroup;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends ApiBaseController
{
    /**
     * Получение дерева категорий
     */
    public function index(Request $request)
    {
        $parentId = $request->parent_id;

        $categories = ProductGroup::byParentId($parentId)
            ->with(['childrenRecursive' => function($query) {
                $query->withCount(['products' => function($q) {
                    $q->withActiveSellerAndRetailPriceAndMainImage();
                }]);
            }])
            ->withCount(['products' => function($q) {
                $q->withActiveSellerAndRetailPriceAndMainImage();
            }])
            ->get();

        return $this->successResponse(
            $categories->map(fn($cat) => $this->formatCategory($cat))
        );
    }

    /**
     * Получение конкретной категории
     */
    public function show(int $id)
    {
        $category = ProductGroup::with([
            'childrenRecursive',
            'parent'
        ])
            ->withCount(['products' => function($q) {
                $q->withActiveSellerAndRetailPriceAndMainImage();
            }])
            ->findOrFail($id);

        return $this->successResponse($this->formatCategoryDetail($category));
    }

    /**
     * Получение дочерних категорий
     */
    public function children(int $id)
    {
        $children = ProductGroup::where('parent_id', $id)
            ->withCount(['products' => function($q) {
                $q->withActiveSellerAndRetailPriceAndMainImage();
            }])
            ->get();

        return $this->successResponse(
            $children->map(fn($child) => $this->formatCategory($child))
        );
    }

    /**
     * Форматирование категории для дерева
     */
    private function formatCategory(ProductGroup $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'image' => $category->image ? $this->getImageUrl($category->image) : null,
            'parent_id' => $category->parent_id,
            'products_count' => $category->products_count ?? 0,
            'children' => $category->childrenRecursive
                ? $category->childrenRecursive->map(fn($c) => $this->formatCategory($c))
                : []
        ];
    }

    /**
     * Форматирование детальной информации о категории
     */
    private function formatCategoryDetail(ProductGroup $category): array
    {
        $data = $this->formatCategory($category);

        $data['parent'] = $category->parent ? [
            'id' => $category->parent->id,
            'name' => $category->parent->name
        ] : null;

        // Хлебные крошки
        $data['breadcrumbs'] = $this->getBreadcrumbs($category);

        return $data;
    }

    /**
     * Получение хлебных крошек
     */
    private function getBreadcrumbs(ProductGroup $category): array
    {
        $breadcrumbs = [];
        $current = $category;

        while ($current) {
            array_unshift($breadcrumbs, [
                'id' => $current->id,
                'name' => $current->name
            ]);
            $current = $current->parent;
        }

        return $breadcrumbs;
    }

    private function getImageUrl(?string $url): ?string
    {
        if (!$url) return null;
        return 'https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/' . $url;
    }
}
