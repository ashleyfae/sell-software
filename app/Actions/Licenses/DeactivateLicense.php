<?php
/**
 * DeactivateLicense.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Licenses;

use App\Models\License;

class DeactivateLicense
{
    public function execute(string $url, License $license): void
    {
        $license->siteActivations()->whereDomain($url)->delete();
    }
}
