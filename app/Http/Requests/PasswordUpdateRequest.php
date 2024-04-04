<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'new_password' => ['required', 'min:4'],
        ];

        // Only validate current password if not an admin or not updating via admin panel
        if (!$this->user()->isAdmin() || !$this->by_admin) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        return $rules;
    }
}
