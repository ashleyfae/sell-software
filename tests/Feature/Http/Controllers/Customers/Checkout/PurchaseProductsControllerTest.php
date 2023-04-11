<?php

namespace Tests\Feature\Http\Controllers\Customers\Checkout;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @covers \App\Http\Controllers\Customers\Checkout\PurchaseProductsController
 */
class PurchaseProductsControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @covers \App\Http\Controllers\Customers\Checkout\PurchaseProductsController::__invoke()
     */
    public function testCanInvoke(): void
    {
        $this->markTestIncomplete(__METHOD__);
    }
}
