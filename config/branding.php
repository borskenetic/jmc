<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Branding stylesheet (per school / deployment)
    |--------------------------------------------------------------------------
    |
    | File under /public, loaded via asset(). Copy from demo-eidt and edit colors.
    | Example: BRANDING_CSS=branding/acd.css
    |
    */
    'css_path' => env('BRANDING_CSS', 'branding/branding.css'),

    /*
    | Optional: force ?v= on branding.css (e.g. BRANDING_ASSET_VERSION=2 after deploy).
    | Leave unset to auto-hash file contents (recommended).
    */
    'asset_version' => env('BRANDING_ASSET_VERSION'),
];
