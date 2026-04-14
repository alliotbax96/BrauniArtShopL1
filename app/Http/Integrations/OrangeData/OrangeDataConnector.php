<?php

namespace App\Http\Integrations\OrangeData;

use Saloon\Http\Connector;
use Saloon\Http\Authenticators\NoAuthenticator;

class OrangeDataConnector extends Connector
{
    public function __construct(
        private string $inn,
        private string $apiUrl,
        private string $signPkeyPath,
        private string $sslClientKeyPath,
        private string $sslClientCrtPath,
        private string $sslCaCertPath,
        private string $sslClientCrtPass
    ) {}

    public function resolveBaseUrl(): string
    {
        return $this->apiUrl;
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    protected function defaultConfig(): array
    {
        return [
            'verify' => $this->sslCaCertPath,
            'cert' => [$this->sslClientCrtPath, $this->sslClientCrtPass],
            'ssl_key' => $this->sslClientKeyPath,
        ];
    }
}
