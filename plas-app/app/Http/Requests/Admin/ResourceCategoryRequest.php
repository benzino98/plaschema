<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ResourceCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $categoryId = $this->route('resource_category') ? $this->route('resource_category')->id : null;

        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'parent_id' => [
                'nullable',
                'exists:resource_categories,id',
                function ($attribute, $value, $fail) use ($categoryId) {
                    if ($value == $categoryId) {
                        $fail('A category cannot be its own parent.');
                    }
                }
            ],
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
        ];

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'name.required' => 'The category name is required.',
            'name.max' => 'The category name cannot be longer than 255 characters.',
            'description.max' => 'The category description cannot be longer than 1000 characters.',
            'parent_id.exists' => 'The selected parent category does not exist.',
            'display_order.integer' => 'The display order must be a number.',
            'display_order.min' => 'The display order must be a positive number.',
        ];
    }
} 