<?php

namespace App\Services;

use App\Models\News;
use App\Models\NewsImage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class NewsImageService
{
    public const MAX_IMAGES = 10;

    public function __construct(
        protected ImageService $imageService
    ) {}

    /**
     * Create a gallery row from legacy featured image paths on the news record.
     */
    public function importLegacyFeaturedImage(News $news): void
    {
        if ($news->images()->exists() || ! $news->image_path) {
            return;
        }

        NewsImage::create([
            'news_id' => $news->id,
            'image_path' => $news->image_path,
            'image_path_small' => $news->image_path_small,
            'image_path_medium' => $news->image_path_medium,
            'image_path_large' => $news->image_path_large,
            'sort_order' => 0,
            'is_cover' => true,
        ]);
    }

    /**
     * Sync gallery uploads, metadata, removals, and cover selection.
     */
    public function syncGallery(News $news, Request $request): void
    {
        $this->importLegacyFeaturedImage($news);

        $removeIds = array_map('intval', (array) $request->input('remove_image_ids', []));
        $captions = (array) $request->input('image_captions', []);
        $links = (array) $request->input('image_links', []);
        $coverId = $request->input('cover_image_id');
        $newCoverIndex = $request->has('new_cover_index') ? (int) $request->input('new_cover_index') : null;

        $ownedImageIds = $news->images()->pluck('id')->all();
        $removeIds = array_values(array_intersect($removeIds, $ownedImageIds));

        foreach ($news->images()->orderBy('sort_order')->get() as $image) {
            if (in_array($image->id, $removeIds, true)) {
                $this->deleteImageFiles($image);
                $image->delete();
                continue;
            }

            $image->caption = $this->nullableString($captions[$image->id] ?? null);
            $image->link_url = $this->sanitizeLinkUrl($links[$image->id] ?? null);
            $image->save();
        }

        if ($coverId !== null && ! str_starts_with((string) $coverId, 'new_')) {
            $news->images()->update(['is_cover' => false]);
            $news->images()->where('id', $coverId)->update(['is_cover' => true]);
        }

        $newFiles = $request->file('gallery_images', []);
        $newCaptions = (array) $request->input('new_image_captions', []);
        $newLinks = (array) $request->input('new_image_links', []);
        $maxSort = (int) $news->images()->max('sort_order');
        $createdIds = [];

        foreach ($newFiles as $index => $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $paths = $this->imageService->storeResponsive($file, 'news/gallery');
            $sortOrder = ++$maxSort;

            $image = NewsImage::create([
                'news_id' => $news->id,
                'image_path' => $paths['original'],
                'image_path_small' => $paths['small'],
                'image_path_medium' => $paths['medium'],
                'image_path_large' => $paths['large'],
                'caption' => $this->nullableString($newCaptions[$index] ?? null),
                'link_url' => $this->sanitizeLinkUrl($newLinks[$index] ?? null),
                'sort_order' => $sortOrder,
                'is_cover' => $newCoverIndex !== null && $newCoverIndex === $index,
            ]);

            $createdIds[$index] = $image->id;
        }

        if ($coverId !== null && str_starts_with((string) $coverId, 'new_')) {
            $newIndex = (int) substr((string) $coverId, 4);
            if (isset($createdIds[$newIndex])) {
                $news->images()->update(['is_cover' => false]);
                NewsImage::whereKey($createdIds[$newIndex])->update(['is_cover' => true]);
            }
        }

        $this->ensureCoverImage($news);
        $this->syncCoverToNewsRecord($news);
        $news->refresh();
        $news->touch();
    }

    /**
     * Delete all gallery images for a news article.
     */
    public function deleteAllForNews(News $news): void
    {
        foreach ($news->images as $image) {
            $this->deleteImageFiles($image);
            $image->delete();
        }
    }

    public function deleteImageFiles(NewsImage $image): void
    {
        foreach (['image_path', 'image_path_small', 'image_path_medium', 'image_path_large'] as $field) {
            if ($image->{$field}) {
                $this->imageService->delete($image->{$field});
            }
        }
    }

    protected function ensureCoverImage(News $news): void
    {
        $images = $news->images()->orderBy('sort_order')->get();

        if ($images->isEmpty()) {
            return;
        }

        if ($images->where('is_cover', true)->isEmpty()) {
            $first = $images->first();
            $first->update(['is_cover' => true]);
        } else {
            $covers = $images->where('is_cover', true);
            if ($covers->count() > 1) {
                $keep = $covers->first();
                $news->images()->where('id', '!=', $keep->id)->update(['is_cover' => false]);
            }
        }
    }

    protected function syncCoverToNewsRecord(News $news): void
    {
        $cover = $news->images()->where('is_cover', true)->first();

        if (! $cover) {
            if (! $news->images()->exists()) {
                $this->clearNewsRecordImages($news);
            }

            return;
        }

        $news->update([
            'image_path' => $cover->image_path,
            'image_path_small' => $cover->image_path_small,
            'image_path_medium' => $cover->image_path_medium,
            'image_path_large' => $cover->image_path_large,
        ]);
    }

    protected function clearNewsRecordImages(News $news): void
    {
        foreach (['image_path', 'image_path_small', 'image_path_medium', 'image_path_large'] as $field) {
            if ($news->{$field}) {
                $this->imageService->delete($news->{$field});
            }
        }

        $news->update([
            'image_path' => null,
            'image_path_small' => null,
            'image_path_medium' => null,
            'image_path_large' => null,
        ]);
    }

    protected function nullableString(?string $value): ?string
    {
        $value = $value !== null ? trim($value) : null;

        return $value === '' ? null : $value;
    }

    protected function sanitizeLinkUrl(?string $url): ?string
    {
        $url = $this->nullableString($url);

        if ($url === null) {
            return null;
        }

        $lower = strtolower($url);

        if (str_starts_with($lower, 'javascript:') || str_starts_with($lower, 'data:') || str_starts_with($lower, 'vbscript:')) {
            return null;
        }

        if (str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return $url;
        }

        if (preg_match('#^https?://#i', $url)) {
            return $url;
        }

        if (str_starts_with($lower, 'mailto:')) {
            return $url;
        }

        return 'https://'.$url;
    }
}
