<?php
/**
 * ImportQuery.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Imports\Database;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;

class ImportQuery
{
    public static function make(): Connection
    {
        return DB::connection('import');
    }
}
