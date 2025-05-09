<?php
/**
 * HasDryRunOption.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Console\Commands\Traits;

trait HasDryRunOption
{
    protected function isDryRun() : bool
    {
        return (bool) $this->option('dry-run');
    }
}
