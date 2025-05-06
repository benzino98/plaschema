@if ($style === 'dropdown')
    <div class="relative language-switcher dropdown">
        <button type="button" class="flex items-center gap-1 text-sm hover:text-primary focus:outline-none" id="language-menu-button" aria-expanded="false" aria-haspopup="true">
            <span class="fi fi-{{ $getLocaleFlag($currentLocale) }} mr-1"></span>
            <span>{{ __('general.language') }}</span>
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
        
        <div class="hidden absolute right-0 mt-2 py-1 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-10" 
             role="menu" 
             aria-orientation="vertical" 
             aria-labelledby="language-menu-button" 
             tabindex="-1" 
             id="language-menu">
            @foreach ($locales as $locale)
                <a href="{{ request()->fullUrlWithQuery(['lang' => $locale]) }}" 
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ $currentLocale === $locale ? 'bg-gray-100' : '' }}" 
                   role="menuitem">
                    <span class="fi fi-{{ $getLocaleFlag($locale) }} mr-2"></span>
                    {{ $getLocaleName($locale) }}
                </a>
            @endforeach
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const button = document.getElementById('language-menu-button');
            const menu = document.getElementById('language-menu');
            
            button.addEventListener('click', function() {
                menu.classList.toggle('hidden');
                button.setAttribute('aria-expanded', menu.classList.contains('hidden') ? 'false' : 'true');
            });
            
            document.addEventListener('click', function(event) {
                if (!button.contains(event.target) && !menu.contains(event.target)) {
                    menu.classList.add('hidden');
                    button.setAttribute('aria-expanded', 'false');
                }
            });
        });
    </script>
@else
    <div class="language-switcher inline flex items-center gap-3">
        <span class="text-sm text-gray-600">{{ __('general.language') }}:</span>
        <div class="flex items-center gap-2">
            @foreach ($locales as $locale)
                <a href="{{ request()->fullUrlWithQuery(['lang' => $locale]) }}" 
                   class="flex items-center gap-1 px-2 py-1 rounded {{ $currentLocale === $locale ? 'bg-primary-100 text-primary-600' : 'text-gray-700 hover:bg-gray-100' }}">
                    <span class="fi fi-{{ $getLocaleFlag($locale) }}"></span>
                    <span class="text-sm">{{ $getLocaleName($locale) }}</span>
                </a>
            @endforeach
        </div>
    </div>
@endif