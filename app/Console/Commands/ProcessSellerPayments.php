<?php
//
//namespace App\Console\Commands;
//
//use App\Http\Integrations\TBankOpenApi\Requests\PayRubleTransferRequest;
//use App\Http\Integrations\TBankOpenApi\TBankOpenApiConnector;
//use App\Models\SellerPayment;
//use Illuminate\Console\Command;
//use Illuminate\Support\Facades\Log;
//use Illuminate\Support\Str;
//
//class ProcessSellerPayments extends Command
//{
//    protected $signature = 'payments:process-sellers';
//    protected $description = 'Process pending seller payments via T-Bank API';
//
//    private TBankOpenApiConnector $tBankConnector;
//    private string $accountNumber;
//    private string $companyInn;
//    private string $companyKpp;
//    private string $companyName;
//
//    public function __construct()
//    {
//        parent::__construct();
//        $this->tBankConnector = new TBankOpenApiConnector();
//        $this->accountNumber = config('services.tbank.account_number');
//        $this->companyInn = config('services.tbank.company_inn');
//        $this->companyKpp = config('services.tbank.company_kpp');
//        $this->companyName = config('services.tbank.company_name');
//    }
//
//    public function handle(): void
//    {
//        $this->info('Starting seller payments processing...');
//
//        // Получаем невыплаченные платежи с продавцами и реквизитами
//        $pendingPayments = SellerPayment::with([
//            'seller.legalDetail',
//            'order'
//        ])
//            ->where('paid', false)
//            ->whereNotNull('order_id')
//            ->get();
//
//        if ($pendingPayments->isEmpty()) {
//            $this->info('No pending payments found.');
//            return;
//        }
//
//        $this->info("Found {$pendingPayments->count()} pending payments.");
//
//        $processedCount = 0;
//        $skippedCount = 0;
//        $failedCount = 0;
//
//        foreach ($pendingPayments as $payment) {
//            try {
//                $seller = $payment->seller;
//
//                if (!$seller) {
//                    $this->warn("Seller not found for payment ID: {$payment->id}");
//                    $failedCount++;
//                    continue;
//                }
//
//                $legalDetail = $seller->legalDetail();
//
//                if (!$legalDetail) {
//                    $this->warn("Legal details not found for seller ID: {$seller->id}");
//                    $failedCount++;
//                    continue;
//                }
//
//                // Проверяем ИНН продавца
//                if ($legalDetail->inn === '4705128695') {
//                    $this->info("Payment ID: {$payment->id} - INN 4705128695 marked as paid without transfer");
//                    $this->markAsPaid($payment);
//                    $skippedCount++;
//                    continue;
//                }
//
//                // Формируем данные для платежа
//                $paymentData = $this->preparePaymentData($payment, $seller, $legalDetail);
//
//                $this->info("Processing payment ID: {$payment->id}, Amount: {$payment->amount}, Seller: {$seller->name}");
//                $this->info("Payment data prepared: " . json_encode($paymentData, JSON_UNESCAPED_UNICODE));
//
//                // Отправляем платеж через T-Bank API используя Saloon
//                $response = $this->sendPayment($paymentData);
//
//                if ($response && $this->isPaymentSuccessful($response)) {
//                    $this->markAsPaid($payment);
//                    $this->info("✓ Payment ID: {$payment->id} processed successfully.");
//
//                    Log::info('Seller payment processed successfully', [
//                        'payment_id' => $payment->id,
//                        'seller_id' => $seller->id,
//                        'seller_name' => $seller->name,
//                        'amount' => $payment->amount,
//                        'tbank_response' => $response
//                    ]);
//
//                    $processedCount++;
//                } else {
//                    $this->error("✗ Failed to process payment ID: {$payment->id}");
//
//                    Log::error('Seller payment failed', [
//                        'payment_id' => $payment->id,
//                        'seller_id' => $seller->id,
//                        'amount' => $payment->amount,
//                        'response' => $response
//                    ]);
//
//                    $failedCount++;
//                }
//
//                // Соблюдаем ограничение API: 10 запросов в секунду
//                usleep(150000); // 150ms пауза между запросами
//
//            } catch (\Exception $e) {
//                $this->error("Error processing payment ID: {$payment->id} - {$e->getMessage()}");
//                Log::error('Seller payment processing error', [
//                    'payment_id' => $payment->id,
//                    'error' => $e->getMessage(),
//                    'trace' => $e->getTraceAsString()
//                ]);
//                $failedCount++;
//                continue;
//            }
//        }
//
//        $this->info("Processing completed. Processed: {$processedCount}, Skipped: {$skippedCount}, Failed: {$failedCount}");
//    }
//
//    /**
//     * Подготовка данных для платежа в соответствии с документацией T-Bank API
//     */
//    private function preparePaymentData(SellerPayment $payment, $seller, $legalDetail): array
//    {
//        // Уникальный идентификатор платежа
//        $paymentId = "seller_payment_{$payment->id}_" . Str::uuid();
//
//        // Номер распоряжения (макс 6 цифр)
//        $documentNumber = (int) substr(abs(crc32($payment->id)), -6);
//        if ($documentNumber < 1) {
//            $documentNumber = 1;
//        }
//
//        // Назначение платежа (макс 210 символов)
//        $orderNumber = $payment->order ? $payment->order->id : $payment->id;
//
//        if ($legalDetail->isIndividualEntrepreneur()) {
//            $purpose = "Выплата ИП {$seller->name} за заказ №{$orderNumber}. Без НДС";
//        } else {
//            $purpose = "Выплата продавцу {$legalDetail->legal_name} за заказ №{$orderNumber}. Без НДС";
//        }
//
//        // Обрезаем назначение до 210 символов
//        $purpose = mb_substr($purpose, 0, 210);
//
//        // Формируем данные получателя
//        $toData = [
//            'name' => $legalDetail->legal_name,
//            'inn' => $legalDetail->inn,
//            'bik' => $legalDetail->bik,
//            'bankName' => $legalDetail->bank_name,
//            'corrAccountNumber' => $legalDetail->correspondent_account,
//            'accountNumber' => $legalDetail->account_number,
//        ];
//
//        // Добавляем КПП для юридических лиц
//        if (!$legalDetail->isIndividualEntrepreneur() && $legalDetail->kpp) {
//            $toData['kpp'] = $legalDetail->kpp;
//        }
//
//        // Основные данные платежа
//        $paymentData = [
//            'id' => $paymentId,
//            'from' => [
//                'accountNumber' => $this->accountNumber,
//            ],
//            'to' => $toData,
//            'purpose' => $purpose,
//            'amount' => (float) $payment->amount,
//            'documentNumber' => $documentNumber,
//            'executionOrder' => 5,
//            'dueDate' => now()->addDays(5)->toIso8601String(),
//            'meta' => [
//                'seller_payment_id' => $payment->id,
//                'seller_id' => $seller->id,
//                'order_id' => $payment->order_id,
//                'processed_at' => now()->toIso8601String(),
//            ],
//        ];
//
//        return $paymentData;
//    }
//
//    /**
//     * Отправка платежа через T-Bank API используя Saloon
//     * Возвращает массив с ответом при успехе, null при ошибке
//     */
//    private function sendPayment(array $data): ?array
//    {
//        try {
//            $this->info("Sending payment to T-Bank API...");
//
//            // Логируем данные запроса
//            Log::info('T-Bank API request data', [
//                'payment_data' => $data
//            ]);
//
//            // Создаем запрос
//            $request = new PayRubleTransferRequest($data);
//
//            // Отправляем запрос через коннектор
//            $response = $this->tBankConnector->send($request);
//
//            $statusCode = $response->status();
//            $responseBody = $response->json();
//
//            $this->info("Response status: {$statusCode}");
//
//            // Логируем ответ
//            Log::info('T-Bank API response', [
//                'status' => $statusCode,
//                'response' => $responseBody
//            ]);
//
//            if ($statusCode === 201) {
//                $this->info("Payment created successfully. PaymentID: " . ($responseBody['paymentId'] ?? 'N/A'));
//                return $responseBody;
//            }
//
//            // Обработка ошибок
//            $this->error("T-Bank API error. Status: {$statusCode}");
//            $this->error("Response: " . json_encode($responseBody, JSON_UNESCAPED_UNICODE));
//
//            Log::error('T-Bank API returned error', [
//                'status' => $statusCode,
//                'response' => $responseBody,
//                'request_data' => $data
//            ]);
//
//            return null;
//
//        } catch (\Saloon\Exceptions\Request\RequestException $e) {
//            $this->error("T-Bank API request failed: " . $e->getMessage());
//
//            Log::error('T-Bank API request exception', [
//                'error' => $e->getMessage(),
//                'data' => $data
//            ]);
//
//            return null;
//
//        } catch (\Exception $e) {
//            $this->error("Unexpected error: " . $e->getMessage());
//
//            Log::error('Unexpected error during T-Bank API request', [
//                'error' => $e->getMessage(),
//                'data' => $data
//            ]);
//
//            return null;
//        }
//    }
//
//    /**
//     * Проверка успешности платежа
//     */
//    private function isPaymentSuccessful(array $response): bool
//    {
//        return isset($response['paymentId']) && !empty($response['paymentId']);
//    }
//
//    /**
//     * Отметка платежа как выплаченного
//     */
//    private function markAsPaid(SellerPayment $payment): void
//    {
//        $payment->update([
//            'paid' => true,
//            'paid_at' => now(),
//        ]);
//
//        $this->info("Payment ID: {$payment->id} marked as paid at {$payment->paid_at}");
//    }
//}
