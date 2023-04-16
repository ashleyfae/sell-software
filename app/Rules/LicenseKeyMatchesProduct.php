<?php

namespace App\Rules;

use App\Models\License;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class LicenseKeyMatchesProduct implements ValidationRule
{
    public function __construct(protected License $license)
    {

    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->license->product->uuid !== $value) {
            $fail('The :attribute must match the license key\'s product.');
        }
    }
}
