@props([
    'title' => '', 
    'image' => null, 
    'imageSmall' => null,
    'imageMedium' => null,
    'imageLarge' => null,
    'icon' => null, 
    'animation' => 'fade-in', 
    'url' => null,
    'aspectRatio' => '16/9', // Default aspect ratio for card images
    'objectPosition' => 'center 30%' // Default object position - slightly toward the top for better face visibility
])

@php
/**
 * Format image URL if it's not already a full URL
 */
$formatCardImageUrl = function($path) {
    if (!$path) {
        return null;
    }
    
    // If it's already a full URL, return as is
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }
    
    // If it already has asset path, return as is
    if (str_starts_with($path, '/storage/') || str_starts_with($path, 'storage/')) {
        return asset($path);
    }
    
    // Otherwise, assume it's a relative path in storage and prefix it
    return asset('storage/' . $path);
};
@endphp

<div {{ $attributes->merge(['class' => 'card rounded-lg shadow-md card-hover p-6 md:p-8 animate-on-scroll']) }} data-animation="{{ $animation }}">
    @if($image)
        <div class="mb-4 overflow-hidden rounded-lg -mt-6 -mx-6 md:-mx-8 md:-mt-8">
            <div class="aspect-ratio-container" style="aspect-ratio: {{ $aspectRatio }};">
                @if($url)
                    <a href="{{ $url }}" class="block w-full h-full">
                        @if($imageSmall || $imageMedium || $imageLarge)
                            <x-responsive-image
                                :path-small="$imageSmall"
                                :path-medium="$imageMedium"
                                :path-large="$imageLarge"
                                :path-original="$image"
                                :alt="$title"
                                class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                                loading="lazy"
                                style="object-position: {{ $objectPosition }};"
                            />
                        @else
                            <img src="{{ $formatCardImageUrl($image) }}" alt="{{ $title }}" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105" loading="lazy" style="object-position: {{ $objectPosition }};">
                        @endif
                    </a>
                @else
                    @if($imageSmall || $imageMedium || $imageLarge)
                        <x-responsive-image
                            :path-small="$imageSmall"
                            :path-medium="$imageMedium"
                            :path-large="$imageLarge"
                            :path-original="$image"
                            :alt="$title"
                            class="w-full h-full object-cover"
                            loading="lazy"
                            style="object-position: {{ $objectPosition }};"
                        />
                    @else
                        <img src="{{ $formatCardImageUrl($image) }}" alt="{{ $title }}" class="w-full h-full object-cover" loading="lazy" style="object-position: {{ $objectPosition }};">
                    @endif
                @endif
            </div>
        </div>
    @endif

    @if($icon)
        <div class="mb-4 text-plaschema">
            {!! $icon !!}
        </div>
    @endif

    @if($title)
        @if($url)
            <h3 class="text-xl font-bold mb-4">
                <a href="{{ $url }}" class="hover:text-plaschema transition-colors">{{ $title }}</a>
            </h3>
        @else
            <h3 class="text-xl font-bold mb-4">{{ $title }}</h3>
        @endif
    @endif

    <div class="card-content">
        {{ $slot }}
    </div>
</div>

<style>
    .aspect-ratio-container {
        position: relative;
        width: 100%;
        overflow: hidden;
    }
    
    .aspect-ratio-container > * {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
</style> 