<?php

namespace App\Http\Integrations\TBank\Requests;

use App\Http\Integrations\TBank\TbankConnector;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class Cancel extends Request implements HasBody
{
    use HasJsonBody;
    /**
     * The HTTP method of the request
     */
    protected ?string $connector = TBankConnector::class;
    protected Method $method = Method::POST;
    protected $TerminalId;
    protected $TerminalPassword;

    /**
     * The endpoint for the request
     */

    public function __construct(
      protected string $PaymentId,
    )
    {
        $this->TerminalId = config('services.tbank.terminalid');
        $this->TerminalPassword = config('services.tbank.terminalpassword');
    }

    public function resolveEndpoint(): string
    {
        return '/Cancel';
    }


    private function generateToken(): string
    {
        // Получаем данные из коннектора
        $terminalId = $this->TerminalId;
        $terminalPassword = $this->TerminalPassword;
            $hashData = [
                'PaymentID' => $this->PaymentId,
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
            'PaymentId' => $this->PaymentId,
            'TerminalKey' => $this->TerminalId,
            'Token' => $token,
        ];

    }
}
