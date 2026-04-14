<?php

namespace App\Http\Integrations\RedSMS\Requests;

use App\Http\Integrations\RedSMS\RedSMSConnector;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\PendingRequest;
use Saloon\Traits\Body\HasJsonBody;

class SendCallCodeRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected ?string $connector = RedSMSConnector::class;
    protected Method $method = Method::POST;

    public function __construct(
        protected string $route,
        protected string $to,
        protected string $text,
        protected string $login,
        protected string $token
    ) {}

    public function resolveEndpoint(): string
    {
        return '/message';
    }

    protected function defaultBody(): array
    {
        return [
            'route' => $this->route,
            'to' => $this->to,
            'text' => $this->text,
        ];
    }



    protected function defaultHeaders(): array
    {
        $timestamp = time();
        $ts = $this->login . $timestamp;
        $secret = md5($ts . $this->token);

        return [
            'login' => $this->login,
            'ts' => (string) $ts,
            'secret' => $secret,
        ];
    }
}
