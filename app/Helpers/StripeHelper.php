<?php
/**
 * StripeHelper.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class StripeHelper
{
    public static function dashboardUrl(string $path) : string
    {
        $configKey = App::isProduction() ? 'prod' : 'test';

        return Config::get("services.stripe.dashboardUrl.{$configKey}").$path;
    }
}
