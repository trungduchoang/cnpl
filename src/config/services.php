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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
        'version' => env('AWS_SES_VERSION', '2010-12-01')
    ],

    'cognito' => [
        'version' => env('AWS_COGNITO_VERSION'),
        'user_pool_id' => env('AWS_COGNITO_USER_POOL_ID'),
        'app_client_id' => env('AWS_COGNITO_APP_CLIENT_ID'),
        'cognito_domain' => env('AWS_COGNITO_DOMAIN')
    ],

    'line' => [
        'client_id' => env('LINE_CLIENT_ID'),
        'channel_secret' => env('LINE_CHANNEL_SECRET'),
        'channel_access_token' => env('CHANNEL_ACCESS_TOKEN')
    ]

];
