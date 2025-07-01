@props(['pathSmall' => null, 'pathMedium' => null, 'pathLarge' => null, 'pathOriginal' => null, 'alt' => '', 'class' => '', 'loading' => 'lazy', 'sizes' => '(max-width: 640px) 100vw, (max-width: 1024px) 800px, 1200px', 'objectPosition' => 'center 30%'])

@php
    // Additional attributes that aren't props
    $additionalAttributes = $attributes->except(['pathSmall', 'pathMedium', 'pathLarge', 'pathOriginal', 'alt', 'class', 'loading', 'sizes', 'objectPosition']);
    
    // Helper function to create correct URL without duplication
    $formatImageUrl = function($path) {
        if (!$path) return '';
        
        // Check if this is already a full URL
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        
        // Check if it already has 'storage/' prefix to avoid duplication
        if (str_starts_with($path, 'storage/') || str_starts_with($path, '/storage/')) {
            return asset(ltrim($path, '/'));
        }
        
        // Standard path, add storage prefix
        return asset('storage/' . $path);
    };
    
    // Generate URLs for lazy loading
    $originalUrl = isset($pathOriginal) ? $formatImageUrl($pathOriginal) : '';
    $placeholderUrl = isset($pathSmall) ? $formatImageUrl($pathSmall) : $originalUrl;
    
    // Generate srcset attribute
    $hasSrcset = false;
    $srcsetValue = '';
    
    if (isset($pathSmall) || isset($pathMedium) || isset($pathLarge)) {
        $hasSrcset = true;
        $srcset = [];
        
        if (isset($pathSmall)) {
            $srcset[] = $formatImageUrl($pathSmall) . ' 400w';
        }
        
        if (isset($pathMedium)) {
            $srcset[] = $formatImageUrl($pathMedium) . ' 800w';
        }
        
        if (isset($pathLarge)) {
            $srcset[] = $formatImageUrl($pathLarge) . ' 1200w';
        }
        
        $srcsetValue = implode(', ', $srcset);
    }
    
    // Create style attribute with object-position
    $style = "object-position: {$objectPosition};";
@endphp

<div class="responsive-image {{ $class }}">
    @if($loading === 'lazy')
        {{-- Lazy loaded responsive image --}}
        <img
            src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 3'%3E%3C/svg%3E"
            data-src="{{ $originalUrl }}" 
            @if($hasSrcset)
            data-srcset="{{ $srcsetValue }}"
            sizes="{{ $sizes }}"
            @endif
            alt="{{ $alt }}"
            class="transition-opacity duration-300 opacity-0 w-full h-full object-cover"
            loading="lazy"
            onload="this.classList.remove('opacity-0')"
            style="{{ $style }}"
            @foreach($additionalAttributes as $attribute => $value)
                {{ $attribute }}="{{ $value }}"
            @endforeach
        >
        <noscript>
            <img
                src="{{ $originalUrl }}" 
                @if($hasSrcset)
                srcset="{{ $srcsetValue }}"
                sizes="{{ $sizes }}"
                @endif
                alt="{{ $alt }}"
                class="w-full h-full object-cover"
                style="{{ $style }}"
                @foreach($additionalAttributes as $attribute => $value)
                    {{ $attribute }}="{{ $value }}"
                @endforeach
            >
        </noscript>
    @else
        {{-- Standard responsive image (no lazy loading) --}}
        <img
            src="{{ $originalUrl }}" 
            @if($hasSrcset)
            srcset="{{ $srcsetValue }}"
            sizes="{{ $sizes }}"
            @endif
            alt="{{ $alt }}"
            class="w-full h-full object-cover"
            loading="{{ $loading }}"
            style="{{ $style }}"
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
        width: 100%;
        height: 100%;
    }
    
    .responsive-image img.loaded,
    .responsive-image img:not([loading="lazy"]) {
        opacity: 1;
    }
    
    .responsive-image img {
        position: relative;
        z-index: 2;
        width: 100%;
        height: 100%;
        object-fit: cover;
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