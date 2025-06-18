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