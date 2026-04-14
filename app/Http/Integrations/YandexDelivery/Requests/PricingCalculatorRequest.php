<?php

namespace App\Http\Integrations\YandexDelivery\Requests;

use App\Http\Integrations\YandexDelivery\YandexDeliveryConnector;
use Illuminate\Testing\Fluent\Concerns\Has;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class PricingCalculatorRequest extends Request implements HasBody
{
    use HasJsonBody;
    /**
     * The HTTP method of the request
     */
    protected ?string $connector = YandexDeliveryConnector::class;
    protected Method $method = Method::POST;

    public function __construct(
        protected string $source_platform_station_id,
        protected string $destination_platform_station_id,
        protected string $weight,
    ){}
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/pricing-calculator';
    }

    protected function defaultBody(): array {
        return array(
            'source' => array(
                'platform_station_id' => $this->source_platform_station_id,
            ),
            'destination' => array(
                'platform_station_id' => $this->destination_platform_station_id,
            ),
            'tariff' => 'self_pickup',
            'total_weight' => $this->weight*1000,
        );
    }
}
