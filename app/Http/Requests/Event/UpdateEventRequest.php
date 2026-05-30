<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only secretaries can update events
        return $this->user() && $this->user()->hasRole('secretary');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $event = $this->route('event');
        $minDate = $event && $event->start_date->isPast() ? 'date' : 'after_or_equal:today';

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:50'],
            'start_date' => ['required', 'date', $minDate],
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
            'start_date.required' => 'Please provide a start date for the event.',
            'end_date.after' => 'The end date must be after the start date.',
        ];
    }
}
