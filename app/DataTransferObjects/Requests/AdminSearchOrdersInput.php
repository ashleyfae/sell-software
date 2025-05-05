<?php
/**
 * AdminSearchOrdersInput.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\DataTransferObjects\Requests;

use Illuminate\Support\Arr;

readonly class AdminSearchOrdersInput
{
    public function __construct(
        public ?string $customerEmailSearchInput = null
    ) {

    }

    public static function fromArray(array $input): static
    {
        return new static(
            customerEmailSearchInput: Arr::get($input, 'customer_email')
        );
    }
}
