<?php

namespace App\Http\Integrations\TBankOpenApi;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class TBankOpenApiConnector extends Connector
{
    use AcceptsJson;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://business.tbank.ru/openapi/sandbox/api/v1';
    }

    /**
     * Default headers for every request
     */
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.config('services.tbank.token')
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
