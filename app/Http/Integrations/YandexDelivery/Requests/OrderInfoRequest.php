<?php

namespace App\Http\Integrations\YandexDelivery\Requests;

use App\Http\Integrations\YandexDelivery\YandexDeliveryConnector;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class OrderInfoRequest extends Request
{
    /**
     * The HTTP method of the request
     */
    protected ?string $connector = YandexDeliveryConnector::class;
    protected Method $method = Method::GET;

    public function __construct(
        protected string $requestId,
    ){}
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/request/info';
    }

    public function defaultQuery(): array
    {
        return [
            'request_id'=>$this->requestId,
        ];
    }

}
