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

    public function className(): string
    {
        return match ($this) {
            LicenseStatus::Active => 'success',
            LicenseStatus::Expired => 'danger',
        };
    }


    public function displayName(): string
    {
        return ucwords($this->value);
    }
}
