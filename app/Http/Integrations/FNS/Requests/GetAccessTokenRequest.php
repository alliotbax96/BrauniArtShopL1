<?php

namespace App\Http\Integrations\FNS\Requests;

use App\Http\Integrations\FNS\AuthConnector;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class GetAccessTokenRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected string $connector = AuthConnector::class;
    protected Method $method = Method::POST;

    public function __construct(
        private readonly array $deviceInfo,
        private readonly string $refreshToken
    ) {}

    public function resolveEndpoint(): string
    {
        return '/auth/token';
    }

    public function defaultBody(): array
    {
        return [
            'deviceInfo' => $this->deviceInfo,
            'refreshToken' => $this->refreshToken,
        ];
    }
}
