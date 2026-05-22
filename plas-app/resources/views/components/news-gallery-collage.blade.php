@props(['images', 'title' => '', 'lightboxStartIndex' => 0])

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
            $lightboxIndex = $lightboxStartIndex + $index;
        @endphp
        @if($galleryUrl)
        <figure class="news-collage__item {{ $layoutClass }} group">
            <button
                type="button"
                class="news-collage__trigger news-lightbox-open w-full h-full border-0 p-0 cursor-zoom-in bg-transparent"
                data-news-lightbox-index="{{ $lightboxIndex }}"
                aria-label="View image {{ $index + 1 }}"
            >
                <img
                    src="{{ $galleryUrl }}"
                    alt="{{ $galleryImage->caption ?: $title }}"
                    class="news-collage__img"
                    loading="lazy"
                    decoding="async"
                >
            </button>
            @if($galleryImage->link_url)
            <a
                href="{{ $galleryImage->link_url }}"
                target="_blank"
                rel="noopener noreferrer"
                class="news-collage__external"
                title="Open related link"
                aria-label="Open related link in new tab"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
            </a>
            @endif
            @if($galleryImage->caption)
            <figcaption class="news-collage__caption">{{ $galleryImage->caption }}</figcaption>
            @endif
        </figure>
        @endif
    @endforeach
</div>
@endif
