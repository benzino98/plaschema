<?php

namespace App\Http\Requests\Admin;

use App\Models\News;
use App\Services\FaqContentService;
use App\Services\NewsImageService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class NewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => 'required|max:255',
            'excerpt' => 'required',
            'content' => 'required|string|max:50000',
            'gallery_images' => 'nullable|array|max:'.NewsImageService::MAX_IMAGES,
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'remove_image_ids' => 'nullable|array',
            'remove_image_ids.*' => 'integer|exists:news_images,id',
            'image_captions' => 'nullable|array',
            'image_captions.*' => 'nullable|string|max:255',
            'image_links' => 'nullable|array',
            'image_links.*' => 'nullable|string|max:2048',
            'new_image_captions' => 'nullable|array',
            'new_image_captions.*' => 'nullable|string|max:255',
            'new_image_links' => 'nullable|array',
            'new_image_links.*' => 'nullable|string|max:2048',
            'cover_image_id' => 'nullable|string|max:50',
            'new_cover_index' => 'nullable|integer|min:0',
            'published_at' => 'nullable|date',
            'is_featured' => 'nullable|boolean',
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['slug'] = [
                'nullable',
                Rule::unique('news')->ignore($this->route('news')),
            ];
        } else {
            $rules['slug'] = 'nullable|unique:news,slug';
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        if (empty($this->slug) && $this->has('title')) {
            $this->merge([
                'slug' => Str::slug($this->title),
            ]);
        }

        $this->merge([
            'is_featured' => $this->has('is_featured'),
        ]);

        if ($this->has('content')) {
            $this->merge([
                'content' => app(FaqContentService::class)->sanitizeForStorage((string) $this->content, 'news'),
            ]);
        }
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $newsId = $this->route('news');
            $existingCount = 0;

            if ($newsId) {
                $news = News::find($newsId);
                if ($news) {
                    $existingCount = $news->images()->count();
                    if ($existingCount === 0 && $news->image_path) {
                        $existingCount = 1;
                    }
                }
            }

            $removed = count((array) $this->input('remove_image_ids', []));
            $newCount = count($this->file('gallery_images') ?? []);
            $total = $existingCount - $removed + $newCount;

            if ($total > NewsImageService::MAX_IMAGES) {
                $validator->errors()->add(
                    'gallery_images',
                    'A news article can have at most '.NewsImageService::MAX_IMAGES.' images.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The news title is required.',
            'title.max' => 'The news title cannot exceed 255 characters.',
            'excerpt.required' => 'A short excerpt is required.',
            'content.required' => 'The news content is required.',
            'gallery_images.max' => 'You can upload at most '.NewsImageService::MAX_IMAGES.' images per article.',
            'gallery_images.*.image' => 'Each gallery file must be an image.',
            'gallery_images.*.mimes' => 'Gallery images must be JPEG, PNG, JPG, GIF, or WebP.',
            'gallery_images.*.max' => 'Each gallery image cannot exceed 5MB.',
            'slug.unique' => 'This slug is already in use. Please modify it or leave blank for auto-generation.',
            'published_at.date' => 'The published date must be a valid date.',
        ];
    }
}
