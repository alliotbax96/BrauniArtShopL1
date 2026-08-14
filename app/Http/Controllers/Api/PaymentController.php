<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Integrations\TBank\Requests\InitPayment;
use App\Http\Integrations\TBank\TbankConnector;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ApiKey;

class PaymentController extends Controller
{
    /**
     * Создание платежа
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:100000',
            'currency' => 'nullable|string|in:RUB',
            'description' => 'nullable|string|max:255',
            'user_id' => 'nullable|string',
            'user_email' => 'nullable|email',
            'user_name' => 'nullable|string|max:255',
            'transaction_id' => 'required|string|max:255',
            'return_url' => 'nullable|url',
            'webhook_url' => 'nullable|url',
        ]);

        // Получаем API ключ из запроса (устанавливается в middleware)
        $apiKeyId = $request->get('api_key_id');

        if (!$apiKeyId) {
            Log::error('API key ID not found in request');
            return response()->json(['error' => 'Invalid API key'], 401);
        }

        $apiKeyRecord = ApiKey::find($apiKeyId);

        if (!$apiKeyRecord) {
            Log::error('API key record not found', ['api_key_id' => $apiKeyId]);
            return response()->json(['error' => 'Invalid API key'], 401);
        }

        // Создаем локальную запись
        $payment = PaymentTransaction::create([
            'transaction_id' => $validated['transaction_id'],
            'amount' => $validated['amount'],
            'currency' => $validated['currency'] ?? 'RUB',
            'status' => PaymentTransaction::STATUS_PENDING,
            'return_url' => $validated['return_url'] ?? $apiKeyRecord->return_url,
            'webhook_url' => $validated['webhook_url'] ?? null,
            'api_key_id' => $apiKeyRecord->id,
            'child_data' => [
                'user_id' => $validated['user_id'] ?? null,
                'user_email' => $validated['user_email'] ?? null,
                'user_name' => $validated['user_name'] ?? null,
                'description' => $validated['description'] ?? 'Пополнение баланса',
                'service_name' => $apiKeyRecord->service_name,
                'service_logo' => $apiKeyRecord->service_logo,
                'primary_color' => $apiKeyRecord->primary_color,
                'secondary_color' => $apiKeyRecord->secondary_color,
                'text_color' => $apiKeyRecord->text_color,
                'bg_color' => $apiKeyRecord->bg_color,
                'company_name' => $apiKeyRecord->company_name,
                'inn' => $apiKeyRecord->inn,
            ],
        ]);

        try {
            // Инициализация платежа в Tinkoff
            $orderId = 'PAY_' . $payment->id . '_' . time();

            $connector = new TbankConnector();
            $requestTinkoff = new InitPayment(
                CustomerKey: $validated['user_email'] ?? 'customer_' . $payment->id,
                Recurrent: '',
                OrderId: $orderId,
                Amount: (string) $validated['amount'],
                SuccessURL: route('payment.redirect', ['id' => $payment->id, 'success' => true]),
                FailURL: route('payment.redirect', ['id' => $payment->id, 'fail' => true]),
                NotificationURL: 'https://baruniart.shop/api/payment/webhook',
            );

            $response = $connector->send($requestTinkoff);

            if (!$response->successful()) {
                Log::error('Tinkoff payment init failed', [
                    'transaction_id' => $payment->id,
                    'error' => $response->body(),
                ]);

                $payment->markAsFailed();
                return response()->json([
                    'success' => false,
                    'error' => 'Не удалось инициировать платеж',
                ], 500);
            }

            $data = $response->json();

            if (!isset($data['Success']) || !$data['Success']) {
                $errorMessage = $data['Message'] ?? 'Ошибка при инициализации платежа';
                Log::error('Tinkoff payment error', [
                    'transaction_id' => $payment->id,
                    'data' => $data,
                ]);

                $payment->markAsFailed();
                return response()->json([
                    'success' => false,
                    'error' => $errorMessage,
                ], 500);
            }

            $payment->update([
                'payment_id' => $data['PaymentId'],
                'payment_url' => $data['PaymentURL'],
                'payment_data' => $data,
                'status' => PaymentTransaction::STATUS_PROCESSING,
            ]);

            $redirectUrl = route('payment.redirect', [
                'id' => $payment->id,
                'payment_id' => $data['PaymentId'],
            ]);

            Log::info('Payment created successfully', [
                'transaction_id' => $payment->id,
                'payment_id' => $data['PaymentId'],
                'amount' => $payment->amount,
            ]);

            return response()->json([
                'success' => true,
                'id' => $data['PaymentId'],
                'url' => $redirectUrl,
                'payment_url' => $data['PaymentURL'],
            ]);

        } catch (\Exception $e) {
            Log::error('Payment creation exception', [
                'transaction_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $payment->markAsFailed();

            return response()->json([
                'success' => false,
                'error' => 'Внутренняя ошибка сервера: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function redirect(Request $request)
    {
        $paymentId = $request->get('id');
        $payment = PaymentTransaction::find($paymentId);

        if (!$payment || !$payment->id) {
            abort(404, 'Платеж не найден');
        }

        if($request->get('success')) {
            return view('payment.already-paid', [
                'payment' => $payment,
            ]);
        }

        if($request->get('fail')) {
            return view('payment.failed', [
                'payment' => $payment,
            ]);
        }

        // Получаем данные брендирования из платежа
        $branding = $payment->child_data ?? [];
        $companyName = $branding['company_name'] ?? 'ООО "БРАУНИ АРТ МАГАЗИН"';
        $inn = $branding['inn'] ?? '4705128695';
        $primaryColor = $branding['primary_color'] ?? '#90e9d1';
        $secondaryColor = $branding['secondary_color'] ?? '#5cd4b8';
        $textColor = $branding['text_color'] ?? '#0a1a1a';
        $bgColor = $branding['bg_color'] ?? '#ffffff';
        $serviceName = $branding['service_name'] ?? 'ALBAX Hosting';
        $serviceLogo = $branding['service_logo'] ?? '/assets/img/logo/logo.png';
        $serviceTextLogo = $branding['service_text_logo'] ?? '';
        $returnUrl = $payment->return_url ?? route('home');

        // Если платеж уже успешен
        if ($payment->isSuccess()) {
            return redirect()->away($returnUrl)->with('message', '✅ Баланс успешно пополнен!');
        }

        // Если платеж отменен или не удался
        if ($payment->status === PaymentTransaction::STATUS_FAILED ||
            $payment->status === PaymentTransaction::STATUS_CANCELED) {
            return view('payment.failed', ['payment' => $payment]);
        }

        // Если платеж уже в процессе и прошло больше 30 минут
        if ($payment->status === PaymentTransaction::STATUS_PROCESSING) {
            if ($payment->updated_at->diffInMinutes(now()) > 30) {
                $payment->markAsFailed();
                return view('payment.failed', ['payment' => $payment]);
            }
        }

        $paymentUrl = $payment->payment_url;

        if (!$paymentUrl) {
            Log::error('Payment URL not found', ['payment_id' => $payment->id]);
            return view('payment.failed', ['payment' => $payment]);
        }

        return view('payment.redirect', [
            'payment' => $payment,
            'paymentUrl' => $paymentUrl,
            'companyName' => $companyName,
            'inn' => $inn,
            'primaryColor' => $primaryColor,
            'secondaryColor' => $secondaryColor,
            'textColor' => $textColor,
            'bgColor' => $bgColor,
            'serviceName' => $serviceName,
            'serviceLogo' => $serviceLogo,
            'serviceTextLogo' => $serviceTextLogo,
            'returnUrl' => $returnUrl,
        ]);
    }

    public function status(Request $request, $id)
    {
        Log::info('Payment status requested', ['id' => $id, 'user' => $request->user()]);

        // Ищем платеж по ID из дочерней системы (transaction_id)
        $payment = PaymentTransaction::where('transaction_id', $id)->first();

        if (!$payment) {
            // Пробуем найти по payment_id
            $payment = PaymentTransaction::where('payment_id', $id)->first();
        }

        if (!$payment) {
            return response()->json([
                'success' => false,
                'error' => 'Payment not found',
            ], 404);
        }

        // Проверяем, есть ли у платежа webhook_url (принадлежит ли он дочке)
        if ($payment->webhook_url) {
            // Можно дополнительно проверить, что запрос пришел от авторизованного пользователя
            // или проверить API ключ
        }

        return response()->json([
            'success' => true,
            'status' => $payment->status,
            'amount' => $payment->amount,
            'paid_at' => $payment->paid_at,
            'transaction_id' => $payment->transaction_id,
            'payment_id' => $payment->payment_id,
            'is_completed' => $payment->isCompleted(),
            'is_success' => $payment->isSuccess(),
        ]);
    }

    /**
     * Получение статуса платежа (для внутреннего использования)
     */
    public function getStatus(Request $request, $id)
    {
        Log::info('Get payment status', ['id' => $id]);

        $payment = PaymentTransaction::find($id);

        if (!$payment) {
            $payment = PaymentTransaction::where('payment_id', $id)->first();
        }

        if (!$payment) {
            return response()->json([
                'success' => false,
                'error' => 'Payment not found',
            ], 404);
        }

        if ($payment->status === PaymentTransaction::STATUS_PROCESSING && $payment->payment_id) {
            try {
                $terminalId = config('services.tinkoff.terminal_id');

                // ОТКЛЮЧАЕМ ПРОВЕРКУ SSL
                $response = Http::withoutVerifying()
                    ->post('https://securepay.tinkoff.ru/v2/GetState', [
                        'TerminalKey' => $terminalId,
                        'PaymentId' => $payment->payment_id,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['Success']) && $data['Success']) {
                        $payment->markAsSuccess();

                        if ($payment->webhook_url) {
                            $this->sendWebhookToChild($payment);
                        }
                    } elseif (isset($data['ErrorCode']) && $data['ErrorCode'] !== '0') {
                        $payment->markAsFailed();
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to check Tinkoff status', [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'status' => $payment->status,
            'amount' => $payment->amount,
            'paid_at' => $payment->paid_at,
            'transaction_id' => $payment->transaction_id,
            'payment_id' => $payment->payment_id,
            'is_completed' => $payment->isCompleted(),
            'is_success' => $payment->isSuccess(),
        ]);
    }


    /**
     * Проверка статуса платежа в Tinkoff
     */
    public function checkStatus($paymentId)
    {
        try {
            $connector = new TbankConnector();
            $response = $connector->sent('/GetState', [
                'PaymentId' => $paymentId,
                'TerminalKey' => config('services.tinkoff.terminal_id'),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data;
            }
        } catch (\Exception $e) {
            Log::error('Check status error', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Обработка вебхука от Tinkoff
     */
    public function webhook(Request $request)
    {
        Log::info('Payment webhook received from Tinkoff', $request->all());

        try {
            if (!$request->isJson()) {
                return response('Not a JSON request', 400);
            }

            $data = $request->json()->all();

            // Валидация обязательных полей
            if (!isset($data['OrderId']) || !isset($data['PaymentId'])) {
                Log::error('Missing required fields in webhook', ['data' => $data]);
                return response('Missing required fields', 422);
            }

            // Парсим OrderId: PAY_{id}_{timestamp}
            $orderId = $data['OrderId'];
            $parts = explode('_', $orderId);

            if (count($parts) < 2 || $parts[0] !== 'PAY') {
                Log::warning('Invalid OrderId format', ['order_id' => $orderId]);
                return response('Invalid OrderId format', 400);
            }

            $paymentId = $parts[1];
            $payment = PaymentTransaction::find($paymentId);

            if (!$payment) {
                Log::warning('Payment not found', ['payment_id' => $paymentId]);
                return response('Payment not found', 404);
            }

            // Проверяем статус платежа
            if (isset($data['Success']) && $data['Success'] === true) {
                // Платеж успешен
                $payment->markAsSuccess();

                // Отправляем вебхук дочке
                if ($payment->webhook_url) {
                    $this->sendWebhookToChild($payment);
                }

                Log::info('Payment successful', [
                    'payment_id' => $payment->id,
                    'amount' => $payment->amount,
                ]);

                return response('OK', 200);
            } else {
                // Платеж не успешен
                $payment->markAsFailed();

                Log::warning('Payment failed', [
                    'payment_id' => $payment->id,
                    'error' => $data['Message'] ?? 'Unknown error',
                ]);

                return response('Payment failed', 402);
            }

        } catch (\Exception $e) {
            Log::error('Webhook processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response('Internal server error', 500);
        }
    }

    /**
     * Отправка вебхука дочерней системе
     */
    protected function sendWebhookToChild(PaymentTransaction $payment)
    {
        try {
            $payload = [
                'id' => $payment->payment_id,
                'amount' => $payment->amount,
                'status' => 'success',
                'transaction_id' => $payment->transaction_id,
            ];

            // Сортируем данные перед созданием токена
            ksort($payload);

            $secret = config('services.payment.webhook_secret');
            $payloadString = json_encode($payload, JSON_UNESCAPED_UNICODE);

            // Генерируем токен
            $payload['token'] = base64_encode(
                hash_hmac('sha256', $payloadString, $secret, true)
            );

            Log::info('Sending webhook to child', [
                'payload' => $payload,
                'payload_string' => $payloadString,
                'secret_length' => strlen($secret),
            ]);

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($payment->webhook_url, $payload);

            Log::info('Webhook sent to child', [
                'payment_id' => $payment->id,
                'child_url' => $payment->webhook_url,
                'response_status' => $response->status(),
                'response_body' => $response->body(),
            ]);

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('Failed to send webhook to child', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function test(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'API работает!',
            'method' => 'GET',
            'api_key' => $request->bearerToken(),
        ]);
    }
}
