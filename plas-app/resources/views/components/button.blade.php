@props(['variant' => 'primary', 'href' => null])

@php
    $baseClasses = 'inline-flex items-center justify-center px-4 py-2 rounded transition duration-150 ease-in-out hover-shadow-md button-push';
    $variantClasses = [
        'primary' => 'bg-plaschema text-white hover:bg-plaschema-dark shadow-sm',
        'secondary' => 'bg-plaschema-dark text-white hover:bg-plaschema shadow-sm',
        'outline' => 'border border-plaschema text-plaschema hover:bg-plaschema hover:text-white shadow-sm',
        'text' => 'bg-transparent text-plaschema hover:text-plaschema-dark hover-glow px-0',
    ];
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $baseClasses . ' ' . $variantClasses[$variant]]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $baseClasses . ' ' . $variantClasses[$variant]]) }}>
        {{ $slot }}
    </button>
@endif 