<?php

namespace App\Http\Requests\Checkout;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchaseProductsRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'license' => ['nullable', 'string', Rule::exists('licenses', 'license_key')],
            'products' => ['required', 'array'],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->input('products') && is_string($this->input(['products']))) {
            $this->merge([
                'products' => explode(',', $this->input('products'))
            ]);
        }
    }
}
