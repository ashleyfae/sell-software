<?php

namespace Tests\Feature\Controllers\Customers\Checkout;

use App\Actions\Checkout\CreateStripeCheckoutSession;
use App\Enums\OrderItemType;
use App\Models\ProductPrice;
use App\Models\User;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * @covers \App\Http\Controllers\Customers\Checkout\PurchaseProductController
 */
class PurchaseProductControllerTest extends TestCase
{
    /**
     * @covers \App\Http\Controllers\Customers\Checkout\PurchaseProductController::__invoke()
     */
    public function testCanInvoke(): void
    {
        $user = User::factory()->create();
        $price = ProductPrice::factory()->create();

        $this->mock(CreateStripeCheckoutSession::class, function(MockInterface $mock) use($user, $price) {
            $mock->expects('execute')
                ->once()
                ->withArgs(function(?User $userArg, array $cartItemArg) use($user, $price) {
                    return $userArg->id === $user->id &&
                        count($cartItemArg) === 1 &&
                        $cartItemArg[0]->price->id === $price->id &&
                        $cartItemArg[0]->type === OrderItemType::New;
                })
                ->andReturn('https://stripe.com/checkout');
        });

        $response = $this->actingAs($user)->get(route('buy', $price));
        $response->assertRedirect('https://stripe.com/checkout');
    }
}
