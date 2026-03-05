<?php

namespace App\Http\Integrations\RedSMS;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class RedSMSConnector extends Connector
{
    use AcceptsJson;

    public string $login;
    public string $token;

    public function __construct(string $login = null, string $token = null)
    {
        $this->login = $login ?? config('services.redsms.login');
        $this->token = $token ?? config('services.redsms.token');
    }

    public function resolveBaseUrl(): string
    {
        return 'https://cp.redsms.ru/api';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }
}
