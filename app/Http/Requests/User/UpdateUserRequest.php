<?php

namespace App\Http\Requests\User;

use App\Rules\StrongPassword;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only secretaries can update users
        return $this->user() && $this->user()->hasRole('secretary');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'confirmed', new StrongPassword],
            'role' => ['required', 'in:secretary,org_editor'],
            'organization_id' => [
                Rule::requiredIf($this->role === 'org_editor'),
                'nullable',
                'exists:organizations,id',
            ],
            'is_active' => ['nullable', 'boolean'],
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
            'password.confirmed' => 'The password confirmation does not match.',
            'role.required' => 'Please select a role for the user.',
            'organization_id.required' => 'Please select an organization for the org editor.',
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

        // Remove password if empty (not changing password)
        if ($this->has('password') && empty($this->password)) {
            $this->request->remove('password');
            $this->request->remove('password_confirmation');
        }
    }
}
