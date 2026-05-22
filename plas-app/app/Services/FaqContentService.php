<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMNode;

class FaqContentService
{
    /**
     * Format FAQ answer HTML for safe display on the public site.
     */
    public function formatForDisplay(string $answer): string
    {
        $answer = trim($answer);

        if ($answer === '') {
            return '';
        }

        if ($this->containsHtml($answer)) {
            return $this->sanitizeHtml($answer);
        }

        return nl2br(e($answer));
    }

    /**
     * Sanitize answer HTML before saving to the database.
     */
    public function sanitizeForStorage(string $answer): string
    {
        $answer = trim($answer);

        if ($answer === '' || ! $this->containsHtml($answer)) {
            return $answer;
        }

        return $this->sanitizeHtml($answer);
    }

    /**
     * Site pages admins can link to from FAQ answers.
     *
     * @return array<string, string>
     */
    public function sitePageOptions(): array
    {
        return [
            'Home' => route('home'),
            'About Us' => route('about'),
            'Health Plans' => route('plans'),
            'Healthcare Providers' => route('providers.index'),
            'News' => route('news'),
            'Resources' => route('resources.index'),
            'FAQs' => route('faq'),
            'Contact' => route('contact'),
        ];
    }

    protected function containsHtml(string $text): bool
    {
        return $text !== strip_tags($text);
    }

    protected function sanitizeHtml(string $html): string
    {
        $allowed = '<p><br><a><strong><b><em><i><ul><ol><li>';
        $html = strip_tags($html, $allowed);

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML(
            '<?xml encoding="utf-8"><div>'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        /** @var DOMNode $anchor */
        foreach ($dom->getElementsByTagName('a') as $anchor) {
            if (! $anchor instanceof DOMElement) {
                continue;
            }

            $href = trim(html_entity_decode($anchor->getAttribute('href'), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

            if (! $this->isSafeHref($href)) {
                $this->unwrapAnchor($anchor);
                continue;
            }

            $anchor->setAttribute('href', $href);
            $anchor->setAttribute('class', 'text-plaschema hover:text-plaschema-dark underline font-medium');

            $target = strtolower($anchor->getAttribute('target'));
            $openInNewTab = $target === '_blank' || $this->shouldOpenInNewTab($href);

            if ($openInNewTab) {
                $anchor->setAttribute('target', '_blank');
                $anchor->setAttribute('rel', 'noopener noreferrer');
            } else {
                $anchor->removeAttribute('target');
                $anchor->removeAttribute('rel');
            }
        }

        $container = $dom->getElementsByTagName('div')->item(0);

        if (! $container) {
            return e($html);
        }

        $output = '';

        foreach ($container->childNodes as $child) {
            $output .= $dom->saveHTML($child);
        }

        return $output;
    }

    protected function isSafeHref(string $href): bool
    {
        if ($href === '') {
            return false;
        }

        $lower = strtolower($href);

        if (str_starts_with($lower, 'javascript:') || str_starts_with($lower, 'data:') || str_starts_with($lower, 'vbscript:')) {
            return false;
        }

        if (str_starts_with($href, '/') || str_starts_with($href, '#')) {
            return true;
        }

        if (preg_match('#^https?://#i', $href)) {
            return true;
        }

        if (str_starts_with($lower, 'mailto:')) {
            return filter_var(substr($href, 7), FILTER_VALIDATE_EMAIL) !== false;
        }

        return false;
    }

    protected function shouldOpenInNewTab(string $href): bool
    {
        if (str_starts_with($href, '/') || str_starts_with($href, '#')) {
            return false;
        }

        if (! preg_match('#^https?://#i', $href)) {
            return false;
        }

        $host = parse_url($href, PHP_URL_HOST);

        if (! $host) {
            return false;
        }

        $appHost = parse_url(config('app.url'), PHP_URL_HOST);

        return $appHost && strcasecmp($host, $appHost) !== 0;
    }

    protected function unwrapAnchor(DOMElement $anchor): void
    {
        $parent = $anchor->parentNode;

        if (! $parent) {
            return;
        }

        while ($anchor->firstChild) {
            $parent->insertBefore($anchor->firstChild, $anchor);
        }

        $parent->removeChild($anchor);
    }
}
