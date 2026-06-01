<?php

namespace App\Support;

/**
 * Append ?v=filemtime to public assets so browsers fetch updates after deploy
 * (same filename, new image — avoids "works with F12 open" cache issues).
 */
class VersionedAsset
{
    public static function url(string $path): string
    {
        $path = ltrim(str_replace('\\', '/', $path), '/');
        $url = asset($path);
        $full = public_path($path);

        if (is_file($full)) {
            return $url.'?v='.filemtime($full);
        }

        return $url;
    }
}
