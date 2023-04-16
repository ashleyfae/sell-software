<?php
/**
 * ActivateLicense.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Licenses;

use App\Models\License;
use App\Models\SiteActivation;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ActivateLicense
{
    protected bool $wasCreated = false;

    public function execute(string $url, License $license): SiteActivation
    {
        try {
            return $this->getExistingActivation($url, $license);
        } catch(ModelNotFoundException $e) {
            $this->wasCreated = true;

            return $license->siteActivations()->create([
                'domain' => $url,
                'is_local' => false, // @TODO
            ]);
        }
    }

    /**
     * @throws ModelNotFoundException
     */
    protected function getExistingActivation(string $url, License $license): SiteActivation
    {
        return $license->siteActivations()->whereDomain($url)->firstOrFail();
    }

    public function wasCreated(): bool
    {
        return $this->wasCreated;
    }
}
