<?php

namespace App\Rules;

use App\Models\License;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class DomainCanBeActivatedForLicense implements ValidationRule
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
        if ($this->license->hasUnlimitedActivations()) {
            return;
        }

        if ($this->license->siteActivations()->whereDomainNot($value)->count() >= $this->license->activation_limit) {
            $fail('License activation limit has been reached.');
        }
    }
}
