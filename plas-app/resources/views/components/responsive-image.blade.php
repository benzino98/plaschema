@props(['pathSmall' => null, 'pathMedium' => null, 'pathLarge' => null, 'pathOriginal' => null, 'alt' => '', 'class' => '', 'loading' => 'lazy', 'sizes' => '(max-width: 640px) 100vw, (max-width: 1024px) 800px, 1200px'])

@php
    // Additional attributes that aren't props
    $additionalAttributes = $attributes->except(['pathSmall', 'pathMedium', 'pathLarge', 'pathOriginal', 'alt', 'class', 'loading', 'sizes']);
    
    // Helper function to check if we have enough paths for srcset
    function hasSrcset() {
        return isset($pathSmall) || isset($pathMedium) || isset($pathLarge);
    }
    
    // Helper to generate the srcset attribute
    function getSrcset() {
        $srcset = [];
        
        if (isset($pathSmall)) {
            $srcset[] = asset('storage/' . $pathSmall) . ' 400w';
        }
        
        if (isset($pathMedium)) {
            $srcset[] = asset('storage/' . $pathMedium) . ' 800w';
        }
        
        if (isset($pathLarge)) {
            $srcset[] = asset('storage/' . $pathLarge) . ' 1200w';
        }
        
        return implode(', ', $srcset);
    }
    
    // Generate URLs for lazy loading
    $originalUrl = isset($pathOriginal) ? asset('storage/' . $pathOriginal) : '';
    $placeholderUrl = isset($pathSmall) ? asset('storage/' . $pathSmall) : $originalUrl;
    $srcsetValue = hasSrcset() ? getSrcset() : '';
@endphp

<div class="responsive-image {{ $class }}">
    @if($loading === 'lazy')
        {{-- Lazy loaded responsive image --}}
        <img
            src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 3'%3E%3C/svg%3E"
            data-src="{{ $originalUrl }}" 
            @if($srcsetValue)
            data-srcset="{{ $srcsetValue }}"
            sizes="{{ $sizes }}"
            @endif
            alt="{{ $alt }}"
            class="transition-opacity duration-300 opacity-0"
            loading="lazy"
            onload="this.classList.remove('opacity-0')"
            @foreach($additionalAttributes as $attribute => $value)
                {{ $attribute }}="{{ $value }}"
            @endforeach
        >
        <noscript>
            <img
                src="{{ $originalUrl }}" 
                @if($srcsetValue)
                srcset="{{ $srcsetValue }}"
                sizes="{{ $sizes }}"
                @endif
                alt="{{ $alt }}"
                class="{{ $class }}"
                @foreach($additionalAttributes as $attribute => $value)
                    {{ $attribute }}="{{ $value }}"
                @endforeach
            >
        </noscript>
    @else
        {{-- Standard responsive image (no lazy loading) --}}
        <img
            src="{{ $originalUrl }}" 
            @if($srcsetValue)
            srcset="{{ $srcsetValue }}"
            sizes="{{ $sizes }}"
            @endif
            alt="{{ $alt }}"
            class="{{ $class }}"
            loading="{{ $loading }}"
            @foreach($additionalAttributes as $attribute => $value)
                {{ $attribute }}="{{ $value }}"
            @endforeach
        >
    @endif
    
    {{-- Optional skeleton loader that disappears when image loads --}}
    <div class="skeleton-loader absolute inset-0 bg-gray-200 animate-pulse rounded" aria-hidden="true"></div>
</div>

<style>
    .responsive-image {
        position: relative;
        overflow: hidden;
    }
    
    .responsive-image img.loaded,
    .responsive-image img:not([loading="lazy"]) {
        opacity: 1;
    }
    
    .responsive-image img {
        position: relative;
        z-index: 2;
    }
    
    .responsive-image .skeleton-loader {
        z-index: 1;
        transition: opacity 0.3s ease-out;
    }
    
    .responsive-image img.loaded + .skeleton-loader,
    .responsive-image img:not([loading="lazy"]) + .skeleton-loader {
        opacity: 0;
    }
</style>