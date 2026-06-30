<?php

namespace App\Http\Integrations\TBankOpenApi\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class PayRubleTransferRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * Эндпоинт для рублевых переводов
     */
    public function resolveEndpoint(): string
    {
        return '/payment/ruble-transfer/pay';
    }

    /**
     * Данные для отправки платежа
     */
    public function __construct(
        protected array $paymentData
    ) {}

    /**
     * Тело запроса в формате JSON
     */
    protected function defaultBody(): array
    {
        return $this->paymentData;
    }
}
