@props(['type' => 'button'])

<button {{ $attributes->merge([
    'type' => $type,
    'class' => 'inline-flex items-center justify-center px-6 py-3 border border-transparent 
               rounded-md font-semibold text-white bg-blue-600 hover:bg-blue-700 
               active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 
               focus:ring-blue-500 transition duration-300 ease-in-out 
               transform hover:scale-[1.02] active:scale-[0.98] shadow-md hover:shadow-lg'
]) }}>
    {{ $slot }}
</button> 