<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class NewsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Since we're already using auth middleware on admin routes
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'required|max:255',
            'excerpt' => 'required',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'published_at' => 'nullable|date',
            'is_featured' => 'nullable|boolean',
        ];

        // Add slug uniqueness check for updates
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

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Generate slug if not provided
        if (empty($this->slug) && $this->has('title')) {
            $this->merge([
                'slug' => Str::slug($this->title),
            ]);
        }

        // Convert checkbox to boolean
        $this->merge([
            'is_featured' => $this->has('is_featured'),
        ]);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The news title is required.',
            'title.max' => 'The news title cannot exceed 255 characters.',
            'excerpt.required' => 'A short excerpt is required.',
            'content.required' => 'The news content is required.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a jpeg, png, jpg, or gif.',
            'image.max' => 'The image size cannot exceed 2MB.',
            'slug.unique' => 'This slug is already in use. Please modify it or leave blank for auto-generation.',
            'published_at.date' => 'The published date must be a valid date.',
        ];
    }
} 