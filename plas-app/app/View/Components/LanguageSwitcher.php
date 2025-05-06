<?php

namespace App\View\Components;

use App\Services\TranslationService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Illuminate\View\Component;

class LanguageSwitcher extends Component
{
    /**
     * The list of available locales.
     *
     * @var array
     */
    public $locales;

    /**
     * The current locale.
     *
     * @var string
     */
    public $currentLocale;
    
    /**
     * The dropdown style.
     *
     * @var string
     */
    public $style;
    
    /**
     * Create a new component instance.
     *
     * @param TranslationService $translationService
     * @param string $style The switcher style ('dropdown' or 'inline')
     */
    public function __construct(TranslationService $translationService, $style = 'dropdown')
    {
        $this->locales = $translationService->getAvailableLocales();
        $this->currentLocale = App::getLocale();
        $this->style = $style;
    }
    
    /**
     * Get the locale display name.
     *
     * @param string $locale
     * @return string
     */
    public function getLocaleName($locale)
    {
        $names = [
            'en' => 'English',
            'fr' => 'Français',
            'ig' => 'Igbo',
            // Add more languages as needed
        ];
        
        return $names[$locale] ?? $locale;
    }
    
    /**
     * Get the locale flag code.
     *
     * @param string $locale
     * @return string
     */
    public function getLocaleFlag($locale)
    {
        $flags = [
            'en' => 'gb', // Use GB flag for English
            'fr' => 'fr',
            'ig' => 'ng', // Use Nigeria flag for Igbo
            // Add more language-to-flag mappings as needed
        ];
        
        return $flags[$locale] ?? $locale;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.language-switcher');
    }
}
