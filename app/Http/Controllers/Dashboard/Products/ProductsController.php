<?php

namespace App\Http\Controllers\Dashboard\Products;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\Seller;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\ProductPrice;
use Illuminate\Support\Facades\Auth;

class ProductsController extends BaseController
{

    protected ProductService $service;
    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }
    public function index(Request $request) {
        if(!Auth::user()->groupInfo()->hasPermission('view_products')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        $this->shareCommonData(); // вызываем один раз

        return view('dashboard.index',[
            'View' => 'dashboard.products.index',
            'title'=>'Управление товарами | Единая система BaID',
            'PageName'=>'Ассортимент',
            'InPageName'=>'Управление товарами',
            'CreateObject' => '/seller/products/create'
        ]);
    }
    public function ajax(Request $request)
    {
        if(!Auth::user()->groupInfo()->hasPermission('view_products')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        $sellerId = Auth::user()->getSellerId();
        try {

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

            // Параметры пагинации DataTable
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $draw = $request->input('draw', 1);

            // Основной запрос для данных
            $query = Product::query();

            // Применяем фильтры
            if (!empty($filters['category'])) {
                $query->where('productGroupId', $filters['category']);
            }
            if (!empty($filters['search'])) {
                $query->where('name', 'like', '%' . $filters['search'] . '%');
            }
            if (!empty($filters['article'])) {
                $query->where('article', 'like', '%' . $filters['article'] . '%');
            }

            // Выбор колонки цены в зависимости от типа
            $priceColumn = $filters['price_type'] === 'Оптовая цена' ? 'wholesale_price' : 'retail_price';
            if ($filters['min_price'] !== null) {
                $query->where($priceColumn, '>=', $filters['min_price']);
            }
            if ($filters['max_price'] !== null) {
                $query->where($priceColumn, '<=', $filters['max_price']);
            }

            // Подсчёт общего количества записей
            if(!Auth::user()->isAdmin()) {
                $query->where('productSeller', $sellerId);
            }
            $totalRecords = $query->count();
            // Получаем данные с пагинацией
            $productsQuery = clone $query;
            $products = $productsQuery
                ->skip($start)
                ->take($length)
                ->get();

            // Форматируем данные для DataTable, включая HTML для столбца «Наименование»
            $formattedProducts = [];
            foreach ($products as $product) {
                try {
                    // Формируем HTML для столбца «Наименование»
                    $imagePath = $product->getMainImage();
                    $hasImage = !empty($imagePath);
                    $imageHtml = $hasImage
                        ? '<img src="https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/' . htmlspecialchars($imagePath) . '" alt="" class="img-fluid" style="width: 40px; height: 40px; object-fit: cover;">'
                        : '';

                    $warningHtml = !$hasImage
                        ? '<p class="badge bg-soft-danger text-danger">Товар скрыт с витрины! Загрузите изображение!</p>'
                        : '';

                    $nameHtml = '
                                  <div class="hstack gap-4">
                                   <div class="avatar-image border-0">' . $imageHtml . '</div>
                                  <div>
                                  <a href="/seller/products/' . $product->id . '" class="text-truncate-2-line">' .
                                      htmlspecialchars($product->getProductName() ?? 'Не указано') . '</a>' .
                                      $warningHtml . '
                                      <div class="project-list-action fs-12 d-flex align-items-center gap-3 mt-2">
                                  <a href="/seller/products/' . $product->id . '">Изменить</a>
                                  <span class="vr text-muted"></span>
                                  <a href="javascript:void(0);" class="text-danger delete_product" data-id="' . $product->id . '">
                                  Удалить
                                  </a>
                                  </div>
                                  </div>
                                  </div>';

                    $formattedProducts[] = [
                        'id' => $product->id,
                        'name' => $nameHtml,
                        'article' => htmlspecialchars($product->productCode ?? ''),
                        'category' => htmlspecialchars(($product->getProductGroup()[0]->name ?? 'Не указана')),
                        'price' => ($product->getProductPrice() ?? 0) . ' руб.',
                    ];
                } catch (\Exception $e) {
                    \Log::error('Error formatting product data: ' . $e->getMessage());
                    // Заглушка при ошибке
                    $formattedProducts[] = [
                        'id' => $product->id,
                        'name' => 'Ошибка данных',
                        'article' => '',
                        'category' => 'Не указана',
                        'price' => '0 руб.',
                    ];
                }
            }

            // Категории для фильтров
            $categories = Product::select('productGroupId')
                ->distinct()
                ->orderBy('productGroupId')
                ->pluck('productGroupId')
                ->toArray();

            $priceTypes = ['Розничная цена', 'Оптовая цена'];

            return response()->json([
                'draw' => (int)$draw,
                'recordsTotal' => (int)$totalRecords,
                'recordsFiltered' => (int)$totalRecords,
                'data' => $formattedProducts,
                'filters' => $filters,
                'categories' => $categories,
                'priceTypes' => $priceTypes
            ]);

        } catch (\Exception $e) {
            \Log::error('DataTable error: ' . $e->getMessage());

            return response()->json([
                'draw' => (int)($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Произошла ошибка при загрузке данных'
            ], 500);
        }
    }
    public function show(int $id){
        if(!Auth::user()->groupInfo()->hasPermission('edit_products')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        $this->shareCommonData();
        $sellerId = Auth::user()->getSellerId();
        if(!Auth::user()->isAdmin()) {
            $product = $this->service->findSeller($id, $sellerId);
        } else {
            $product = $this->service->find($id);
        }
        if (!$product) {
            abort(404, 'Товар не найден');
        }

        $productGroups = ProductGroup::where('parent_id', null)->get();
        if(Auth::user()->isAdmin()) {
            $sellers = Seller::all();
        }
        return view('dashboard.index', ['View' => 'dashboard.products.show', 'sellers'=>$sellers ? $sellers : '', 'title'=> $product->getProductName().' | Единая система BaID', 'PageName'=>'Управление товарами', 'InPageName'=>$product->getProductName(), 'product' => $product, 'productGroups' => $productGroups]);
    }
    public function create(){
        if(!Auth::user()->groupInfo()->hasPermission('create_products')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        $this->shareCommonData();
        $productGroups = ProductGroup::where('parent_id', null)->get();
        if(Auth::user()->isAdmin()) {
          $sellers = Seller::all();
        }
        return view('dashboard.index', ['View' => 'dashboard.products.create', 'sellers'=>$sellers ? $sellers : '', 'title'=> 'Создание товара | Единая система BaID', 'PageName'=>'Управление товарами', 'InPageName'=>'Создание товара', 'productGroups' => $productGroups]);
    }
    public function store(Request $request)
    {
        if(!Auth::user()->groupInfo()->hasPermission('create_products')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }

        $validator = Validator::make($request->all(), [
            'productName' => 'required|string|max:255',
            'prices' => 'required|array',
            'prices.*.price' => 'required|numeric',
            'prices.*.priceType' => 'required|string|max:255',
            'productDescription' => 'required|string',
            'productGroupId' => 'required|string|max:100',
            'productSeller' => 'required|integer|exists:sellers,id',
            'productCode' => 'required|string|max:50|unique:products,productCode',
            'productWeight' => 'required|numeric|min:0',
            'images' => 'nullable|array',
            'images.*' => 'string|regex:/^uploads\/.+$/',
        ], [
            // Сообщения для productName
            'productName.required' => 'Название товара обязательно для заполнения',
            'productName.string' => 'Название товара должно быть текстом',
            'productName.max' => 'Название товара не может превышать 255 символов',

            // Сообщения для productPrice
            'prices.required' => 'Цена товара обязательна для заполнения',
            'prices.*.price.numeric' => 'Цена должна быть числом',
            'prices.*.price.min' => 'Цена не может быть отрицательной',

            // Сообщения для productGroupId
            'productGroupId.required' => 'Группа товара обязательна для выбора',
            'productGroupId.string' => 'Группа товара должна быть текстом',
            'productGroupId.max' => 'Группа товара не может превышать 100 символов',

            // Сообщения для productSeller
            'productSeller.required' => 'Продавец товара обязателен для выбора',
            'productSeller.integer' => 'ID продавца должен быть целым числом',
            'productSeller.exists' => 'Выбранный продавец не существует',

            // Сообщения для productCode
            'productCode.string' => 'Код товара должен быть текстом',
            'productCode.max' => 'Код товара не может превышать 50 символов',
            'productCode.unique' => 'Такой код товара уже используется другим товаром',

            // Сообщения для images
            'images.array' => 'Изображения должны быть представлены в виде массива',
            'images.*.string' => 'Каждый элемент массива изображений должен быть строкой',
            'images.*.regex' => 'Некорректный формат пути к изображению. Должен начинаться с "uploads/"',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Ошибка валидации данных',
                'errors' => $validator->errors()->toArray(),
                'detailed_errors' => collect($validator->errors()->messages())->mapWithKeys(function ($messages, $field) {
                    return [
                        $field => [
                            'field' => $field,
                            'messages' => $messages,
                            'human_readable' => $this->getHumanReadableFieldName($field)
                        ]
                    ];
                })->toArray()
            ]);
        }

        DB::beginTransaction();

        try {
            $validatedData = $validator->validated();
            $success = $this->service->create($validatedData);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'error' => 'Товар не найден'
                ]);
            }

            $id = $success->id;

            foreach ($validatedData['prices'] as $price) {
                // СОХРАНЕНИЕ ЦЕНЫ — добавляем прямо в контроллере
                ProductPrice::create([
                    'productId' => $id,
                    'price' => $price['price'],
                    'priceType' => $price['priceType'] // фиксированное значение
                ]);
            }
            // Сохранение изображений (оставляем как было)
            if ($request->has('images')) {
                $this->updateProductImages($id, $request->input('images'));
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Товар успешно создан',
                'redirect_url' => route('seller.products.show', $id),
                'data' => $request->input('ProductDescription')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Ошибка при создании товара: ' . $e->getMessage()
            ]);
        }
    }
    public function update(Request $request, int $id)
    {
        if(!Auth::user()->groupInfo()->hasPermission('edit_products')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        $validator = Validator::make($request->all(), [
            'productName' => 'required|string|max:255',
            'prices' => 'required|array',
            'prices.*.price' => 'required|numeric',
            'prices.*.priceType' => 'required|string|max:255',
            'productDescription' => 'nullable|string',
            'productGroupId' => 'required|string|max:100',
            'productSeller' => 'required|integer|exists:sellers,id',
            'productCode' => 'nullable|string|max:50|unique:products,productCode,' . $id,
            'productWeight' => 'required|numeric|min:0',
            'images' => 'nullable|array',
            'images.*' => 'string|regex:/^uploads\/.+$/',
        ], [
            // Сообщения для productName
            'productName.required' => 'Название товара обязательно для заполнения',
            'productName.string' => 'Название товара должно быть текстом',
            'productName.max' => 'Название товара не может превышать 255 символов',

            // Сообщения для productPrice
            'prices.required' => 'Цена товара обязательна для заполнения',
            'prices.*.price.numeric' => 'Цена должна быть числом',
            'prices.*.price.min' => 'Цена не может быть отрицательной',

            // Сообщения для productGroupId
            'productGroupId.required' => 'Группа товара обязательна для выбора',
            'productGroupId.string' => 'Группа товара должна быть текстом',
            'productGroupId.max' => 'Группа товара не может превышать 100 символов',

            // Сообщения для productSeller
            'productSeller.required' => 'Продавец товара обязателен для выбора',
            'productSeller.integer' => 'ID продавца должен быть целым числом',
            'productSeller.exists' => 'Выбранный продавец не существует',

            // Сообщения для productCode
            'productCode.string' => 'Код товара должен быть текстом',
            'productCode.max' => 'Код товара не может превышать 50 символов',
            'productCode.unique' => 'Такой код товара уже используется другим товаром',

            // Сообщения для images
            'images.array' => 'Изображения должны быть представлены в виде массива',
            'images.*.string' => 'Каждый элемент массива изображений должен быть строкой',
            'images.*.regex' => 'Некорректный формат пути к изображению. Должен начинаться с "uploads/"',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Ошибка валидации данных',
                'errors' => $validator->errors()->toArray(),
                'detailed_errors' => collect($validator->errors()->messages())->mapWithKeys(function ($messages, $field) {
                    return [
                        $field => [
                            'field' => $field,
                            'messages' => $messages,
                            'human_readable' => $this->getHumanReadableFieldName($field)
                        ]
                    ];
                })->toArray()
            ]);
        }

        DB::beginTransaction();

        try {
            $validatedData = $validator->validated();
            $success = $this->service->update($id, $validatedData);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'error' => 'Товар не найден'
                ]);
            }

            foreach ($validatedData['prices'] as $price) {
                // СОХРАНЕНИЕ ЦЕНЫ — добавляем прямо в контроллере
                $productPrice = ProductPrice::where('productId', $id)->where('priceType', $price['priceType'])->first();
                if (!$productPrice) {
                    ProductPrice::create([
                        'productId' => $id,
                        'price' => $price['price'],
                        'priceType' => $price['priceType'] // фиксированное значение
                    ]);
                } else {
                    $productPrice->update([
                        'price' => $price['price']
                    ]);
                }
            }

            if ($request->has('images')) {
                $this->updateProductImages($id, $request->input('images'));
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Товар успешно обновлён',
                'redirect_url' => route('seller.products.show', $id),
                'data'=>$request->input('ProductDescription')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Product update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Ошибка при обновлении товара: ' . $e->getMessage()
            ]);
        }
    }
    private function processProductImages(int $productId, array $imagePaths): void
    {
        foreach ($imagePaths as $index => $tempPath) {
            try {
                $extension = pathinfo($tempPath, PATHINFO_EXTENSION);
                $newFilename = 'uploads/' . $productId . '_' . Str::random(8) . '.' . $extension;

                if (Storage::disk('s3')->exists($tempPath)) {
                    Storage::disk('s3')->move($tempPath, $newFilename);

                    ProductImage::create([
                        'productId' => $productId,
                        'url' => $newFilename,
                        'mainImage' => ($index === 0) // первое изображение — главное
                    ]);
                } else {
                    throw new \Exception("Файл не найден в S3: $tempPath");
                }
            } catch (\Exception $e) {
                \Log::error("Image processing error for product $productId: " . $e->getMessage());
                throw $e;
            }
        }
    }
    private function updateProductImages(int $productId, array $newImagePaths): void
    {
        // Удаляем старые изображения из БД и S3
        $oldImages = ProductImage::where('productId', $productId)->get();
        foreach ($oldImages as $oldImage) {
            if (in_array($oldImage->url, $newImagePaths)) {
                $oldImage->update(['mainImage' => null]);
                continue;
            }
            if (Storage::disk('s3')->exists($oldImage->url)) {
                Storage::disk('s3')->delete($oldImage->url);
            }
            $oldImage->delete();
        }
        // Добавляем новые изображения
        foreach ($newImagePaths as $index => $tempPath) {
            if(ProductImage::where('url', $tempPath)->exists()){
                ProductImage::where('url', $tempPath)->update(['order'=>$index]);
                if($index === 0){
                    ProductImage::where('url', $tempPath)->update(['mainImage'=>1]);
                }
                continue;
            }
            try {
                $extension = pathinfo($tempPath, PATHINFO_EXTENSION);
                $newFilename = 'uploads/' . $productId . '_' . Str::random(8) . '.' . $extension;
                if (Storage::disk('s3')->exists($tempPath)) {
                    Storage::disk('s3')->move($tempPath, $newFilename);
                    ProductImage::create([
                        'productId' => $productId,
                        'url' => $newFilename,
                        'mainImage' => ($index === 0), // первое изображение — главное
                        'order'=> $index
                    ]);
                } else {
                    throw new \Exception("Файл не найден в S3: $tempPath");
                }
            } catch (\Exception $e) {
                \Log::error("Image update error for product $productId: " . $e->getMessage());
                throw $e;
            }
        }
    }

    /**
     * Удаление продукта с изображениями
     */
    public function destroy(int $id)
    {
        if(!Auth::user()->groupInfo()->hasPermission('delete_products')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        DB::beginTransaction();

        try {
            $product = Product::find($id);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'error' => 'Товар не найден',
                    'code' => 404
                ], 404);
            }

            // Удаляем изображения из S3 и записи из БД
            $images = ProductImage::where('productId', $id)->get();
            foreach ($images as $image) {
                if (Storage::disk('s3')->exists($image->url)) {
                    Storage::disk('s3')->delete($image->url);
                }
                $image->delete();
            }

            $success = $this->service->delete($id);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ошибка при удалении товара',
                    'code' => 400
                ], 400);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Товар удалён',
                'data' => [
                    'product_id' => $id
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Product deletion error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Ошибка при удалении товара: ' . $e->getMessage(),
                'code' => 500,
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], 500);
        }
    }

    /**
     * Возвращает читаемое название поля для отображения пользователю
     * @param string $field Техническое имя поля
     * @return string Читаемое название
     */
    private function getHumanReadableFieldName(string $field): string
    {
        $fieldNames = [
            'productName' => 'Название товара',
            'productPrice' => 'Цена',
            'productDescription' => 'Описание',
            'productGroupId' => 'Группа товара',
            'productSeller' => 'Продавец',
            'productCode' => 'Код товара',
            'images' => 'Изображения'
        ];

        return $fieldNames[$field] ?? $field;
    }
}
