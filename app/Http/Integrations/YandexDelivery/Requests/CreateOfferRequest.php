<?php

namespace App\Http\Integrations\YandexDelivery\Requests;

use App\Http\Integrations\YandexDelivery\YandexDeliveryConnector;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Saloon\Contracts\Body\HasBody;

class CreateOfferRequest extends Request implements HasBody
{
    use HasJsonBody;
    /**
     * The HTTP method of the request
     */
    protected ?string $connector = YandexDeliveryConnector::class;
    protected Method $method = Method::POST;

    public function __construct(
        protected string $orderID,
        protected string $source,
        protected string $destination,
        protected array  $places,
        protected array  $recipientInfo,
        protected array  $items,
    ){}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/offers/create';
    }

    protected function defaultBody(): array
    {
        return array(
            'info' => array(
                'operator_request_id' => $this->orderID,
            ),
            'source' => array(
                'platform_station' => array(
                    'platform_id' => $this->source,
                ),
            ),
            'destination' => array(
                'platform_station' => array(
                    'platform_id' => $this->destination,
                ),
            ),
            'items' => $this->items,
            'places' => $this->places,
            'billing_info' => array(
                'payment_method' => 'already_paid',
            ),
            'recipient_info' => array(
                'first_name' => $this->recipientInfo['first_name'],
                'phone' => $this->recipientInfo['phone'],
            ),
            'last_mile_policy' => 'self_pickup',
        );
    }
}
