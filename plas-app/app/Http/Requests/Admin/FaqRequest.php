<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FaqRequest extends FormRequest
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
        return [
            'question' => 'required|max:255',
            'answer' => 'required',
            'category' => 'nullable|max:100',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'show_on_plans_page' => 'nullable|boolean',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert checkboxes to boolean
        $this->merge([
            'is_active' => $this->has('is_active'),
            'show_on_plans_page' => $this->has('show_on_plans_page'),
        ]);
        
        // Convert empty order to 0
        if (empty($this->order)) {
            $this->merge(['order' => 0]);
        }
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'question.required' => 'The question is required.',
            'question.max' => 'The question cannot exceed 255 characters.',
            'answer.required' => 'The answer is required.',
            'category.max' => 'The category name cannot exceed 100 characters.',
            'order.integer' => 'The order must be a whole number.',
            'order.min' => 'The order cannot be a negative number.',
        ];
    }
} 