<?php

namespace App\Http\Integrations\YandexDelivery;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class YandexDeliveryConnector extends Connector
{
    use AcceptsJson;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://b2b.taxi.yandex.net/api/b2b/platform';
    }

    /**
     * Default headers for every request
     */
    protected function defaultHeaders(): array
    {
        return [
            'Authorization' => 'Bearer '.config('services.yandex.delivery.token'),
            'ContentType' => 'application/json',
        ];
    }

    /**
     * Default HTTP client options
     */
    protected function defaultConfig(): array
    {
        return [];
    }
}
