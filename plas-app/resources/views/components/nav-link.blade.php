@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-plaschema text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-plaschema-dark transition-all duration-300 ease-in-out nav-item-animation nav-hover-glow'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-plaschema focus:outline-none focus:text-plaschema transition-all duration-300 ease-in-out nav-item-animation nav-underline-animation nav-hover-glow';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
