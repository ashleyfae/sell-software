<?php
/**
 * PaymentGateway.php
 *
 * @package   software
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace App\Enums;

enum PaymentGateway: string
{
    case Manual = 'manual';
    case Stripe = 'stripe';
}
