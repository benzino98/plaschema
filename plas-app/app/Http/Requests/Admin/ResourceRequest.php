<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ResourceRequest extends FormRequest
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
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:10000',
            'category_id' => 'required|exists:resource_categories,id',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];

        // If this is a creation request or if a new file is being uploaded
        if ($this->isMethod('post') || $this->hasFile('file')) {
            $rules['file'] = 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar|max:20480'; // 20MB max
        }

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
            'title.required' => 'The resource title is required.',
            'title.max' => 'The resource title cannot be longer than 255 characters.',
            'description.required' => 'The resource description is required.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category does not exist.',
            'file.required' => 'Please upload a file.',
            'file.file' => 'The uploaded file is invalid.',
            'file.mimes' => 'The file must be a PDF, Word document, Excel spreadsheet, PowerPoint presentation, text file, or archive (ZIP/RAR).',
            'file.max' => 'The file size cannot exceed 20MB.',
        ];
    }
} 