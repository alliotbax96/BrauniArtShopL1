<?php

namespace App\Http\Integrations\TBankOpenApi;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;

class TBankOpenApiConnector extends Connector
{
    use AcceptsJson;
    // Не используем AlwaysThrowOnErrors, так как нам нужна обработка разных статусов

    /**
     * Базовый URL для T-Bank API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://secured-openapi.tbank.ru/api/v1';
    }

    /**
     * Заголовки запроса по умолчанию
     */
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Аутентификация через Bearer токен
     */
    protected function defaultAuth(): TBankAuth
    {
        return new TBankAuth(config('services.tbank.api_token'));
    }

    /**
     * Настройка SSL сертификатов для mTLS
     */
    protected function defaultConfig(): array
    {
        return [
            'curl' => [
                CURLOPT_SSLCERT => config('services.tbank.cert_path'),
                CURLOPT_SSLKEY => config('services.tbank.key_path'),
                CURLOPT_SSLCERTPASSWD => config('services.tbank.cert_password'),
            ],
        ];
    }

    /**
     * Таймаут запроса
     */
    public function requestTimeout(): int
    {
        return 30;
    }
}
