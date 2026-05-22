@php
    $existingImages = isset($news) ? $news->images : collect();
    $existingCount = $existingImages->count();
    $maxImages = \App\Services\NewsImageService::MAX_IMAGES;
    $defaultCoverId = old('cover_image_id', $existingImages->firstWhere('is_cover', true)?->id ?? $existingImages->first()?->id);
@endphp

<div class="mb-6" id="news-gallery-section">
    <label class="block text-gray-700 text-sm font-bold mb-2">Article images</label>
    <p class="text-gray-500 text-xs mb-3">
        Upload up to {{ $maxImages }} images. Choose one as the cover (shown in listings and at the top of the article).
        Optional caption and link per image.
    </p>
    <p class="text-sm text-gray-600 mb-2" id="gallery-count-label">
        <span id="gallery-current-count">{{ $existingCount }}</span> / {{ $maxImages }} images
    </p>

    @if($existingImages->isNotEmpty())
    <div class="mb-4 space-y-4" id="existing-gallery">
        @foreach($existingImages as $image)
        @php
            $previewUrl = \App\Helpers\ImageHelper::bestUrl([
                $image->image_path_medium,
                $image->image_path,
                $image->image_path_small,
            ]);
        @endphp
        <div class="border rounded-lg p-4 flex flex-col sm:flex-row gap-4 existing-gallery-item" data-image-id="{{ $image->id }}">
            <div class="flex-shrink-0">
                @if($previewUrl)
                <img src="{{ $previewUrl }}" alt="" class="w-32 h-24 object-cover rounded">
                @endif
            </div>
            <div class="flex-grow space-y-2">
                <label class="flex items-center text-sm">
                    <input type="radio" name="cover_image_id" value="{{ $image->id }}"
                        {{ (string) $defaultCoverId === (string) $image->id ? 'checked' : '' }}
                        class="mr-2">
                    <span>Use as cover image</span>
                </label>
                <div>
                    <label class="text-xs text-gray-600">Caption (optional)</label>
                    <input type="text" name="image_captions[{{ $image->id }}]"
                        value="{{ old('image_captions.'.$image->id, $image->caption) }}"
                        class="shadow appearance-none border rounded w-full py-1 px-2 text-sm text-gray-700">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Link URL (optional)</label>
                    <input type="url" name="image_links[{{ $image->id }}]"
                        value="{{ old('image_links.'.$image->id, $image->link_url) }}"
                        placeholder="https://example.com"
                        class="shadow appearance-none border rounded w-full py-1 px-2 text-sm text-gray-700">
                </div>
                <label class="flex items-center text-sm text-red-600">
                    <input type="checkbox" name="remove_image_ids[]" value="{{ $image->id }}" class="mr-2 remove-image-checkbox">
                    Remove this image
                </label>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <div id="new-gallery-previews" class="mb-4 space-y-4"></div>

    <input type="file" name="gallery_images[]" id="gallery_images" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
        multiple class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
    <p class="text-gray-500 text-xs mt-1">JPEG, PNG, GIF, or WebP. Max 5MB each.</p>
    @error('gallery_images')
    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
    @error('gallery_images.*')
    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
</div>

@push('scripts')
<script>
(function () {
    const maxImages = {{ $maxImages }};
    const existingCount = {{ $existingCount }};
    const fileInput = document.getElementById('gallery_images');
    const previewContainer = document.getElementById('new-gallery-previews');
    const countLabel = document.getElementById('gallery-current-count');

    function countRemovals() {
        return document.querySelectorAll('.remove-image-checkbox:checked').length;
    }

    function updateCount(newFiles) {
        const remaining = existingCount - countRemovals();
        const total = remaining + newFiles;
        if (countLabel) {
            countLabel.textContent = total;
        }
    }

    fileInput?.addEventListener('change', function () {
        previewContainer.innerHTML = '';
        const files = Array.from(this.files || []);
        const remaining = existingCount - countRemovals();

        if (remaining + files.length > maxImages) {
            alert('You can only have ' + maxImages + ' images per article. Remove some images first.');
            this.value = '';
            updateCount(0);
            return;
        }

        files.forEach(function (file, index) {
            const row = document.createElement('div');
            row.className = 'border rounded-lg p-4 flex flex-col sm:flex-row gap-4';

            const preview = document.createElement('div');
            preview.className = 'flex-shrink-0';
            const img = document.createElement('img');
            img.className = 'w-32 h-24 object-cover rounded';
            img.src = URL.createObjectURL(file);
            preview.appendChild(img);

            const fields = document.createElement('div');
            fields.className = 'flex-grow space-y-2';
            fields.innerHTML =
                '<label class="flex items-center text-sm">' +
                '<input type="radio" name="cover_image_id" value="new_' + index + '" class="mr-2"' +
                (index === 0 && existingCount - countRemovals() === 0 ? ' checked' : '') + '>' +
                '<span>Use as cover image</span></label>' +
                '<div><label class="text-xs text-gray-600">Caption (optional)</label>' +
                '<input type="text" name="new_image_captions[' + index + ']" class="shadow appearance-none border rounded w-full py-1 px-2 text-sm text-gray-700"></div>' +
                '<div><label class="text-xs text-gray-600">Link URL (optional)</label>' +
                '<input type="url" name="new_image_links[' + index + ']" placeholder="https://example.com" class="shadow appearance-none border rounded w-full py-1 px-2 text-sm text-gray-700"></div>';

            row.appendChild(preview);
            row.appendChild(fields);
            previewContainer.appendChild(row);
        });

        updateCount(files.length);
    });

    document.querySelectorAll('.remove-image-checkbox').forEach(function (cb) {
        cb.addEventListener('change', function () {
            updateCount((fileInput?.files || []).length);
        });
    });
})();
</script>
@endpush
