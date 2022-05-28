<?php
/**
 * LicenseStatus.php
 *
 * @package   software
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace App\Enums;

enum LicenseStatus: string
{
    case Active = 'active';
    case Expired = 'expired';

    public function getDisplayName(): string
    {
        return ucwords($this->value);
    }
}
