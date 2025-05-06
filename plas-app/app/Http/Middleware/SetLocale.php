<?php

namespace App\Http\Middleware;

use App\Services\TranslationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * The translation service instance.
     *
     * @var TranslationService
     */
    protected $translationService;

    /**
     * Create a new middleware instance.
     *
     * @param TranslationService $translationService
     * @return void
     */
    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Priority 1: URL parameter
        if ($request->has('lang')) {
            $locale = $request->input('lang');
            if ($this->isValidLocale($locale)) {
                Session::put('locale', $locale);
                App::setLocale($locale);
                
                // Set a cookie that lasts for 30 days
                return $next($request)->withCookie(cookie('locale', $locale, 60 * 24 * 30));
            }
        }
        
        // Priority 2: Session
        if (Session::has('locale') && $this->isValidLocale(Session::get('locale'))) {
            App::setLocale(Session::get('locale'));
            return $next($request);
        }
        
        // Priority 3: Cookie
        if ($request->hasCookie('locale') && $this->isValidLocale($request->cookie('locale'))) {
            App::setLocale($request->cookie('locale'));
            Session::put('locale', $request->cookie('locale'));
            return $next($request);
        }
        
        // Priority 4: Browser preference
        $browserLocales = $this->getBrowserLocales($request);
        foreach ($browserLocales as $browserLocale) {
            if ($this->isValidLocale($browserLocale)) {
                App::setLocale($browserLocale);
                Session::put('locale', $browserLocale);
                return $next($request)->withCookie(cookie('locale', $browserLocale, 60 * 24 * 30));
            }
        }
        
        // Priority 5: Default locale from config
        return $next($request);
    }

    /**
     * Check if the given locale is valid.
     *
     * @param string $locale
     * @return bool
     */
    protected function isValidLocale(string $locale): bool
    {
        $availableLocales = $this->translationService->getAvailableLocales();
        return in_array($locale, $availableLocales);
    }

    /**
     * Get browser locales from the Accept-Language header.
     *
     * @param Request $request
     * @return array
     */
    protected function getBrowserLocales(Request $request): array
    {
        $languages = [];
        
        if ($request->header('Accept-Language')) {
            $browserLanguages = explode(',', $request->header('Accept-Language'));
            
            foreach ($browserLanguages as $browserLanguage) {
                $parts = explode(';', $browserLanguage);
                $lang = trim($parts[0]);
                
                // Extract just the language code without country
                if (strpos($lang, '-') !== false) {
                    $langParts = explode('-', $lang);
                    $lang = $langParts[0];
                }
                
                $languages[] = strtolower($lang);
            }
        }
        
        return $languages;
    }
}
