<?php

namespace Tests\Feature\Repositories;

use App\Models\Store;
use App\Repositories\StoreRepository;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * @covers \App\Repositories\StoreRepository
 */
class StoreRepositoryTest extends TestCase
{
    /**
     * @covers \App\Repositories\StoreRepository::getStoreForRequest()
     */
    public function testCanGetStoreForRequest(): void
    {
        $store = \Mockery::mock(Store::class);

        $request = new Request();
        $request->merge([
            'currentStore' => $store,
        ]);

        $this->assertSame($store, app(StoreRepository::class)->getStoreForRequest($request));
    }
}
