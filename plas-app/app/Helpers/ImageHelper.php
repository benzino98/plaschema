<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    /**
     * Normalize a stored image path to a disk-relative path (e.g. news/uuid.jpg).
     */
    public static function normalizePath(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        $path = trim($path);

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $path = ltrim($path, '/');

        return preg_replace('#^(?:storage/)+#', '', $path) ?: null;
    }

    /**
     * Format an image path for use in Blade components (disk-relative path).
     *
     * @param string|null $path The image path to format
     * @return string|null The formatted path or null if the input is null
     */
    public static function formatPath(?string $path): ?string
    {
        return self::normalizePath($path);
    }

    /**
     * Get a public URL for an image, served through Laravel when needed.
     *
     * @param string|null $path The image path
     * @return string|null The URL to the image or null if the input is null
     */
    public static function url(?string $path): ?string
    {
        $normalized = self::normalizePath($path);

        if ($normalized === null) {
            return null;
        }

        if (filter_var($normalized, FILTER_VALIDATE_URL)) {
            return $normalized;
        }

        if (Storage::disk('public')->exists($normalized)) {
            return route('media.serve', ['path' => $normalized]);
        }

        // Fallback for environments with a working public/storage symlink
        return asset('storage/' . $normalized);
    }

    /**
     * Pick the best available image URL from a list of responsive variants.
     *
     * @param  array<string, string|null>  $paths
     */
    public static function bestUrl(array $paths): ?string
    {
        foreach ($paths as $path) {
            $url = self::url($path);

            if ($url !== null) {
                return $url;
            }
        }

        return null;
    }
} 