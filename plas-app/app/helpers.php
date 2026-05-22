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

        $formatted = app(\App\Services\FaqContentService::class)->formatForDisplay($content, 'news');

        return normalize_news_content_paragraphs($formatted);
    }
}

if (! function_exists('normalize_news_content_paragraphs')) {
    /**
     * Ensure article body uses separate paragraph blocks for spacing.
     */
    function normalize_news_content_paragraphs(string $html): string
    {
        $html = trim($html);

        if ($html === '') {
            return '';
        }

        if (preg_match_all('/<p\b[^>]*>/i', $html) >= 2) {
            return $html;
        }

        if (! preg_match('/<p\b/i', $html) && preg_match('/<br\s*\/?>/i', $html)) {
            $chunks = preg_split('/(?:<br\s*\/?>\s*){2,}/i', $html);
            $chunks = array_values(array_filter(array_map('trim', $chunks ?: []), function (string $chunk): bool {
                return $chunk !== '' && trim(strip_tags($chunk)) !== '';
            }));

            if (count($chunks) > 1) {
                $paragraphs = [];

                foreach ($chunks as $chunk) {
                    $paragraphs[] = '<p>'.$chunk.'</p>';
                }

                return implode("\n", $paragraphs);
            }
        }

        $inner = $html;

        if (preg_match('/^<p\b[^>]*>(.*)<\/p>$/is', $html, $matches)) {
            $inner = $matches[1];
        }

        $chunks = preg_split('/(?:<br\s*\/?>\s*){2,}|\r\n\r\n|\n\n/', $inner);
        $chunks = array_values(array_filter(array_map('trim', $chunks ?: []), function (string $chunk): bool {
            return $chunk !== '' && trim(strip_tags($chunk)) !== '';
        }));

        if (count($chunks) <= 1) {
            return $html;
        }

        $paragraphs = [];

        foreach ($chunks as $chunk) {
            if (preg_match('/^<p\b/i', $chunk)) {
                $paragraphs[] = $chunk;
            } else {
                $paragraphs[] = '<p>'.$chunk.'</p>';
            }
        }

        return implode("\n", $paragraphs);
    }
}

if (! function_exists('split_news_content_after_paragraphs')) {
    /**
     * Split formatted HTML so a gallery can be injected after N paragraphs.
     *
     * @return array{before: string, after: string}
     */
    function split_news_content_after_paragraphs(string $html, int $paragraphCount = 2): array
    {
        $html = trim($html);

        if ($html === '' || $paragraphCount < 1) {
            return ['before' => $html, 'after' => ''];
        }

        $count = 0;
        $offset = 0;
        $splitAt = null;

        while (preg_match('/<\/p>/i', $html, $matches, PREG_OFFSET_CAPTURE, $offset)) {
            $count++;
            $splitAt = $matches[0][1] + strlen($matches[0][0]);

            if ($count >= $paragraphCount) {
                break;
            }

            $offset = $splitAt;
        }

        if ($splitAt === null || $count < $paragraphCount) {
            return ['before' => $html, 'after' => ''];
        }

        return [
            'before' => substr($html, 0, $splitAt),
            'after' => substr($html, $splitAt),
        ];
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