<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class UserUpdateRequest extends FormRequest
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
        $instanceID = $this->route('instance')?->id;

        return [
            'name' => ['string', 'max:255', Rule::unique(User::class)->ignore($instanceID)],
            'email' => ['email', 'max:255', Rule::unique(User::class)->ignore($instanceID)],
            'photo' => ['file', File::types(['png', 'jpg', 'jpeg']), 'nullable'],
            'roles' => ['required'],
        ];
    }
}
