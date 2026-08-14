<?php

namespace App\Http\Integrations\TBank;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
class TbankConnector extends Connector
{
    use AcceptsJson;

    private string $TerminalId;
    private string $TerminalPassword;

    public function __construct(string $TerminalId = null, string $TerminalPassword = null)
    {
        $this->TerminalId = config('services.tbank.terminalid');
        $this->TerminalPassword = config('services.tbank.terminalpassword');
    }

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://securepay.tinkoff.ru/v2';
    }

    /**
     * Default headers for every request
     */
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Get Terminal ID
     */
    public function getTerminalId(): string
    {
        return $this->TerminalId;
    }

    protected function defaultConfig(): array
    {
        return [
            'verify' => false, // Отключаем проверку SSL
        ];
    }

    /**
     * Get Terminal Password
     */
    public function getTerminalPassword(): string
    {
        return $this->TerminalPassword;
    }
}
