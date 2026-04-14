<?php

namespace App\Http\Integrations\OrangeData\Requests;

use Saloon\Http\Request;
use Saloon\Enums\Method;

class CreateOrderRequest extends Request
{
    protected Method $method = Method::POST;

    public function __construct(private array $orderData) {}

    public function resolveEndpoint(): string
    {
        return '/api/v2/orders';
    }

    protected function defaultBody(): array
    {
        return $this->orderData;
    }
}
