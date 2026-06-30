<?php

namespace App\Http\Integrations\TBankOpenApi;

use Saloon\Http\Auth\TokenAuthenticator;

class TBankAuth extends TokenAuthenticator
{
    public function __construct(string $token)
    {
        parent::__construct($token, 'Bearer');
    }
}
