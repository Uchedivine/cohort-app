<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only secretaries can create events
        return $this->user() && $this->user()->hasRole('secretary');
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
            'description' => ['required', 'string', 'min:50'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'location' => ['nullable', 'string', 'max:500'],
            'virtual_link' => ['nullable', 'url', 'max:500'],
            'rsvp_link' => ['nullable', 'url', 'max:500'],
            'banner_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'], // 5MB
            
            // Tags
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
            
            // Related organizations
            'organizations' => ['nullable', 'array'],
            'organizations.*' => ['exists:organizations,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please provide an event title.',
            'description.required' => 'Please provide an event description.',
            'description.min' => 'The description must be at least 50 characters.',
            'start_date.required' => 'Please provide a start date for the event.',
            'start_date.after_or_equal' => 'The start date must be today or a future date.',
            'end_date.after' => 'The end date must be after the start date.',
            'virtual_link.url' => 'Please provide a valid virtual meeting URL.',
            'rsvp_link.url' => 'Please provide a valid RSVP URL.',
            'banner_image.image' => 'The banner must be a valid image file.',
            'banner_image.max' => 'The banner image cannot exceed 5MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean up URLs
        if ($this->has('virtual_link')) {
            $this->merge(['virtual_link' => trim($this->virtual_link)]);
        }

        if ($this->has('rsvp_link')) {
            $this->merge(['rsvp_link' => trim($this->rsvp_link)]);
        }
    }
}
