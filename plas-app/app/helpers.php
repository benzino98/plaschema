<?php

/**
 * Custom helper functions for the application
 */

if (!function_exists('safe_storage_path')) {
    /**
     * Get the path to the storage directory with fallback to environment variable.
     *
     * @param  string  $path
     * @return string
     */
    function safe_storage_path($path = '')
    {
        // Check if we have an environment variable for storage path
        $storagePath = getenv('STORAGE_PATH');
        
        if ($storagePath) {
            return $storagePath.($path ? DIRECTORY_SEPARATOR.$path : $path);
        }
        
        // Fall back to Laravel's default storage_path
        return storage_path($path);
    }
}

if (! function_exists('format_faq_answer')) {
    /**
     * Render a FAQ answer with support for safe links and basic formatting.
     */
    function format_faq_answer(?string $answer): string
    {
        if ($answer === null || trim($answer) === '') {
            return '';
        }

        return app(\App\Services\FaqContentService::class)->formatForDisplay($answer);
    }
}

if (! function_exists('format_news_content')) {
    /**
     * Render news article body with safe links and rich formatting.
     */
    function format_news_content(?string $content): string
    {
        if ($content === null || trim($content) === '') {
            return '';
        }

        return app(\App\Services\FaqContentService::class)->formatForDisplay($content, 'news');
    }
}

if (!function_exists('safe_log_path')) {
    /**
     * Get the path to the logs directory with fallback to environment variable.
     *
     * @param  string  $path
     * @return string
     */
    function safe_log_path($path = '')
    {
        // Check if we have an environment variable for log path
        $logPath = getenv('LOG_PATH');
        
        if ($logPath) {
            return $logPath.($path ? DIRECTORY_SEPARATOR.$path : $path);
        }
        
        // Fall back to storage_path/logs
        return safe_storage_path('logs'.($path ? DIRECTORY_SEPARATOR.$path : $path));
    }
} 