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
    case PayPal = 'paypal'; // backwards compat

    public function getCustomerLabel() : string
    {
        return match($this) {
            PaymentGateway::Stripe => 'Credit/Debit Card',
            default => $this->name,
        };
    }
}
