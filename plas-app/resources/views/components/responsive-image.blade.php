@if($hasSrcset())
    {{-- Use responsive image with srcset --}}
    <img
        src="{{ $pathOriginal }}" 
        srcset="{{ $getSrcset() }}"
        sizes="(max-width: 640px) 100vw, (max-width: 1024px) 800px, 1200px"
        alt="{{ $alt }}"
        class="{{ $class }}"
        loading="{{ $loading }}"
        @foreach($additionalAttributes as $attribute => $value)
            {{ $attribute }}="{{ $value }}"
        @endforeach
    >
@else
    {{-- Fallback to standard image --}}
    <img
        src="{{ $pathOriginal ?? '' }}"
        alt="{{ $alt }}"
        class="{{ $class }}"
        loading="{{ $loading }}"
        @foreach($additionalAttributes as $attribute => $value)
            {{ $attribute }}="{{ $value }}"
        @endforeach
    >
@endif