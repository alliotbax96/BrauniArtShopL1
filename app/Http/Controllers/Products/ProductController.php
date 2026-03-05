<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\BaseController;
use App\Models\Product;
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
        $product = $this->service->find($id);
        if (!$product) {
            abort(404, 'Товар не найден');
        }
        $scripts[] = "/assets/scripts/pages/products/product.js";
        return view('index', ['view' => 'products.product', 'product' => compact('product'), 'scripts' => $scripts]);
    }

    /**
     * Список всех продуктов
     */
    public function index(Request $request)
    {
        $this->shareCommonData(); // вызываем один раз

        $filters = [
            'category' => $request->input('category'),
            'price_type' => $request->input('price_type', 'Розничная цена'), // Новый параметр
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

        $products = $this->service->getFilteredProducts($filters, $validPerPage);

        // Категории и типы цен для фильтров
        $categories = Product::select('productGroupId')
            ->distinct()
            ->orderBy('productGroupId')
            ->pluck('productGroupId');

        $priceTypes = ['Розничная цена', 'Оптовая цена'];
        $scripts[] = "/assets/scripts/pages/products/products.js";
        return view('index',['view' => 'products.products', 'scripts' => $scripts, 'products' => compact('products', 'filters', 'categories', 'priceTypes', 'perPage')]);
    }

    /**
     * Форма создания нового продукта
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Сохранение нового продукта
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'productName' => 'required|string|max:255',
            'productPrice' => 'required|numeric|min:0',
//            'productQuantity' => 'required|integer|min:0',
            'productDescription' => 'nullable|string',
            'productGroupId' => 'required|string|max:100',
            'productSeller' => 'required|integer|exists:sellers,id',
            'productCode' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $product = $this->service->create($validator->validated());

        return redirect()->route('products.show', $product->getProductId())
            ->with('success', 'Товар успешно создан');
    }

    /**
     * Форма редактирования продукта
     */
    public function edit(int $id)
    {
        $product = $this->service->find($id);
        if (!$product) {
            abort(404, 'Товар не найден');
        }

        return view('products.edit', compact('product'));
    }

    /**
     * Обновление продукта
     */
    public function update(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'productName' => 'required|string|max:255',
            'productPrice' => 'required|numeric|min:0',
//            'productQuantity' => 'required|integer|min:0',
            'productDescription' => 'nullable|string',
            'productGroupId' => 'required|string|max:100',
            'productSeller' => 'required|integer|exists:sellers,id',
            'productCode' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $success = $this->service->update($id, $validator->validated());

        if (!$success) {
            abort(404, 'Товар не найден');
        }

        return redirect()->route('products.show', $id)
            ->with('success', 'Товар успешно обновлён');
    }

    /**
     * Удаление продукта
     */
    public function destroy(int $id)
    {
        $success = $this->service->delete($id);

        if (!$success) {
            abort(404, 'Товар не найден');
        }

        return redirect()->route('products.index')
            ->with('success', 'Товар удалён');
    }
}
