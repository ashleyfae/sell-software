<?php
/**
 * CreateableFromArray.php
 *
 * @package   software
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace App\Contracts;

interface CreateableFromArray
{
    public static function fromArray(array $array): static;
}
