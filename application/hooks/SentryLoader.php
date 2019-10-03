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
        $sentry_dsn = $ci->config->item('sentry_dsn');
        if($sentry==TRUE){
            Sentry\init(['dsn' => $sentry_dsn ]);
            Sentry\captureLastError();
        }
        
        
    }
}

