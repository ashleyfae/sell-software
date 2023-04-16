<?php

namespace App\Rules;

use App\Exceptions\InvalidUrlException;
use App\Helpers\DomainSanitizer;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class DomainWithOptionalPath implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            DomainSanitizer::normalize($value);
        } catch(InvalidUrlException $e) {
            $fail('The :attribute must be a valid URL.');
        }
    }
}
