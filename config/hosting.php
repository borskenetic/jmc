<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Hostinger / multi-site hints (documentation + optional .env overrides)
    |--------------------------------------------------------------------------
    |
    | Subdomain apps live at public_html/{name}/ with document root public_html/{name}/public
    | See deploy/hostinger/README.md
    |
    */
    'hostinger' => [
        'subdomain' => env('HOSTINGER_SUBDOMAIN', null),
        'public_path' => env('HOSTINGER_PUBLIC_PATH', 'public'),
    ],

];
