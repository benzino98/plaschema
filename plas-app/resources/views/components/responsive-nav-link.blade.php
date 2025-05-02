@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-plaschema text-start text-base font-medium text-plaschema-dark bg-green-50 focus:outline-none focus:text-plaschema-dark focus:bg-green-100 focus:border-plaschema-dark transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-plaschema hover:bg-green-50 hover:border-plaschema focus:outline-none focus:text-plaschema focus:bg-green-50 focus:border-plaschema transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
