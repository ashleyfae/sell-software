<?php

namespace Tests\Feature\Http\Controllers\Customers\Checkout;

use App\Http\Controllers\Customers\Checkout\PurchaseProductsController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(PurchaseProductsController::class)]
class PurchaseProductsControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @see \App\Http\Controllers\Customers\Checkout\PurchaseProductsController::__invoke()
     */
    public function testCanInvoke(): void
    {
        $this->markTestIncomplete(__METHOD__);
    }
}
