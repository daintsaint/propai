<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS, Stripe and more. This file provides the 
    | default configuration for these services.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Stripe Configuration
    |--------------------------------------------------------------------------
    |
    | Configure Stripe for processing subscription payments.
    | Get your API keys from https://dashboard.stripe.com/apikeys
    |
    */
    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'webhook_url' => env('STRIPE_WEBHOOK_URL', '/api/webhooks/stripe'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Nebula Configuration (AI Agent Platform)
    |--------------------------------------------------------------------------
    |
    | Configure Nebula integration for AI agent automation.
    | This enables webhook communication and agent triggering.
    |
    */
    'nebula' => [
        'base_url' => env('NEBULA_BASE_URL', 'https://api.nebula.ai'),
        'api_key' => env('NEBULA_API_KEY'),
        'webhook_secret' => env('NEBULA_WEBHOOK_SECRET'),
        'webhook_url' => env('NEBULA_WEBHOOK_URL', '/api/webhooks/nebula'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Telegram Configuration (for agent integrations)
    |--------------------------------------------------------------------------
    */
    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'api_url' => env('TELEGRAM_API_URL', 'https://api.telegram.org/bot'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Mail Service Configuration
    |--------------------------------------------------------------------------
    */
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

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

];
