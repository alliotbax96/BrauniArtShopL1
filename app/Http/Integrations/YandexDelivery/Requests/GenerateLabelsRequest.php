<?php

namespace App\Http\Integrations\YandexDelivery\Requests;

use App\Http\Integrations\YandexDelivery\YandexDeliveryConnector;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class GenerateLabelsRequest extends Request implements HasBody
{
    use HasJsonBody;
    /**
     * The HTTP method of the request
     */
    protected ?string $connector = YandexDeliveryConnector::class;
    protected Method $method = Method::POST;

    public function __construct(
        protected string $requestId,
    ){}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/request/generate-labels';
    }

    protected function defaultBody(): array
    {
        return array(
            'request_ids' => [$this->requestId],
        );
    }
}
