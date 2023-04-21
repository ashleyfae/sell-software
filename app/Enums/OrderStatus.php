<?php
/**
 * OrderStatus.php
 *
 * @package   software
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Complete = 'complete';
    case Failed = 'failed';

    public function displayName(): string
    {
        return ucwords($this->value);
    }

    public function className(): string
    {
        return match($this) {
            OrderStatus::Pending => 'info',
            OrderStatus::Complete => 'success',
            OrderStatus::Failed => 'danger',
        };
    }
}
