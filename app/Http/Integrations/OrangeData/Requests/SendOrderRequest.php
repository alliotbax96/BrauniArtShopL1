<?php

namespace App\Http\Integrations\OrangeData\Requests;

use Saloon\Http\Request;
use Saloon\Enums\Method;

class SendOrderRequest extends Request
{
    protected Method $method = Method::POST;

    public function __construct(private string $orderId) {}

    public function resolveEndpoint(): string
    {
        return "/api/v2/orders/{$this->orderId}/send";
    }
}
