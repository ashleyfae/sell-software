<?php
/**
 * DataSourceRepository.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Imports\Repositories;

use App\Imports\Enums\DataSource;
use Illuminate\Support\Facades\Config;

class DataSourceRepository
{
    public static function getCurrentSource() : DataSource
    {
        return Config::get('imports.currentSource');
    }

    public static function getCurrentCurrency() : string
    {
        return static::getCurrentSource()->getCurrency();
    }
}
