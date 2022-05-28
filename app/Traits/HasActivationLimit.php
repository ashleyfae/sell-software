<?php
/**
 * HasActivationLimit.php
 *
 * @package   software
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace App\Traits;

trait HasActivationLimit
{
    public function hasUnlimitedActivations(): bool
    {
        return $this->activation_limit === null;
    }
}
