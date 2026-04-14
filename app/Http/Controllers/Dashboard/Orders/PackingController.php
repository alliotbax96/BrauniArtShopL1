<?php

namespace App\Http\Controllers\Dashboard\Orders;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\Order;
use App\Models\OrderBox;
use App\Models\BoxItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Auth;

class PackingController extends BaseController
{
    public function savePacking(Request $request)
    {
        if(!Auth::user()->groupInfo()->hasPermission('processing_orders')) {
            abort(403, 'Access denied');
        }

        $validated = $request->validate([
            'data.order_id' => 'required|exists:orders,id',
            'data.boxes' => 'required|array|min:1',
            'data.boxes.*.box_number' => 'required|string|max:50',
            'data.boxes.*.length' => 'nullable|numeric|min:0|max:999.99',
            'data.boxes.*.height' => 'nullable|numeric|min:0|max:999.99',
            'data.boxes.*.width' => 'nullable|numeric|min:0|max:999.99',
            'data.boxes.*.weight' => 'required|numeric|min:0|max:999.99',
            'data.boxes.*.items' => 'required|array',
            'data.boxes.*.items.*.product_id' => 'required|integer|exists:products,id',
            'data.boxes.*.items.*.quantity' => 'required|integer|min:1',
            'data.boxes.*.items.*.item_id' => 'required',
        ],
            [
            // Общие сообщения
            'required' => 'Это поле обязательно для заполнения.',
            'array' => 'Это поле должно быть массивом.',
            'integer' => 'Значение должно быть целым числом.',
            'numeric' => 'Значение должно быть числом.',

            // Конкретные сообщения для полей
            'data.order_id.required' => 'Не указан ID заказа.',
            'data.order_id.exists' => 'Указанный заказ не существует.',

            'data.boxes.required' => 'Должна быть хотя бы одна коробка.',
            'data.boxes.min' => 'Должна быть хотя бы одна коробка.',

            'data.boxes.*.box_number.required' => 'Укажите номер коробки.',
            'data.boxes.*.box_number.max' => 'Номер коробки не может превышать 50 символов.',


            'data.boxes.*.length.numeric' => 'Длина должна быть числом.',
            'data.boxes.*.length.min' => 'Длина не может быть отрицательной.',
            'data.boxes.*.length.max' => 'Максимальная длина — 999,99 см.',

            'data.boxes.*.height.numeric' => 'Высота должна быть числом.',
            'data.boxes.*.height.min' => 'Высота не может быть отрицательной.',
            'data.boxes.*.height.max' => 'Максимальная высота — 999,99 см.',

            'data.boxes.*.width.numeric' => 'Ширина должна быть числом.',
            'data.boxes.*.width.min' => 'Ширина не может быть отрицательной.',
            'data.boxes.*.width.max' => 'Максимальная ширина — 999,99 см.',

            'data.boxes.*.weight.required' => 'Укажите вес коробки.',
            'data.boxes.*.weight.numeric' => 'Вес должен быть числом.',
            'data.boxes.*.weight.min' => 'Вес не может быть отрицательным.',
            'data.boxes.*.weight.max' => 'Максимальный вес — 999,99 кг.',

            'data.boxes.*.items.required' => 'В коробке должны быть товары.',

            'data.boxes.*.items.*.product_id.required' => 'Не указан товар.',
            'data.boxes.*.items.*.product_id.exists' => 'Указанный товар не существует.',

            'data.boxes.*.items.*.quantity.required' => 'Укажите количество товара.',
            'data.boxes.*.items.*.quantity.min' => 'Количество должно быть не менее 1.'
        ]
        );

//        return response()->json($validated);

       // Если валидация прошла успешно, продолжаем выполнение кода
        try {
            DB::transaction(function () use ($validated) {
                $orderId = $validated['data']['order_id'];
                $sellerId = Auth::user()->getFirstSeller()->id;
                // Удаляем существующие коробки и их товары для этого заказа
                OrderBox::where('order_id', $orderId)->delete();

                foreach ($validated['data']['boxes'] as $boxData) {
                    // Рассчитываем объём
                    $length = $boxData['length'] ?? 0;
                    $height = $boxData['height'] ?? 0;
                    $width = $boxData['width'] ?? 0;
                    $volume = $length * $height * $width;

                    // Создаём коробку
                    $box = OrderBox::create([
                        'order_id' => $orderId,
                        'box_number' => $boxData['box_number'],
                        'length' => $length,
                        'height' => $height,
                        'width' => $width,
                        'volume' => $volume,
                        'weight' => $boxData['weight'],
                        'notes' => $boxData['notes'] ?? null,
                        'seller_id' => $sellerId,
                    ]);

                    // Добавляем товары в коробку
                    foreach ($boxData['items'] as $itemData) {
                        BoxItem::create([
                            'box_id' => $box->id,
                            'product_id' => $itemData['product_id'],
                            'quantity' => $itemData['quantity'],
                            'order_item_id' => $itemData['item_id'],
                        ]);
                    }
                }

                $order = Order::where('id', $orderId)->first();
                $order->getSellerStatus($sellerId)->update([
                    'status' => 'assembly'
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Данные успешно сохранены'
            ]);
        } catch (\Exception $e) {
            \Log::error('Ошибка сохранения упаковки: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Произошла ошибка при сохранении данных'
            ], 500);
        }
    }
}
