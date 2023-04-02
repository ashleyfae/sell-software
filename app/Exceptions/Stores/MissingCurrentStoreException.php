<?php
/**
 * MissingCurrentStoreException.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Exceptions\Stores;

use App\Repositories\StoreRepository;

/**
 * Thrown when the "currentStore" is unexpectedly missing from the request.
 * {@see StoreRepository::getStoreForRequest()}
 */
class MissingCurrentStoreException extends \Exception
{

}
