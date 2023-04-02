<?php

namespace Tests\Feature\Http\Middleware;

use App\Actions\Stores\StoreDeterminer;
use App\Http\Middleware\DetermineStoreMiddleware;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;

/**
 * @covers \App\Http\Middleware\DetermineStoreMiddleware
 */
class DetermineStoreMiddlewareTest extends TestCase
{
    /**
     * @covers \App\Http\Middleware\DetermineStoreMiddleware::handle()
     */
    public function testCanHandle(): void
    {
        $store = Mockery::mock(Store::class);
        $request = new Request();

        $this->assertNull($request->input('currentStore'));

        $determiner = Mockery::mock(StoreDeterminer::class);
        $determiner->expects('determineForRequest')
            ->once()
            ->with($request);

        $determiner->currentStore = $store;

        $middleware = new DetermineStoreMiddleware($determiner);

        $middleware->handle($request, function($request) use($store) {
            $this->assertEquals($store, $request->input('currentStore'));

            return Mockery::mock(Response::class);
        });
    }
}
