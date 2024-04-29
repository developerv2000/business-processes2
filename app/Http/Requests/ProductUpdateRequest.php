<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductUpdateRequest extends FormRequest
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
            'dosage' => [
                Rule::unique(Product::class)->ignore($instanceID)->where(function ($query) {
                    $query->where('manufacturer_id', $this->manufacturer_id)
                        ->where('inn_id', $this->inn_id)
                        ->where('form_id', $this->form_id)
                        ->where('dosage', $this->dosage)
                        ->where('pack', $this->pack);
                }),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'dosage.unique' => trans('validation.custom.ivp.unique'),
        ];
    }
}
