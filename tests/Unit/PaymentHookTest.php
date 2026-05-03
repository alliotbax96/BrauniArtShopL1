<?php

namespace Tests\Feature;

use Tests\TestCase;

class PaymentHookTest extends TestCase
{
    /**
     * A basic functional test example.
     */
    public function test_making_an_api_request(): void
    {
        $response = $this->postJson('/checkout/PaymentHook', [
            'TerminalKey' => 'TBankTest',
            'Amount' => '100000',
            'OrderId' => '104',
            'Success' => '1',
            'Status' => 'string',
            'PaymentId' => '8420638345',
            'ErrorCode' => '0',
            'Message' => 'string',
            'Details' => 'string',
            'RebillId' => '3207469334',
            'CardId' => '665028415',
            'Pan' => 'string',
            'ExpDate' => '0229',
            'Token' => '7241ac8307f349afb7bb9dda760717721bbb45950b97c67289f23d8c69cc7b96'
        ]);

        $response
            ->assertStatus(200)
            ->ddBody('OK');
    }
}
