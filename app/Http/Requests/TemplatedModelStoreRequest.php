<?php

namespace App\Http\Requests;

use App\Http\Controllers\TemplatedModelController;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TemplatedModelStoreRequest extends FormRequest
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
        $rules = [];

        $modelName = $this->route('modelName');
        $model = TemplatedModelController::findModelByName($modelName);
        $modelAttributes = collect($model['attributes']);

        if ($modelAttributes->contains('name')) {
            $rules['name'] = ['string', Rule::unique($model['full_namespace'])];
        }

        return $rules;
    }
}
