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
        $hashData = array(
            'PaymentId'=>$this->PaymentID,
            'RebillId'=>$this->RebildID,
            'TerminalKey' => $this->TerminalId,
        );

        $token = $this->generateToken($hashData);
        $hashData['Token'] = $token;
        return $hashData;
    }
}
