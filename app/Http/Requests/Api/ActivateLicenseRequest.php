<?php

namespace App\Http\Requests\Api;

use App\Rules\DomainCanBeActivatedForLicense;
use App\Rules\DomainWithOptionalPath;
use App\Rules\LicenseKeyMatchesProduct;
use Illuminate\Foundation\Http\FormRequest;

class ActivateLicenseRequest extends FormRequest
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
            'product_id' => ['required', 'string', 'exists:products,uuid', new LicenseKeyMatchesProduct($this->license)],
            'url' => ['required', new DomainWithOptionalPath(), new DomainCanBeActivatedForLicense($this->license)],
        ];
    }
}
