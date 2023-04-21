<?php

namespace Tests\Feature\Http\Requests\Api;

use App\Http\Requests\Api\DeactivateLicenseRequest;
use Tests\TestCase;

/**
 * @covers \App\Http\Requests\Api\DeactivateLicenseRequest
 */
class DeactivateLicenseRequestTest extends TestCase
{
    /**
     * @covers \App\Http\Requests\Api\DeactivateLicenseRequest::rules()
     */
    public function testRules(): void
    {
        $this->assertSame(
            ['url'],
            array_keys((new DeactivateLicenseRequest())->rules())
        );
    }
}
