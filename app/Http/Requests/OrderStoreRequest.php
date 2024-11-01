<?php

namespace App\Http\Requests;

use App\Models\Currency;
use App\Models\Manufacturer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'manufacturer_id' => [Rule::exists(Manufacturer::class, 'id')],
            'currency_id' => [Rule::exists(Currency::class, 'id')],
        ];
    }
}
