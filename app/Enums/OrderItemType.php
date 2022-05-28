<?php
/**
 * OrderItemType.php
 *
 * @package   software
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace App\Enums;

enum OrderItemType: string
{
    case New = 'new';
    case Renewal = 'renewal';
}
