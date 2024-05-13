<?php

namespace App\Http\Requests;

use App\Models\Meeting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MeetingStoreRequest extends FormRequest
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
        return [
            'manufacturer_id' => 'required|exists:manufacturers,id',
            'year' => [
                Rule::unique(Meeting::class)->where(function ($query) {
                    $query->where('year', $this->year)
                        ->where('manufacturer_id', $this->manufacturer_id);
                })
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'year.unique' => trans('validation.custom.meetings.unique'),
        ];
    }
}
