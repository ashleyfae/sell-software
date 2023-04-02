<?php

namespace App\Http\Requests;

use App\Enums\Currency;
use App\Enums\PeriodUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductPriceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'currency' => ['required', 'string', Rule::in(array_map(fn(Currency $currency) => $currency->value, Currency::cases()))],
            'price' => ['required', 'string', 'numeric'],
            'renewal_price' => ['required', 'string', 'numeric'],
            'license_period' => ['nullable', 'integer'],
            'license_period_unit' => ['required', Rule::in(array_map(fn(PeriodUnit $unit) => $unit->value, PeriodUnit::cases()))],
            'activation_limit' => ['nullable', 'integer'],
            'stripe_id' => ['required', 'string', 'max:500'],
        ];
    }
}
