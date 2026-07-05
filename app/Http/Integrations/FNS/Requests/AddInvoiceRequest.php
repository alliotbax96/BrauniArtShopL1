<?php

namespace App\Http\Integrations\FNS\Requests;

use App\Http\Integrations\FNS\AuthConnector;
use Illuminate\Support\Facades\Cache;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class AddInvoiceRequest extends Request implements HasBody
{
    use HasJsonBody;
    /**
     * The HTTP method of the request
     */
    protected string $connector = AuthConnector::class;
    protected Method $method = Method::POST;

    public function __construct(
        private readonly int $amount
    ) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/invoice';
    }

    /**
     * Default headers for the request
     */
    protected function defaultHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . Cache::get('fns_access_token'),
        ];
    }

    public function defaultBody(): array
    {
        $requestBody = [
            'paymentType' => 'ACCOUNT',
            'bankName' => 'АО "ТБАНК"',
            'bankBik' => '044525974',
            'corrAccount' => '30101810145250000974',
            'currentAccount' => '40817810000000346736',
            'clientName' => 'ИП ШИЛОВ МАКСИМ АЛЕКСАНДРОВИЧ',
            'clientInn' => '782600455701',
            'type' => 'MANUAL',
            'services' => [
                [
                    'name' => 'Услуги по разработке программного обеспечения',
                    'amount' => $this->amount,
                    'quantity' => 1,
                    'serviceNumber' => 0,
                ],
            ],
            'totalAmount' => $this->amount,
            'clientType' => 'FROM_LEGAL_ENTITY',
        ];
        return $requestBody;
    }
}
