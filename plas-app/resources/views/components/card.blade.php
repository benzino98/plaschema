@props(['title' => '', 'image' => null, 'icon' => null, 'animation' => 'fade-in', 'url' => null])

<div {{ $attributes->merge(['class' => 'card rounded-lg shadow-md p-6 md:p-8 ' . $animation]) }}>
    @if($image)
        <div class="mb-4 overflow-hidden rounded-lg -mt-6 -mx-6 md:-mx-8 md:-mt-8">
            @if($url)
                <a href="{{ $url }}">
                    <img src="{{ $image }}" alt="{{ $title }}" class="w-full h-auto object-cover transition-transform duration-300 hover:scale-105" loading="lazy">
                </a>
            @else
                <img src="{{ $image }}" alt="{{ $title }}" class="w-full h-auto object-cover" loading="lazy">
            @endif
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