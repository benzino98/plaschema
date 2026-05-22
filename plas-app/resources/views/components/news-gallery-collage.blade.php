@props(['images', 'title' => ''])

@if($images->isNotEmpty())
<div class="news-collage my-10" aria-label="Photo gallery">
    @foreach($images as $index => $galleryImage)
        @php
            $galleryUrl = ImageHelper::bestUrl([
                $galleryImage->image_path_large,
                $galleryImage->image_path_medium,
                $galleryImage->image_path,
                $galleryImage->image_path_small,
            ]);
            $layoutClass = 'news-collage__item--'.(($index % 8) + 1);
        @endphp
        @if($galleryUrl)
        <figure class="news-collage__item {{ $layoutClass }} group">
            @if($galleryImage->link_url)
            <a href="{{ $galleryImage->link_url }}" target="_blank" rel="noopener noreferrer" class="news-collage__link block w-full h-full">
                <img
                    src="{{ $galleryUrl }}"
                    alt="{{ $galleryImage->caption ?: $title }}"
                    class="news-collage__img"
                    loading="lazy"
                    decoding="async"
                >
            </a>
            @else
            <img
                src="{{ $galleryUrl }}"
                alt="{{ $galleryImage->caption ?: $title }}"
                class="news-collage__img"
                loading="lazy"
                decoding="async"
            >
            @endif
            @if($galleryImage->caption)
            <figcaption class="news-collage__caption">{{ $galleryImage->caption }}</figcaption>
            @endif
        </figure>
        @endif
    @endforeach
</div>
@endif
