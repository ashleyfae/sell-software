<?php

namespace Tests\Feature\Actions\Stores;

use App\Actions\Stores\StoreDeterminer;
use App\Models\Store;
use App\Models\User;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * @covers \App\Actions\Stores\StoreDeterminer
 */
class StoreDeterminerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @covers \App\Actions\Stores\StoreDeterminer::determineForCurrentUser()
     */
    public function testCanDetermineForCurrentUserWhenNotLoggedIn(): void
    {
        Auth::shouldReceive('check')->andReturnFalse();

        /** @var StoreDeterminer $determiner */
        $determiner = app(StoreDeterminer::class);
        $determiner->determineForCurrentUser();

        $this->assertTrue($determiner->stores->isEmpty());
        $this->assertNull($determiner->currentStore);
    }

    /**
     * @covers \App\Actions\Stores\StoreDeterminer::determineForCurrentUser()
     */
    public function testCanDetermineForCurrentUserWhenLoggedIn(): void
    {
        $user = User::factory()->has(Store::factory()->count(3))->create();

        Auth::shouldReceive('check')->andReturnTrue();
        Auth::shouldReceive('user')->andReturn($user);

        /** @var StoreDeterminer&MockInterface $determiner */
        $determiner = $this->partialMock(StoreDeterminer::class);
        $determiner->shouldAllowMockingProtectedMethods();

        $determiner->expects('getCurrentStore')->once()->andReturnNull();

        $determiner->determineForCurrentUser();

        $this->assertSame(3, $determiner->stores->count());
        $this->assertNull($determiner->currentStore);
    }

    /**
     * @covers \App\Actions\Stores\StoreDeterminer::getCurrentStore()
     * @dataProvider providerCanSetCurrentStore
     */
    public function testCanSetCurrentStore(bool $hasSessionValue, bool $sessionValueMatchesStoreId, bool $hasStores, bool $shouldUpdateSession, bool $shouldHaveCurrentStore): void
    {
        /** @var StoreDeterminer $determiner */
        $determiner = app(StoreDeterminer::class);

        $store = Store::factory()->create();

        $sessionValue = null;
        if ($hasSessionValue) {
            $sessionValue = $sessionValueMatchesStoreId ? $store->id : ((int) $store->id) * 5;
        }

        Session::shouldReceive('get')->with('currentStore')->andReturn($sessionValue);

        if ($hasStores) {
            $determiner->stores = collect([$store]);
        }

        if ($shouldUpdateSession) {
            Session::shouldReceive('put')->with('currentStore', $store->id);
        } else {
            Session::expects('put')->never();
        }

        $currentStore = $this->invokeInaccessibleMethod($determiner, 'getCurrentStore');

        if ($shouldHaveCurrentStore) {
            $this->assertSame($store->id, $currentStore->id);
        } else {
            $this->assertNull($currentStore);
        }
    }

    /** @see testCanSetCurrentStore */
    public function providerCanSetCurrentStore(): Generator
    {
        yield 'has session value with valid store ID' => [
            'hasSessionValue' => true,
            'sessionValueMatchesStoreId' => true,
            'hasStores' => true,
            'shouldUpdateSession' => false,
            'shouldHaveCurrentStore' => true,
        ];

        yield 'has session value with invalid store ID' => [
            'hasSessionValue' => true,
            'sessionValueMatchesStoreId' => false,
            'hasStores' => true,
            'shouldUpdateSession' => false,
            'shouldHaveCurrentStore' => false,
        ];

        yield 'no session value, no stores' => [
            'hasSessionValue' => false,
            'sessionValueMatchesStoreId' => false,
            'hasStores' => false,
            'shouldUpdateSession' => false,
            'shouldHaveCurrentStore' => false,
        ];

        yield 'no session value, has stores' => [
            'hasSessionValue' => false,
            'sessionValueMatchesStoreId' => false,
            'hasStores' => true,
            'shouldUpdateSession' => true,
            'shouldHaveCurrentStore' => true,
        ];
    }
}
