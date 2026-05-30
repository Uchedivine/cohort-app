<?php

namespace App\Http\Requests\User;

use App\Rules\StrongPassword;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only secretaries can create users
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', new StrongPassword],
            'role' => ['required', 'in:secretary,org_editor'],
            'organization_id' => [
                Rule::requiredIf($this->role === 'org_editor'),
                'nullable',
                'exists:organizations,id',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please provide the user\'s full name.',
            'email.required' => 'Please provide an email address.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'password.required' => 'Please provide a password.',
            'password.confirmed' => 'The password confirmation does not match.',
            'role.required' => 'Please select a role for the user.',
            'role.in' => 'Invalid role selected.',
            'organization_id.required' => 'Please select an organization for the org editor.',
            'organization_id.exists' => 'The selected organization is invalid.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'organization_id' => 'organization',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean up email
        if ($this->has('email')) {
            $this->merge([
                'email' => trim(strtolower($this->email)),
            ]);
        }

        // If role is secretary, remove organization_id
        if ($this->role === 'secretary') {
            $this->merge(['organization_id' => null]);
        }
    }
}
