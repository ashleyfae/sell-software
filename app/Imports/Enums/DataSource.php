<?php
/**
 * DataSource.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Imports\Enums;

enum DataSource : string
{
    case Novelist = 'novelist';
    case NoseGraze = 'nosegraze';

    public function getCurrency() : string
    {
        return match($this) {
            DataSource::Novelist => 'gbp',
            DataSource::NoseGraze => 'usd',
        };
    }
}
