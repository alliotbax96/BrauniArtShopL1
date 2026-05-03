<?php

namespace App\Http\Integrations\TBank\Requests;

use App\Http\Integrations\TBank\TbankConnector;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class InitPayment extends Request implements HasBody
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
        protected string $CustomerKey,
        protected string $Recurrent = '',
        protected string $OrderId,
        protected string $Amount,
        protected string $SuccessURL = '',
        protected string $FailURL = ''
    ){
        $this->TerminalId = config('services.tbank.terminalid');
        $this->TerminalPassword = config('services.tbank.terminalpassword');
    }

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/Init';
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
            'OrderId' => $this->OrderId,
            'Amount' => $this->Amount*100,
            'CustomerKey' => $this->CustomerKey,
            'Recurrent' => $this->Recurrent,
            'TerminalKey' => $terminalId,
        ];

        if($this->Recurrent == 'Y') {
            $hashData['NotificationURL'] = 'https://brauniart.shop/api/PaymentHook';
            $hashData['Recurrent'] = 'Y';
        }

        if($this->SuccessURL != '') {
            $hashData['SuccessURL'] = $this->SuccessURL;
        }
        if($this->FailURL != '') {
            $hashData['FailURL'] = $this->FailURL;
        }

        $token = $this->generateToken($hashData);

        $hashData['Token'] = $token;
        return $hashData;
    }
}
