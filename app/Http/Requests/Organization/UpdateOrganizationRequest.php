<?php

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // User must be authenticated, have org_editor role, and own the organization
        return $this->user() 
            && $this->user()->hasRole('org_editor')
            && $this->user()->organization_id !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Basic Information
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['required', 'string', 'max:500'],
            'full_profile' => ['required', 'string', 'min:100'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'], // 2MB
            
            // Location & Details
            'location' => ['nullable', 'string', 'max:255'],
            'founded_year' => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
            'thematic_focus' => ['nullable', 'string', 'max:255'],
            
            // Programs & Highlights
            'programs' => ['nullable', 'string', 'max:5000'],
            'highlights' => ['nullable', 'string', 'max:5000'],
            
            // Contact Information
            'contact_email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:500'],
            'facebook' => ['nullable', 'url', 'max:500'],
            'twitter' => ['nullable', 'url', 'max:500'],
            'linkedin' => ['nullable', 'url', 'max:500'],
            'instagram' => ['nullable', 'url', 'max:500'],
            
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
            'name.required' => 'Please provide your organization name.',
            'short_description.required' => 'Please provide a brief description of your organization.',
            'short_description.max' => 'The short description cannot exceed 500 characters.',
            'full_profile.required' => 'Please provide a detailed profile of your organization.',
            'full_profile.min' => 'The full profile must be at least 100 characters.',
            'logo.image' => 'The logo must be a valid image file.',
            'logo.mimes' => 'The logo must be a JPEG, PNG, GIF, or WebP file.',
            'logo.max' => 'The logo cannot exceed 2MB.',
            'founded_year.min' => 'The founded year must be 1900 or later.',
            'founded_year.max' => 'The founded year cannot be in the future.',
            'contact_email.email' => 'Please provide a valid email address.',
            'website.url' => 'Please provide a valid website URL.',
            'facebook.url' => 'Please provide a valid Facebook URL.',
            'twitter.url' => 'Please provide a valid Twitter URL.',
            'linkedin.url' => 'Please provide a valid LinkedIn URL.',
            'instagram.url' => 'Please provide a valid Instagram URL.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean up URLs
        $urlFields = ['website', 'facebook', 'twitter', 'linkedin', 'instagram'];
        
        foreach ($urlFields as $field) {
            if ($this->has($field) && $this->$field) {
                $this->merge([
                    $field => trim($this->$field),
                ]);
            }
        }

        // Clean up email
        if ($this->has('contact_email') && $this->contact_email) {
            $this->merge([
                'contact_email' => trim(strtolower($this->contact_email)),
            ]);
        }
    }
}
