<?php

namespace App\Http\Requests\Tag;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only secretaries can create tags
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
            'name' => ['required', 'string', 'max:100', 'unique:tags,name'],
            'type' => ['nullable', 'in:general,sdg,theme,sector'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please provide a tag name.',
            'name.max' => 'The tag name cannot exceed 100 characters.',
            'name.unique' => 'This tag already exists.',
            'type.in' => 'Invalid tag type selected.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean up tag name
        if ($this->has('name')) {
            $this->merge([
                'name' => trim($this->name),
            ]);
        }

        // Set default type if not provided
        if (!$this->has('type') || empty($this->type)) {
            $this->merge(['type' => 'general']);
        }
    }
}
