<?php

namespace App\Helpers;

class ImageHelper
{
    /**
     * Format an image path to ensure it has the correct storage prefix
     *
     * @param string|null $path The image path to format
     * @return string|null The formatted path or null if the input is null
     */
    public static function formatPath(?string $path): ?string
    {
        // Return null if path is null
        if ($path === null || trim($path) === '') {
            return null;
        }

        // Trim the path
        $path = trim($path);
        
        // If it's a full URL, return it as is
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }
        
        // Remove any leading slash
        $path = ltrim($path, '/');
        
        // If it already starts with 'storage/', return it as is
        if (str_starts_with($path, 'storage/')) {
            return $path;
        }
        
        // Add 'storage/' prefix if it doesn't have it
        return 'storage/' . $path;
    }
    
    /**
     * Get the storage URL for an image
     *
     * @param string|null $path The image path
     * @return string|null The URL to the image or null if the input is null
     */
    public static function url(?string $path): ?string
    {
        $formattedPath = self::formatPath($path);
        
        if ($formattedPath === null) {
            return null;
        }
        
        // If it's already a full URL, return it as is
        if (filter_var($formattedPath, FILTER_VALIDATE_URL)) {
            return $formattedPath;
        }
        
        // Use asset() helper to generate the URL
        return asset($formattedPath);
    }
} 