@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-plaschema text-start text-base font-medium text-plaschema-dark bg-green-50 focus:outline-none focus:text-plaschema-dark focus:bg-green-100 focus:border-plaschema-dark transition-all duration-300 ease-in-out transform hover:translate-x-1 nav-hover-glow'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-plaschema hover:bg-green-50 hover:border-plaschema focus:outline-none focus:text-plaschema focus:bg-green-50 focus:border-plaschema transition-all duration-300 ease-in-out transform hover:translate-x-1 nav-hover-glow';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
