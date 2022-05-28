<?php
/**
 * HasActivationLimit.php
 *
 * @package   software
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace App\Traits;

/**
 * @property int|null $activation_limit
 */
trait HasActivationLimit
{
    public function hasUnlimitedActivations(): bool
    {
        return $this->activation_limit === null;
    }
}
