@props(['title' => '', 'subtitle' => '', 'background' => 'white', 'spacing' => 'py-16'])

@php
    $bgClasses = [
        'white' => 'bg-white',
        'light' => 'bg-gray-50',
        'dark' => 'bg-gray-900 text-white',
        'primary' => 'bg-plaschema-light text-gray-800',
        'secondary' => 'bg-plaschema-dark text-white',
    ];
    
    $bgClass = $bgClasses[$background] ?? $bgClasses['white'];
@endphp

<section {{ $attributes->merge(['class' => $bgClass . ' ' . $spacing]) }}>
    <div class="container mx-auto px-4">
        @if($title)
            <div class="mb-12 text-center">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ $title }}</h2>
                @if($subtitle)
                    <p class="text-xl max-w-3xl mx-auto {{ $background == 'dark' ? 'text-gray-300' : 'text-gray-600' }}">{{ $subtitle }}</p>
                @endif
            </div>
        @endif
        
        {{ $slot }}
    </div>
</section> 