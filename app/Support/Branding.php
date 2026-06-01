<?php

namespace App\Support;

class Branding
{
    public static function cssPath(): string
    {
        return (string) config('branding.css_path', 'branding/branding.css');
    }

    /**
     * Public URL for the branding stylesheet (includes ?v= filemtime for cache busting).
     */
    public static function stylesheetUrl(): string
    {
        $path = self::cssPath();
        $url = asset($path);
        $full = public_path($path);

        if (is_file($full)) {
            return $url.'?v='.filemtime($full);
        }

        return $url;
    }
}
