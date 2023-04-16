<?php

namespace Tests\Feature\Http\Controller\Api;

use App\Models\License;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

/**
 * @covers \App\Http\Controllers\Api\LicensesController
 */
class LicensesControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * @covers \App\Http\Controllers\Api\LicensesController::activate()
     */
    public function testCanActivate(): void
    {
        /** @var License $license */
        $license = License::factory()->create();

        $response = $this->post(route('api.licenses.activations.store', $license), [
            'product_id' => $license->product->uuid,
            'url' => 'https://example.com',
        ]);

        $response->assertJson([
            'domain' => 'https://example.com',
        ]);

        $response->assertJsonStructure([
            'domain',
            'created_at',
            'updated_at',
        ]);
    }

    /**
     * @covers \App\Http\Controllers\Api\LicensesController::deactivate()
     */
    public function testCanDeactivate(): void
    {
        $this->markTestIncomplete(__METHOD__);
    }
}
