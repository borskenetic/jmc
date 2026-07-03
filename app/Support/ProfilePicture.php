<?php

namespace App\Support;

/**
 * Normalize legacy profile picture paths (bare filenames) to images/profile_pictures/…
 */
class ProfilePicture
{
    public static function relativePath(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        $path = str_replace('\\', '/', trim($path));

        if (
            str_starts_with($path, 'images/')
            || str_starts_with($path, 'http://')
            || str_starts_with($path, 'https://')
        ) {
            return $path;
        }

        return 'images/profile_pictures/'.ltrim($path, '/');
    }

    public static function url(?string $path): ?string
    {
        $relative = self::relativePath($path);

        return $relative !== null ? asset($relative) : null;
    }

    /** Filesystem path for Intervention Image / unlink (public/ first, then project root). */
    public static function absolutePath(?string $path): ?string
    {
        $relative = self::relativePath($path);

        if ($relative === null) {
            return null;
        }

        $public = public_path($relative);
        if (is_file($public)) {
            return $public;
        }

        $base = base_path($relative);

        return is_file($base) ? $base : $base;
    }
}
