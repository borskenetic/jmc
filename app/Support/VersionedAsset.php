<?php

namespace App\Support;

/**
 * Append ?v= to public assets so browsers fetch updates after deploy.
 * Uses an MD5 of file contents (not mtime) so Hostinger/LiteSpeed cannot
 * keep serving stale CSS when the timestamp did not change.
 */
class VersionedAsset
{
    public static function url(string $path): string
    {
        $path = ltrim(str_replace('\\', '/', $path), '/');
        $url = asset($path);
        $full = public_path($path);

        if (! is_file($full)) {
            return $url;
        }

        $version = config('branding.asset_version');
        if ($version !== null && $version !== '') {
            return $url.'?v='.$version;
        }

        return $url.'?v='.self::contentVersion($full);
    }

    public static function contentVersion(string $fullPath): string
    {
        $hash = @md5_file($fullPath);

        return $hash !== false ? substr($hash, 0, 12) : (string) filemtime($fullPath);
    }
}
