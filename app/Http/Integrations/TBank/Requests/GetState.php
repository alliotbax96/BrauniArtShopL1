<?php

namespace App\Http\Integrations\TBank\Requests;

use App\Http\Integrations\TBank\TbankConnector;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class GetState extends Request implements HasBody
{
    use HasJsonBody;
    /**
     * The HTTP method of the request
     */
    protected ?string $connector = TBankConnector::class;
    protected Method $method = Method::POST;
    protected string $TerminalId;
    protected string $TerminalPassword;

    public function __construct(
        protected string $PaymentId
    ){
        $this->TerminalId = config('services.tbank.terminalid');
        $this->TerminalPassword = config('services.tbank.terminalpassword');
    }
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/GetState';
    }

    private function generateToken($hashData): string
    {
        $terminalPassword = $this->TerminalPassword;

        $hashData['Password'] = $terminalPassword;

        // Сортируем по ключам
        ksort($hashData);

        // Объединяем значения в строку
        $hashString = implode('', $hashData);

        // Генерируем SHA256 хеш
        $token = hash('sha256', $hashString);

        \Log::debug('Generated token:', ['hash'=> $hashData, 'token' => $token]);

        return $token;
    }

    protected function defaultBody(): array
    {
        // Получаем данные из коннектора
        $terminalId = $this->TerminalId;

        $hashData = [
            'TerminalKey' => $terminalId,
            'PaymentId' => $this->PaymentId,
        ];
        $token = $this->generateToken($hashData);
        $hashData['Token'] = $token;
        return $hashData;
    }
}
