<?php

namespace App\Http\Integrations\YandexDelivery\Requests;

use App\Http\Integrations\YandexDelivery\YandexDeliveryConnector;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Saloon\Contracts\Body\HasBody;

class OffersConfirmRequest extends Request implements HasBody
{
    use HasJsonBody;
    /**
     * The HTTP method of the request
     */
    protected ?string $connector = YandexDeliveryConnector::class;
    protected Method $method = Method::POST;

    public function __construct(
        protected string $offerId,
    ){}
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/offers/confirm/';
    }

    protected function defaultBody(): array
    {
        return array(
        'offer_id' => $this->offerId,
         );
    }

}
