@props(['slides' => []])

@if(count($slides) > 0)
<div
    id="news-lightbox"
    class="news-lightbox"
    role="dialog"
    aria-modal="true"
    aria-hidden="true"
    hidden
>
    <div class="news-lightbox__backdrop" data-news-lightbox-close></div>
    <div class="news-lightbox__panel">
        <button type="button" class="news-lightbox__close" data-news-lightbox-close aria-label="Close image viewer">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <button type="button" class="news-lightbox__nav news-lightbox__nav--prev" data-news-lightbox-prev aria-label="Previous image">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        <button type="button" class="news-lightbox__nav news-lightbox__nav--next" data-news-lightbox-next aria-label="Next image">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
        <figure class="news-lightbox__figure">
            <img id="news-lightbox-image" src="" alt="" class="news-lightbox__image">
            <figcaption id="news-lightbox-caption" class="news-lightbox__caption"></figcaption>
            <a id="news-lightbox-external" href="#" target="_blank" rel="noopener noreferrer" class="news-lightbox__external hidden">Open related link</a>
        </figure>
        <p id="news-lightbox-counter" class="news-lightbox__counter"></p>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const slides = @json(array_values($slides));

    if (!slides.length) {
        return;
    }

    const lightbox = document.getElementById('news-lightbox');
    const imageEl = document.getElementById('news-lightbox-image');
    const captionEl = document.getElementById('news-lightbox-caption');
    const counterEl = document.getElementById('news-lightbox-counter');
    const externalEl = document.getElementById('news-lightbox-external');
    const prevBtn = lightbox?.querySelector('[data-news-lightbox-prev]');
    const nextBtn = lightbox?.querySelector('[data-news-lightbox-next]');
    let currentIndex = 0;

    function renderSlide(index) {
        const slide = slides[index];

        if (!slide || !imageEl) {
            return;
        }

        currentIndex = index;
        imageEl.src = slide.src;
        imageEl.alt = slide.caption || 'News image';

        if (captionEl) {
            captionEl.textContent = slide.caption || '';
            captionEl.hidden = !slide.caption;
        }

        if (counterEl) {
            counterEl.textContent = (index + 1) + ' / ' + slides.length;
        }

        if (externalEl) {
            if (slide.external) {
                externalEl.href = slide.external;
                externalEl.classList.remove('hidden');
            } else {
                externalEl.classList.add('hidden');
            }
        }

        if (prevBtn) {
            prevBtn.disabled = index === 0;
        }

        if (nextBtn) {
            nextBtn.disabled = index === slides.length - 1;
        }
    }

    function openLightbox(index) {
        if (!lightbox) {
            return;
        }

        renderSlide(index);
        lightbox.hidden = false;
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.classList.add('news-lightbox-open');
        lightbox.querySelector('[data-news-lightbox-close]')?.focus();
    }

    function closeLightbox() {
        if (!lightbox) {
            return;
        }

        lightbox.hidden = true;
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('news-lightbox-open');
        imageEl.src = '';
    }

    document.querySelectorAll('.news-lightbox-open').forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            const index = parseInt(trigger.getAttribute('data-news-lightbox-index') || '0', 10);
            openLightbox(index);
        });
    });

    lightbox?.querySelectorAll('[data-news-lightbox-close]').forEach(function (el) {
        el.addEventListener('click', closeLightbox);
    });

    prevBtn?.addEventListener('click', function () {
        if (currentIndex > 0) {
            renderSlide(currentIndex - 1);
        }
    });

    nextBtn?.addEventListener('click', function () {
        if (currentIndex < slides.length - 1) {
            renderSlide(currentIndex + 1);
        }
    });

    document.addEventListener('keydown', function (event) {
        if (lightbox?.hidden) {
            return;
        }

        if (event.key === 'Escape') {
            closeLightbox();
        }

        if (event.key === 'ArrowLeft' && currentIndex > 0) {
            renderSlide(currentIndex - 1);
        }

        if (event.key === 'ArrowRight' && currentIndex < slides.length - 1) {
            renderSlide(currentIndex + 1);
        }
    });
})();
</script>
@endpush
@endif
