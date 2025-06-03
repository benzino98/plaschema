@props(['disabled' => false, 'icon' => null])

<div class="relative">
    @if($icon)
    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
        {{ $icon }}
    </div>
    <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'form-input-focus-effect rounded-md shadow-sm border-gray-300 focus:border-blue-500 focus:ring-blue-500 w-full pl-10 transition duration-300 ease-in-out']) !!}>
    @else
    <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'form-input-focus-effect rounded-md shadow-sm border-gray-300 focus:border-blue-500 focus:ring-blue-500 w-full transition duration-300 ease-in-out']) !!}>
    @endif
</div> 