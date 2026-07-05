<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'redsms' => [
        'login'=> env('REDSMS_LOGIN'),
        'token'=> env('REDSMS_TOKEN')
    ],

    'tbank' => [
        'terminalid'=> env('TBANK_TERMINAL_ID'),
        'terminalpassword'=> env('TBANK_TERMINAL_PASSWORD'),
        'token' => env('TBANK_TOKEN'),
        'api_token' => env('TBANK_TOKEN'),
        'account_number' => env('TBANK_ACCOUNT_NUMBER'),
        'company_inn' => env('TBANK_COMPANY_INN'),
        'company_kpp' => env('TBANK_COMPANY_KPP'),
        'company_name' => env('TBANK_COMPANY_NAME'),
        'cert_path' => env('TBANK_CERT_PATH', '/path/to/cert.pem'),
        'key_path' => env('TBANK_KEY_PATH', '/path/to/key.key'),
        'cert_password' => env('TBANK_CERT_PASSWORD', null),
    ],

    'yandex' => [
        'client_id' => env('YANDEX_CLIENT_ID'),
        'client_secret' => env('YANDEX_CLIENT_SECRET'),
        'redirect' => env('YANDEX_REDIRECT_URI'),
        'delivery' => [
            'token' => env('YANDEX_DELIVERY_TOKEN'),
        ]
    ],
    'vkontakte' => [
        'client_id' => env('VK_CLIENT_ID'),
        'client_secret' => env('VK_CLIENT_SECRET'),
        'redirect' => env('VK_REDIRECT_URI'),
        'scope' => explode(' ', env('VK_SCOPE', '')),
    ],
    'fns' => [
        'source_device_id' => env('FN_SOURCE_DEVICE_ID'),
        'refresh_token' => env('FN_REFRESH_TOKEN'),
    ]




];
