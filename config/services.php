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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],


    'apiperu' => [
        'token' => env('APIPERU_TOKEN'),
        'url' => env('APIPERU_URL', 'https://apiperu.dev/api'),
    ],

    'facturacion' => [
        'url' => rtrim(env('FACTURACION_API_URL', env('APP_URL', 'http://localhost')), '/'),
    ],

    'sunat' => [
        'prod_auth_url' => env('SUNAT_PROD_AUTH_URL', 'https://api-seguridad.sunat.gob.pe/v1'),
        'prod_cpe_url' => env('SUNAT_PROD_CPE_URL', 'https://api-cpe.sunat.gob.pe/v1'),
        'beta_auth_url' => env('SUNAT_BETA_AUTH_URL', 'https://gre-test.nubefact.com/v1'),
        'beta_cpe_url' => env('SUNAT_BETA_CPE_URL', 'https://gre-test.nubefact.com/v1'),
        'beta_client_id' => env('SUNAT_BETA_CLIENT_ID'),
        'beta_client_secret' => env('SUNAT_BETA_CLIENT_SECRET'),
        'beta_sol_user' => env('SUNAT_BETA_SOL_USER', 'MODDATOS'),
        'beta_sol_pass' => env('SUNAT_BETA_SOL_PASS', 'MODDATOS'),
    ],

];
