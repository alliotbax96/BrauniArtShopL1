<?php

namespace App\Http\Integrations\FNS;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class FNSConnector extends Connector
{
    use AcceptsJson;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://statusnpd.nalog.ru/api/v1/tracker';
    }

    /**
     * Default headers for every request
     */
    protected function defaultHeaders(): array
    {
        return [];
    }

    /**
     * Default HTTP client options
     */
    protected function defaultConfig(): array
    {
        return [];
    }
}
