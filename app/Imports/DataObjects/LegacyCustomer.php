<?php
/**
 * Customer.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Imports\DataObjects;

use Illuminate\Contracts\Support\Arrayable;

readonly class LegacyCustomer implements Arrayable
{
    public function __construct(
        public int $id,
        public string $customerEmail,
        public ?string $userAccountEmail,
        public ?string $name,
        public string $dateCreated,
        public ?string $stripeCustomerId
    )
    {
    }

    public function toArray() : array
    {
        return get_object_vars($this);
    }
}
