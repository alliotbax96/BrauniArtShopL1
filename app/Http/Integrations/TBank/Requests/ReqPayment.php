<?php

namespace App\Http\Integrations\TBank\Requests;

use App\Http\Integrations\TBank\TbankConnector;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class ReqPayment extends Request implements HasBody
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
        protected string $PaymentID,
        protected string $RebildID,
    ){
        $this->TerminalId = config('services.tbank.terminalid');
        $this->TerminalPassword = config('services.tbank.terminalpassword');
    }
    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/Charge';
    }

    private function generateToken(): string
    {
        // Получаем данные из коннектора
        $terminalId = $this->TerminalId;
        $terminalPassword = $this->TerminalPassword;

        // Формируем массив для хеширования
        $hashData = [
            'PaymentId'=>$this->PaymentID,
            'RebildID'=>$this->RebildID,
            'TerminalKey' => $terminalId,
            'Password' => $terminalPassword,
        ];

        \Log::debug('Hash data before sorting:', $hashData);

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

        \Log::debug('AddCustomer request data before sending:', [
            'PaymentId'=>$this->PaymentID,
            'RebildID'=>$this->RebildID,
            'TerminalKey' => $this->TerminalId,
            'Token' => $token,
        ]);

        return [
            'PaymentId'=>$this->PaymentID,
            'RebildID'=>$this->RebildID,
            'TerminalKey' => $this->TerminalId,
            'Token' => $token,
        ];
    }
}
