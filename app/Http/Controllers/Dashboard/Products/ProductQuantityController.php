<?php

namespace App\Http\Controllers\Dashboard\Products;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\ProductQuantity;

class ProductQuantityController extends BaseController
{
    public function index() {
        if(!Auth::user()->groupInfo()->hasPermission('view_products')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        $this->shareCommonData(); // вызываем один раз

        return view('dashboard.index',[
            'View' => 'dashboard.products.quantity',
            'title'=>'Управление остатками | Единая система BaID',
            'PageName'=>'Ассортимент',
            'InPageName'=>'Управление остатками',
        ]);
    }

    public function ajax(Request $request)
    {
        if (!Auth::user()->groupInfo()->hasPermission('view_products')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }

        $sellerId = Auth::user()->getSellerId();

        try {
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $draw = $request->input('draw', 1);
            $searchValue = $request->input('search.value');

            $query = Product::query();

            if (!Auth::user()->isAdmin()) {
                $query->where('productSeller', $sellerId);
            }

            // Обработка поиска
            if (!empty($searchValue)) {
                $query->where('productName', 'like', '%' . $searchValue . '%')
                    ->orWhere('productCode', 'like', '%' . $searchValue . '%');
            }

            // Подсчёт общего количества записей
            $totalRecords = $query->count();

            // Подсчёт отфильтрованных записей
            $filteredRecords = $query->clone()->count();

            // Получаем данные с пагинацией
            $products = $query
                ->skip($start)
                ->take($length)
                ->get();

            // Форматируем данные для DataTable
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

//                    $nameHtml = '
//                    <div class="hstack gap-4">
//                <div class="avatar-image border-0">' . $imageHtml . '</div>
//                <div>
//                    <a href="/seller/products/' . $product->id . '" class="text-truncate-2-line">' .
//                        htmlspecialchars($product->getProductName() ?? 'Не указано') . '</a>' .
//                        $warningHtml . '</div></div>';

                    $nameHtml = '
                            <div class="d-flex align-items-center gap-3">
                                <div class="table-product-img">
                                    ' . ($hasImage
                               ? '<img src="https://s3.ru1.storage.beget.cloud/d5833d93d74c-brauniartfiles/' . htmlspecialchars($imagePath) . '" alt="">'
                               : '<div class="table-product-img-placeholder"><i class="feather-image"></i></div>') . '
                               </div>
                               <div>
                                   <a href="/seller/products/' . $product->id . '" class="table-product-name">' .
                                                   htmlspecialchars($product->getProductName() ?? 'Не указано') . '</a>
                                   ' . $warningHtml . '
                               </div>
                            </div>';

                    $quantityHtml = '
<input form="stocks" type="number" name="quantity[' . $product->id . ']"
       class="form-control form-control-sm stock-quantity-input"
       value="' . $product->getProductQuantity() . '" min="0">';

                    $quantityHtml = '
                <input form="stocks" type="number" name="" class="form-control form-control-sm" value="' . $product->getProductQuantity() . '">
                ';

                    $formattedProducts[] = [
                        'name' => $nameHtml,
                        'article' => htmlspecialchars($product->productCode ?? ''),
                        'quantity' => $quantityHtml,
                        'DT_RowAttr' => [
                            'data-product-id' => $product->id
                        ],
                    ];
                } catch (\Exception $e) {
                    \Log::error('Error formatting product data: ' . $e->getMessage());
                    // Заглушка при ошибке
                    $formattedProducts[] = [
                        'name' => 'Ошибка данных',
                        'article' => '',
                        'quantity' => '<input form="stocks" type="number" name="" class="form-control form-control-sm" value="0">'
                    ];
                }
            }

            return response()->json([
                'draw' => (int)$draw,
                'recordsTotal' => (int)$totalRecords,
                'recordsFiltered' => (int)$filteredRecords,
                'data' => $formattedProducts
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

    public function updateQuantity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Неверные данные: ' . $validator->errors()->first()
            ], 422);
        }

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');
        $warehouseId = 1; // Склад по умолчанию

        try {
            // Ищем существующую запись
            $productQuantity = ProductQuantity::where('product_id', $productId)
                ->where('warehouse_id', $warehouseId)
                ->first();

            if ($productQuantity) {
                // Обновляем существующую запись
                $productQuantity->quantity = $quantity;
                $productQuantity->save();
                $action = 'updated';
            } else {
                // Создаём новую запись
                ProductQuantity::create([
                    'product_id' => $productId,
                    'warehouse_id' => $warehouseId,
                    'quantity' => $quantity,
                    'product_type' => 'App\\Models\\ProductQuantity' // или другое значение по умолчанию
                ]);
                $action = 'created';
            }

            return response()->json([
                'success' => true,
                'message' => 'Остаток успешно ' . ($action === 'updated' ? 'обновлён' : 'создан'),
                'action' => $action
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error updating quantity: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Ошибка базы данных при обновлении остатка'
            ], 500);
        } catch (\Exception $e) {
            \Log::error('Error updating quantity: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Произошла ошибка при обновлении остатка'
            ], 500);
        }
    }


}
