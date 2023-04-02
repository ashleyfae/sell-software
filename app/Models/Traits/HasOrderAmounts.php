<?php
/**
 * HasOrderAmounts.php
 *
 * @package   software
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace App\Models\Traits;

use App\Helpers\Money;

/**
 * @property Money $subtotal
 * @property Money $discount
 * @property Money $tax
 * @property Money $total
 */
trait HasOrderAmounts
{

}
