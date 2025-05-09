<?php
/**
 * HasMaxOption.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Console\Commands\Traits;

trait HasMaxOption
{
    protected int $numberImported = 0;

    protected function atMaxItems(): bool
    {
        return $this->option('max') && $this->numberImported >= $this->option('max');
    }
}
