<?php

namespace Tests\Feature\Actions\Stores;

use App\Actions\Stores\StoreDeterminer;
use App\Models\Store;
use App\Models\User;
use App\Repositories\StoreRepository;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * @covers \App\Actions\Stores\StoreDeterminer
 */
class StoreDeterminerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @covers \App\Actions\Stores\StoreDeterminer::determineForRequest()
     */
    public function testCanDetermineForRequestWhenNotLoggedIn(): void
    {
        $request = Mockery::mock(Request::class);
        $request->allows('user')->andReturnNull();

        $determiner = app(StoreDeterminer::class);
        $determiner->determineForRequest($request);

        $this->assertTrue($determiner->stores->isEmpty());
        $this->assertNull($determiner->currentStore);
    }

    public function testCanDetermineForRequestWhenLoggedIn(): void
    {
        $user = User::factory()->create();
        $stores = Store::factory()->count(3)->for($user)->create();

        $this->mock(StoreRepository::class, function(MockInterface $mock) use($user, $stores) {
            $mock->expects('listForUser')
                ->once()
                ->withArgs(fn($arg) => $arg->is($user))
                ->andReturn($stores);
        });

        $request = Mockery::mock(Request::class);
        $request->allows('user')->andReturn($user);

        $determiner = app(StoreDeterminer::class);
        $determiner->determineForRequest($request);

        $this->assertSame(3, $determiner->stores->count());
        $this->assertNotNull($determiner->currentStore);
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
