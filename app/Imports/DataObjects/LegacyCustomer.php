<?php
/**
 * Customer.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Imports\DataObjects;

class LegacyCustomer extends AbstractLegacyObject
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
}
