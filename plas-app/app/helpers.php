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

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML(
            '<?xml encoding="utf-8"><div id="news-content-wrap">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        $wrap = $dom->getElementById('news-content-wrap');

        if (! $wrap) {
            return ['before' => $html, 'after' => ''];
        }

        $beforeParts = [];
        $afterParts = [];
        $paragraphsSeen = 0;
        $splitDone = false;

        foreach ($wrap->childNodes as $child) {
            $chunk = $dom->saveHTML($child);

            if ($child->nodeType !== XML_ELEMENT_NODE && trim($chunk) === '') {
                continue;
            }

            if (! $splitDone && strtolower($child->nodeName) === 'p') {
                $paragraphsSeen++;

                if ($paragraphsSeen <= $paragraphCount) {
                    $beforeParts[] = $chunk;
                } else {
                    $splitDone = true;
                    $afterParts[] = $chunk;
                }

                continue;
            }

            if ($splitDone || $paragraphsSeen >= $paragraphCount) {
                $splitDone = true;
                $afterParts[] = $chunk;
            } else {
                $beforeParts[] = $chunk;
            }
        }

        if ($paragraphsSeen === 0) {
            return ['before' => $html, 'after' => ''];
        }

        return [
            'before' => implode('', $beforeParts),
            'after' => implode('', $afterParts),
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