<?php
/**
 * LegacyLicense.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Imports\DataObjects;

use App\Models\License;

class LegacyLicense extends AbstractLegacyObject
{
    public function __construct(
        public int $id,
        public License $license
    )
    {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'license' => $this->license->toArray(),
        ];
    }
}
