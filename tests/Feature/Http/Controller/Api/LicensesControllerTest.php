<?php

namespace Tests\Feature\Http\Controller\Api;

use App\Http\Controllers\Api\LicensesController;
use App\Models\License;
use App\Models\SiteActivation;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[CoversClass(LicensesController::class)]
class LicensesControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @see \App\Http\Controllers\Api\LicensesController::activate()
     */
    #[DataProvider('providerCanActivate')]
    public function testCanActivate(bool $wasAlreadyActivated, int $expectedStatusCode): void
    {
        /** @var License $license */
        $license = License::factory()->create();

        if ($wasAlreadyActivated) {
            SiteActivation::factory()->create([
                'license_id' => $license->id,
                'domain' => 'example.com',
            ]);
        }

        $response = $this->postJson(route('api.licenses.activations.store', $license), [
            'product_id' => $license->product->uuid,
            'url' => 'https://example.com',
        ]);

        $response->assertStatus($expectedStatusCode);

        $response->assertJson([
            'domain' => 'example.com',
        ]);

        $response->assertJsonStructure([
            'domain',
            'created_at',
            'updated_at',
        ]);
    }

    /** @see testCanActivate */
    public static function providerCanActivate(): \Generator
    {
        yield 'already activated' => [true, 200];
        yield 'not activated yet' => [false, 201];
    }

    /**
     * @covers \App\Http\Controllers\Api\LicensesController::activate()
     */
    public function testCannotActivateIfLimitReached(): void
    {
        /** @var License $license */
        $license = License::factory()->create([
            'activation_limit' => 1,
        ]);

        SiteActivation::factory()->create([
            'license_id' => $license->id,
            'domain' => 'example.com',
        ]);

        // you can activate for the *same* domain just fine
        $response = $this->postJson(route('api.licenses.activations.store', $license), [
            'product_id' => $license->product->uuid,
            'url' => 'https://example.com',
        ]);

        $response->assertOk();

        // you cannot activate for a different one
        $response = $this->postJson(route('api.licenses.activations.store', $license), [
            'product_id' => $license->product->uuid,
            'url' => 'https://site2.example.com',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor('url');
        $this->assertSame(['License activation limit has been reached.'], $response->json('errors.url'));
    }

    /**
     * @see \App\Http\Controllers\Api\LicensesController::deactivate()
     */
    public function testCanDeactivate(): void
    {
        /** @var SiteActivation $siteActivation */
        $siteActivation = SiteActivation::factory()->create([
            'domain' => 'example.com',
        ]);

        $this->assertDatabaseHas(SiteActivation::class, [
            'license_id' => $siteActivation->license_id,
            'domain' => 'example.com',
            'deleted_at' => null,
        ]);

        $response = $this->deleteJson(route('api.licenses.activations.destroy', $siteActivation->license), [
            'url' => 'https://example.com',
        ]);

        $response->assertOk();

        $this->assertDatabaseMissing(SiteActivation::class, [
            'license_id' => $siteActivation->license_id,
            'domain' => 'example.com',
            'deleted_at' => null,
        ]);

        $siteActivation->refresh();
        $this->assertNotNull($siteActivation->deleted_at);
    }
}
