<?php

namespace App\Http\Integrations\TBank\Requests;

use App\Http\Integrations\TBank\TbankConnector;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class DeleteCard extends Request implements HasBody
{
    use HasJsonBody;

    protected ?string $connector = TbankConnector::class;
    protected Method $method = Method::POST;
    protected $TerminalId;
    protected $TerminalPassword;

    public function __construct(
      protected string $CustomerKey,
      protected string $CardId,
    )
    {
        $this->TerminalId = config('services.tbank.terminalid');
        $this->TerminalPassword = config('services.tbank.terminalpassword');
    }

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/RemoveCard';
    }

    private function generateToken(): string
    {
        // Получаем данные из коннектора
        $terminalId = $this->TerminalId;
        $terminalPassword = $this->TerminalPassword;
        $hashData = [
            'CustomerKey' => $this->CustomerKey,
            'CardId' => $this->CardId,
            'TerminalKey' => $terminalId,
            'Password' => $terminalPassword,
        ];
        // Сортируем по ключам
        ksort($hashData);
        // Объединяем значения в строку
        $hashString = implode('', $hashData);
        // Генерируем SHA256 хеш
        $token = hash('sha256', $hashString);
        return $token;
    }

    protected function defaultBody(): array
    {
        $token = $this->generateToken();

        return [
            'CustomerKey' => $this->CustomerKey,
            'CardId' => $this->CardId,
            'TerminalKey' => $this->TerminalId,
            'Token' => $token,
        ];

    }
}
