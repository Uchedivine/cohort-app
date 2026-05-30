<?php

namespace App\Http\Requests\Resource;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResourceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // User must be authenticated and have org_editor role
        return $this->user() && $this->user()->hasRole('org_editor');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'type' => ['required', 'in:file,link,video'],
            
            // Conditional validation based on type
            'file_path' => [
                Rule::requiredIf($this->type === 'file'),
                'nullable',
                'file',
                'mimes:pdf,doc,docx,ppt,pptx',
                'max:10240', // 10MB
            ],
            'external_url' => [
                Rule::requiredIf($this->type === 'link'),
                'nullable',
                'url',
                'max:500',
            ],
            'video_url' => [
                Rule::requiredIf($this->type === 'video'),
                'nullable',
                'url',
                'max:500',
                'regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be|vimeo\.com)\/.*$/',
            ],
            
            // Tags
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please provide a title for the resource.',
            'description.required' => 'Please provide a description of the resource.',
            'type.required' => 'Please select a resource type.',
            'type.in' => 'Invalid resource type selected.',
            'file_path.required' => 'Please upload a file.',
            'file_path.mimes' => 'The file must be a PDF, DOC, DOCX, PPT, or PPTX.',
            'file_path.max' => 'The file cannot exceed 10MB.',
            'external_url.required' => 'Please provide a URL for the resource.',
            'external_url.url' => 'Please provide a valid URL.',
            'video_url.required' => 'Please provide a video URL.',
            'video_url.url' => 'Please provide a valid video URL.',
            'video_url.regex' => 'The video URL must be from YouTube or Vimeo.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean up URLs
        if ($this->has('external_url')) {
            $this->merge([
                'external_url' => trim($this->external_url),
            ]);
        }

        if ($this->has('video_url')) {
            $this->merge([
                'video_url' => trim($this->video_url),
            ]);
        }
    }
}
