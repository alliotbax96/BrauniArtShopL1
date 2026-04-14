<?php

namespace App\Http\Integrations\TBank\Requests;

use App\Http\Integrations\TBank\TbankConnector;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class GetCustomer extends Request implements HasBody
{
    use HasJsonBody;
    /**
     * The HTTP method of the request
     */
    protected ?string $connector = TBankConnector::class;
    protected Method $method = Method::POST;
    protected $TerminalId;
    protected $TerminalPassword;

    public function __construct(
        protected string $CustomerKey
    ){
        $this->TerminalId = config('services.tbank.terminalid');
        $this->TerminalPassword = config('services.tbank.terminalpassword');
    }

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/GetCustomer';
    }

    private function generateToken(): string
    {
        // Получаем данные из коннектора
        $terminalId = $this->TerminalId;
        $terminalPassword = $this->TerminalPassword;

        // Формируем массив для хеширования
        $hashData = [
            'CustomerKey' => $this->CustomerKey,
            'TerminalKey' => $terminalId,
            'Password' => $terminalPassword,
        ];

        // Сортируем по ключам
        ksort($hashData);

        // Объединяем значения в строку
        $hashString = implode('', $hashData);

        // Генерируем SHA256 хеш
        $token = hash('sha256', $hashString);

        \Log::debug('Generated token:', ['token' => $token]);

        return $token;
    }

    protected function defaultBody(): array
    {
        $token = $this->generateToken();

        return [
            'CustomerKey' => $this->CustomerKey,
            'TerminalKey' => $this->TerminalId,
            'Token' => $token,
        ];

    }
}
