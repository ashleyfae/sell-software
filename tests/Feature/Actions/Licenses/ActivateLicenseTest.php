<?php

namespace Tests\Feature\Actions\Licenses;

use App\Actions\Licenses\ActivateLicense;
use App\Models\License;
use App\Models\SiteActivation;
use Generator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[CoversClass(ActivateLicense::class)]
class ActivateLicenseTest extends TestCase
{
    /**
     * @see \App\Actions\Licenses\ActivateLicense::execute()
     */
    public function testCanExecuteWithExistingActivation(): void
    {
        /** @var ActivateLicense&MockInterface $action */
        $action = $this->partialMock(ActivateLicense::class);
        $action->shouldAllowMockingProtectedMethods();

        $license = new License();
        $activation = new SiteActivation();

        $action->expects('getExistingActivation')
            ->once()
            ->with('example.com', $license)
            ->andReturn($activation);

        $this->assertSame($activation, $action->execute('example.com', $license));
        $this->assertFalse($action->wasCreated());
    }

    /**
     * @see \App\Actions\Licenses\ActivateLicense::execute()
     */
    public function testCanExecuteWithNoExistingActivation(): void
    {
        /** @var ActivateLicense&MockInterface $action */
        $action = $this->partialMock(ActivateLicense::class);
        $action->shouldAllowMockingProtectedMethods();

        /** @var License $license */
        $license = License::factory()->create();

        $this->assertDatabaseMissing(SiteActivation::class, ['license_id' => $license->id]);

        $action->expects('getExistingActivation')
            ->once()
            ->with('example.com', $license)
            ->andThrow(ModelNotFoundException::class);

        $activation = $action->execute('example.com', $license);

        $this->assertDatabaseHas(SiteActivation::class, ['license_id' => $license->id]);
        $this->assertTrue($action->wasCreated());
        $this->assertSame($license->id, $activation->license_id);
    }

    /**
     * @see \App\Actions\Licenses\ActivateLicense::getExistingActivation()
     */
    #[DataProvider('providerCanGetExistingActivation')]
    public function testCanGetExistingActivation(string $domain, bool $shouldThrowException): void
    {
        /** @var SiteActivation $activation */
        $activation = SiteActivation::factory()->create([
            'domain' => 'example.com',
        ]);

        if ($shouldThrowException) {
            $this->expectException(ModelNotFoundException::class);
        }

        /** @var SiteActivation $found */
        $found = $this->invokeInaccessibleMethod(app(ActivateLicense::class), 'getExistingActivation', $domain, $activation->license);
        $this->assertSame($domain, $found->domain);
        $this->assertSame($activation->license_id, $found->license_id);
    }

    /** @see testCanGetExistingActivation */
    public static function providerCanGetExistingActivation(): Generator
    {
        yield 'mismatching domain' => [
            'domain' => 'invalid.example.com',
            'shouldThrowException' => true,
        ];

        yield 'matching domain' => [
            'domain' => 'example.com',
            'shouldThrowException' => false,
        ];
    }
}
