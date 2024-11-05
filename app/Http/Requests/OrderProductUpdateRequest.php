<?php

namespace App\Http\Requests;

use App\Models\CountryCode;
use App\Models\MarketingAuthorizationHolder;
use App\Models\Process;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderProductUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'process_id' => [Rule::exists(Process::class, 'id')],
            'country_code_id' => [Rule::exists(CountryCode::class, 'id')],
            'marketing_authorization_holder_id' => [Rule::exists(MarketingAuthorizationHolder::class, 'id')],
        ];
    }
}
