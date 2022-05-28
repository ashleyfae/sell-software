<?php
/**
 * DataTransferObject.php
 *
 * @package   software
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace App\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface DataTransferObject extends CreateableFromArray, Arrayable
{

}
