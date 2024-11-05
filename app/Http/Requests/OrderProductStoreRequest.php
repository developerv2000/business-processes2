<?php

namespace App\Http\Requests;

use App\Models\CountryCode;
use App\Models\MarketingAuthorizationHolder;
use App\Models\Order;
use App\Models\Process;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderProductStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_id' => [Rule::exists(Order::class, 'id')],
            'process_id' => [Rule::exists(Process::class, 'id')],
            'country_code_id' => [Rule::exists(CountryCode::class, 'id')],
            'marketing_authorization_holder_id' => [Rule::exists(MarketingAuthorizationHolder::class, 'id')],
        ];
    }
}
