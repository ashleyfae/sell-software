<?php

namespace Tests\Feature\Http\Requests\Api;

use App\Http\Requests\Api\DeactivateLicenseRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(DeactivateLicenseRequest::class)]
class DeactivateLicenseRequestTest extends TestCase
{
    /**
     * @see \App\Http\Requests\Api\DeactivateLicenseRequest::rules()
     */
    public function testRules(): void
    {
        $this->assertSame(
            ['url'],
            array_keys((new DeactivateLicenseRequest())->rules())
        );
    }
}
