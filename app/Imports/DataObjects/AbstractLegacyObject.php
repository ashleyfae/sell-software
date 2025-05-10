<?php
/**
 * AbstractLegacyObject.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Imports\DataObjects;

use Illuminate\Contracts\Support\Arrayable;

abstract class AbstractLegacyObject implements Arrayable
{
    public function toArray() : array
    {
        return get_object_vars($this);
    }
}
