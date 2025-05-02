<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProviderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'provider_type' => 'required|max:100',
            'address' => 'required',
            'city' => 'required|max:100',
            'state' => 'required|max:100',
            'phone' => 'required|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'services' => 'nullable',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The provider name is required.',
            'name.max' => 'The provider name cannot exceed 255 characters.',
            'provider_type.required' => 'Please select a provider type.',
            'provider_type.max' => 'The provider type cannot exceed 100 characters.',
            'address.required' => 'The address is required.',
            'city.required' => 'The city is required.',
            'city.max' => 'The city name cannot exceed 100 characters.',
            'state.required' => 'The state is required.',
            'state.max' => 'The state name cannot exceed 100 characters.',
            'phone.required' => 'The phone number is required.',
            'phone.max' => 'The phone number cannot exceed 20 characters.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'The email address cannot exceed 255 characters.',
            'website.url' => 'Please enter a valid website URL (including http:// or https://).',
            'website.max' => 'The website URL cannot exceed 255 characters.',
            'logo.image' => 'The logo must be an image file.',
            'logo.mimes' => 'The logo must be a file of type: jpeg, png, jpg, gif.',
            'logo.max' => 'The logo may not be greater than 2MB.',
        ];
    }
}
