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
        $ci = get_instance(); // CI_Loader instance
        $ci->load->config('config');
        $sentry = $ci->config->item('sentry');
        if($sentry==TRUE){
            var_dump($ci->config->item('sentry'));
            Sentry\init(['dsn' => 'https://e217243c746a491c8312d165b916d6a4@sentry.io/1586264' ]);
            Sentry\captureLastError();
        }
        
        
    }
}

