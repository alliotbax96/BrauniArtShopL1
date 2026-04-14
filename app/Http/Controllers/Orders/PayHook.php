<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\BaseController;
use App\Services\TbankService;
use Illuminate\Http\Request;
use App\Models\UserCard;
use App\Models\Order;

class PayHook extends BaseController
{
    public function index(Request $request)
    {
        try {
            if (!$request->isJson()) {
                \Log::warning('Non-JSON request received', [
                    'ip' => $request->ip(),
                    'headers' => $request->headers->all()
                ]);
                return response('Not a JSON request', 400);
            }

            $data = $request->json()->all();

            // Валидация обязательных полей
            $requiredFields = ['OrderId'];
            $missingFields = array_filter($requiredFields, function ($field) use ($data) {
                return !isset($data[$field]) || empty($data[$field]);
            });

            if (!empty($missingFields)) {
                \Log::error('Missing required fields in JSON request', [
                    'missing_fields' => $missingFields,
                    'received_data' => $data
                ]);
                return response('Missing required fields', 422);
            }

            // Обработка RebillId, если присутствует
            if (isset($data['RebillId']) && !empty($data['RebillId'])) {
                try {
                    UserCard::create([
                        'RebillID' => $data['RebillId'],
                        'CardID' => $data['CardId'] ?? null
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to create UserCard record', [
                        'exception' => $e->getMessage(),
                        'data' => $data,
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Продолжаем выполнение, так как это не критичная операция
                }
            }

            $check = explode('##', $data['OrderId']);

            if ($check[0] !== 'binding') {
                // Обновляем статус заказа
                try {
                    $updated = Order::where('id', $data['OrderId'])->update(['status' => 'paid']);
                    if (!$updated) {
                        \Log::warning('Order not found or not updated', [
                            'order_id' => $data['OrderId']
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Database error while updating order status', [
                        'exception' => $e->getMessage(),
                        'order_id' => $data['OrderId'],
                        'trace' => $e->getTraceAsString()
                    ]);
                    return response('Internal server error', 500);
                }
            } else {
                // Обрабатываем отмену платежа для операций привязки
                if (!isset($data['PaymentId']) || empty($data['PaymentId'])) {
                    \Log::error('PaymentId is required for binding operations', [
                        'order_id' => $data['OrderId'],
                        'received_data' => $data
                    ]);
                    return response('PaymentId is required', 422);
                }

                try {
                    $tbankService = new TbankService();
                    $result = $tbankService->CancelPayment($data['PaymentId']);

                    // Проверяем результат операции отмены платежа
                    if ($result === false) {
                        \Log::error('Failed to cancel payment', [
                            'payment_id' => $data['PaymentId'],
                            'order_id' => $data['OrderId']
                        ]);
                        return response('Failed to cancel payment', 500);
                    }
                } catch (\Exception $e) {
                    \Log::error('Exception during payment cancellation', [
                        'exception' => $e->getMessage(),
                        'payment_id' => $data['PaymentId'],
                        'order_id' => $data['OrderId'],
                        'trace' => $e->getTraceAsString()
                    ]);
                    return response('Internal server error', 500);
                }
            }

            return response('OK', 200);

        } catch (\Exception $e) {
            \Log::critical('Unexpected error in index method', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response('Internal server error', 500);
        }
    }

}
