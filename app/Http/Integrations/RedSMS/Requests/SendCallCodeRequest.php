<?php

namespace App\Http\Integrations\RedSMS\Requests;

use App\Http\Integrations\RedSMS\RedSMSConnector;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class SendCallCodeRequest extends Request
{
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

    protected function beforeSend(): void
    {
        $timestamp = time();
        $ts = $this->login . $timestamp;
        $secret = md5($ts . $this->token);

        $this->addHeaders([
            'login' => $this->login,
            'ts' => (string) $timestamp,
            'secret' => $secret,
        ]);
    }
}
