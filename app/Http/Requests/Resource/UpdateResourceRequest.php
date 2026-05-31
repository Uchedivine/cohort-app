<?php

namespace App\Http\Requests\Resource;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateResourceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by controller
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
            'description' => ['required', 'string'],
            'type' => ['required', 'in:file,link,video'],
            
            // File upload - only required if type is 'file' and no existing file
            'file_path' => [
                Rule::requiredIf(function () {
                    return $this->type === 'file' && !$this->resource?->file_path;
                }),
                'nullable',
                'file',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip',
                'max:10240', // 10MB
            ],
            
            // External URL - required if type is 'link'
            'external_url' => [
                Rule::requiredIf($this->type === 'link'),
                'nullable',
                'url',
                'max:500',
            ],
            
            // Video URL - required if type is 'video'
            'video_url' => [
                Rule::requiredIf($this->type === 'video'),
                'nullable',
                'url',
                'regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be|vimeo\.com)\/.*$/',
                'max:500',
            ],
            
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please provide a title for the resource.',
            'description.required' => 'Please provide a description for the resource.',
            'type.required' => 'Please select a resource type.',
            'type.in' => 'Invalid resource type selected.',
            'file_path.required' => 'Please upload a file.',
            'file_path.mimes' => 'File must be a PDF, Word, Excel, PowerPoint, text, or ZIP file.',
            'file_path.max' => 'File size must not exceed 10MB.',
            'external_url.required' => 'Please provide a URL for the external link.',
            'external_url.url' => 'Please provide a valid URL.',
            'video_url.required' => 'Please provide a video URL.',
            'video_url.url' => 'Please provide a valid video URL.',
            'video_url.regex' => 'Video URL must be from YouTube or Vimeo.',
            'tags.*.exists' => 'One or more selected tags are invalid.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // If video_url is provided, map it to external_url for database storage
        if ($this->filled('video_url') && $this->type === 'video') {
            $this->merge([
                'external_url' => $this->video_url,
            ]);
        }
    }
}
