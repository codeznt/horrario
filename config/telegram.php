<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Token
    |--------------------------------------------------------------------------
    |
    | Your Telegram Bot token obtained from @BotFather.
    | This is used for validating WebApp authentication data.
    |
    */

    'bot_token' => env('TELEGRAM_BOT_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Auth Data Max Age
    |--------------------------------------------------------------------------
    |
    | Maximum age in seconds for Telegram auth data to be considered valid.
    | Default is 86400 seconds (24 hours).
    |
    */

    'auth_max_age' => env('TELEGRAM_AUTH_MAX_AGE', 86400),

    /*
    |--------------------------------------------------------------------------
    | WebApp URL
    |--------------------------------------------------------------------------
    |
    | The URL of your Telegram WebApp. This should match the URL you've
    | configured in @BotFather for your bot's WebApp.
    |
    */

    'webapp_url' => env('TELEGRAM_WEBAPP_URL', config('app.url')),

];