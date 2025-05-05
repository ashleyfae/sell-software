<?php
/**
 * AdminSearchLicensesInput.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\DataTransferObjects\Requests;

use Illuminate\Support\Arr;

readonly class AdminSearchLicensesInput
{
    public function __construct(
        public ?string $licenseKeySearchInput = null,
        public ?string $customerEmailSearchInput = null
    ) {

    }

    public static function fromArray(array $input)
    {
        return new static(
            licenseKeySearchInput: Arr::get($input, 'license_key'),
            customerEmailSearchInput: Arr::get($input, 'customer_email')
        );
    }
}
