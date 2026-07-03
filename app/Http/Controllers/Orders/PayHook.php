<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\BaseController;
use App\Models\UserCard;
use App\Models\Order;
use App\Models\BookOrder;
use Illuminate\Http\Request;
use App\Services\TbankService;

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

            \Log::info('Payment hook received', ['data' => $data]);

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

            // Проверка ErrorCode
            if (isset($data['ErrorCode']) && $data['ErrorCode'] !== '0') {
                \Log::warning('Payment failed with error code', [
                    'error_code' => $data['ErrorCode'],
                    'message' => $data['Message'] ?? 'No message',
                    'order_id' => $data['OrderId']
                ]);
                return response('Payment failed', 402);
            }

            // Обработка RebillId
            if (isset($data['RebillId']) && !empty($data['RebillId'])) {
                try {
                    UserCard::create([
                        'RebildID' => $data['RebillId'],
                        'CardID' => $data['CardId'] ?? null
                    ]);
                    \Log::info('UserCard record created', [
                        'rebill_id' => $data['RebillId'],
                        'card_id' => $data['CardId'] ?? 'unknown'
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to create UserCard record', [
                        'exception' => $e->getMessage(),
                        'data' => $data,
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Не прерываем обработку, это не критично
                }
            }

            $check = explode('##', $data['OrderId']);

            // Логика для binding-операций
            if (count($check) >= 2 && $check[0] === 'binding') {
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

                    if (is_array($result) && isset($result['Success']) && !$result['Success']) {
                        \Log::error('Failed to cancel payment', [
                            'payment_id' => $data['PaymentId'],
                            'order_id' => $data['OrderId'],
                            'result' => $result
                        ]);
                        return response('Failed to cancel payment', 500);
                    } elseif (!is_array($result)) {
                        \Log::error('Unexpected result from CancelPayment', [
                            'payment_id' => $data['PaymentId'],
                            'order_id' => $data['OrderId'],
                            'result' => $result
                        ]);
                        return response('Internal server error', 500);
                    }

                    \Log::info('Payment binding cancelled successfully', [
                        'payment_id' => $data['PaymentId'],
                        'order_id' => $data['OrderId']
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Exception during payment cancellation', [
                        'exception' => $e->getMessage(),
                        'payment_id' => $data['PaymentId'],
                        'order_id' => $data['OrderId'],
                        'trace' => $e->getTraceAsString()
                    ]);
                    return response('Internal server error', 500);
                }

                return response('OK', 200);
            }

            // Обычная логика: обновление статуса по paymentId в Order ИЛИ BookOrder
            if (!isset($data['PaymentId']) || empty($data['PaymentId'])) {
                \Log::error('PaymentId is required for order status update', [
                    'order_id' => $data['OrderId'],
                    'received_data' => $data
                ]);
                return response('PaymentId is required', 422);
            }

            $paymentId = $data['PaymentId'];

            // Сначала пробуем обновить обычный заказ
            $updated = Order::where('payment_id', $paymentId)->update(['status' => 'paid']);
            if ($updated) {
                \Log::info('Order status updated to paid (Order)', [
                    'order_id' => $data['OrderId'],
                    'payment_id' => $paymentId
                ]);
                return response('OK', 200);
            }

            // Если не нашли в Order — пробуем BookOrder
            $bookOrderUpdated = BookOrder::where('payment_id', $paymentId)
                ->update([
                    'status' => 'paid',
                    'paid_at' => now()
                ]);

            if ($bookOrderUpdated) {
                \Log::info('BookOrder status updated to paid (BookOrder)', [
                    'order_id' => $data['OrderId'],
                    'payment_id' => $paymentId
                ]);
                return response('OK', 200);
            }

            \Log::warning('Order not found or not updated', [
                'order_id' => $data['OrderId'],
                'payment_id' => $paymentId
            ]);

            return response('Order not found', 404);
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
