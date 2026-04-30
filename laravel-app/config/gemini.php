<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Gemini API Credentials
    |--------------------------------------------------------------------------
    */
    'api_key' => env('GEMINI_API_KEY'),
    'model'   => env('GEMINI_MODEL', 'gemini-2.5-flash'),

    /*
    |--------------------------------------------------------------------------
    | Endpoint Construction
    |--------------------------------------------------------------------------
    */
    'base_uri' => env('GEMINI_BASE_URI', 'https://generativelanguage.googleapis.com/v1beta/'),
    'endpoint' => 'models/' . env('GEMINI_MODEL', 'gemini-2.5-flash') . ':generateContent',

    /*
    |--------------------------------------------------------------------------
    | Aliases — used by AIWebsiteGenerator and WebsiteController
    | These fix the broken config('gemini.url') / config('gemini.key') calls
    |--------------------------------------------------------------------------
    */
    'url' => env('GEMINI_BASE_URI', 'https://generativelanguage.googleapis.com/v1beta/')
           . 'models/' . env('GEMINI_MODEL', 'gemini-2.5-flash') . ':generateContent',

    'key' => env('GEMINI_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Timeout
    |--------------------------------------------------------------------------
    */
    'timeout' => 60,

];