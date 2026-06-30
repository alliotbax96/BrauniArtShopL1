<?php

namespace App\Http\Integrations\FNS\Requests;

use App\Http\Integrations\FNS\FNSConnector;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class taxpayerStatus extends Request implements HasBody
{
    use HasJsonBody;
    /**
     * The HTTP method of the request
     */
    protected ?string $connector = FNSConnector::class;
    protected Method $method = Method::POST;

    /**
     * The endpoint for the request
     */
    public function __construct(
        protected string $inn
    ){}
    public function resolveEndpoint(): string
    {
        return '/taxpayer_status';
    }

    protected function defaultBody(): array
    {
        return [
            "inn"=> $this->inn,
            "requestDate"=> date('Y-m-d')
        ];
    }
}
