<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProviderRequest extends FormRequest
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
            'name' => 'required|max:255',
            'type' => 'required|max:100',
            'address' => 'required|max:255',
            'city' => 'required|max:100',
            'state' => 'nullable|max:100',
            'phone' => 'required|max:50',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'required',
            'services' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert checkboxes to boolean
        $this->merge([
            'is_featured' => $this->has('is_featured'),
            'is_active' => $this->has('is_active'),
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
            'name.required' => 'The provider name is required.',
            'name.max' => 'The provider name cannot exceed 255 characters.',
            'type.required' => 'The provider type is required.',
            'address.required' => 'The address is required.',
            'city.required' => 'The city is required.',
            'phone.required' => 'The phone number is required.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'website.url' => 'Please enter a valid website URL.',
            'description.required' => 'The description is required.',
            'services.required' => 'The services information is required.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a jpeg, png, jpg, or gif.',
            'image.max' => 'The image size cannot exceed 2MB.',
        ];
    }
} 