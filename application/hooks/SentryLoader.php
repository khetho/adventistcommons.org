<?php


class SentryLoader
{
    function initialize() {

        /*
        |--------------------------------------------------------------------------
        | Initializing Sentry
        |--------------------------------------------------------------------------
        |
        | In this function we init Sentry with the setup params that were provided
        | by sentry.io
        */
        
        Sentry\init(['dsn' => 'https://e217243c746a491c8312d165b916d6a4@sentry.io/1586264' ]);
		Sentry\captureLastError();
        
    }
}