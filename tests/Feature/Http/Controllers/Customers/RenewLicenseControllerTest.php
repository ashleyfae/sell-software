<?php

namespace Tests\Feature\Http\Controllers\Customers;

use App\Actions\Checkout\CreateStripeCheckoutSession;
use App\Enums\OrderItemType;
use App\Http\Controllers\Customers\RenewLicenseController;
use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(RenewLicenseController::class)]
class RenewLicenseControllerTest extends TestCase
{
    use RefreshDatabase;

    protected License $license;

    public function setUp(): void
    {
        parent::setUp();

        $this->license = License::factory()->create();
    }

    /**
     * @see \App\Http\Controllers\Customers\RenewLicenseController::__invoke()
     */
    public function testCanInvoke(): void
    {
        $this->mock(CreateStripeCheckoutSession::class, function(MockInterface $mock) {
            $mock->expects('execute')
                ->once()
                ->withArgs(function(User $userArg, array $cartItemArg) {
                    return $userArg->id === $this->license->user->id &&
                        count($cartItemArg) === 1 &&
                        $cartItemArg[0]->price->id === $this->license->productPrice->id &&
                        $cartItemArg[0]->type === OrderItemType::Renewal &&
                        $cartItemArg[0]->license->id === $this->license->id;
                })
                ->andReturn('https://stripe.com/checkout');
        });

        $response = $this->actingAs($this->license->user)->get(route('customer.licenses.renew', $this->license));
        $response->assertRedirect('https://stripe.com/checkout');
    }

    public function testCannotInvokeIfNotLoggedIn(): void
    {
        $response = $this->get(route('customer.licenses.renew', $this->license));

        $response->assertRedirectToRoute('login');
    }

    public function testCannotInvokeIfUserDoesNotOwnLicense(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('customer.licenses.renew', $this->license));

        $response->assertForbidden();
    }
}
