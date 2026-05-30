<?php

namespace App\Http\Requests\Story;

use Illuminate\Foundation\Http\FormRequest;

class StoreStoryRequest extends FormRequest
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
            'summary' => ['required', 'string', 'max:500'],
            'content' => ['required', 'string', 'min:100'],
            'author' => ['nullable', 'string', 'max:255'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'], // 5MB
            
            // Structured content (optional)
            'structured_content' => ['nullable', 'array'],
            'structured_content.problem' => ['nullable', 'string', 'max:2000'],
            'structured_content.approach' => ['nullable', 'string', 'max:2000'],
            'structured_content.outcome' => ['nullable', 'string', 'max:2000'],
            'structured_content.lessons' => ['nullable', 'string', 'max:2000'],
            
            // Tags
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
            
            // Action (submit or draft)
            'action' => ['nullable', 'in:submit,draft'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please provide a title for your story.',
            'title.max' => 'The title cannot exceed 255 characters.',
            'summary.required' => 'Please provide a brief summary of your story.',
            'summary.max' => 'The summary cannot exceed 500 characters.',
            'content.required' => 'Please provide the full story content.',
            'content.min' => 'The story content must be at least 100 characters.',
            'featured_image.image' => 'The featured image must be a valid image file.',
            'featured_image.mimes' => 'The featured image must be a JPEG, PNG, GIF, or WebP file.',
            'featured_image.max' => 'The featured image cannot exceed 5MB.',
            'tags.*.exists' => 'One or more selected tags are invalid.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'structured_content.problem' => 'problem section',
            'structured_content.approach' => 'approach section',
            'structured_content.outcome' => 'outcome section',
            'structured_content.lessons' => 'lessons learned section',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default action if not provided
        if (!$this->has('action')) {
            $this->merge(['action' => 'draft']);
        }
    }
}
