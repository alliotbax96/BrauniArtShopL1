<?php

namespace App\Http\Integrations\OrangeData\Requests;

use Saloon\Http\Request;
use Saloon\Enums\Method;

class AddPaymentRequest extends Request
{
    protected Method $method = Method::PUT;

    public function __construct(
        private string $orderId,
        private array $paymentData
    ) {}

    public function resolveEndpoint(): string
    {
        return "/api/v2/orders/{$this->orderId}/payments";
    }

    protected function defaultBody(): array
    {
        return $this->paymentData;
    }
}
