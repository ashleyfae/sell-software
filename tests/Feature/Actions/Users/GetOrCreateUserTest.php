<?php

namespace Tests\Feature\Actions\Users;

use App\Actions\Users\GetOrCreateUser;
use App\DataTransferObjects\Customer;
use App\Enums\Currency;
use App\Models\StripeCustomer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Mockery\MockInterface;
use Tests\TestCase;

class GetOrCreateUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @covers \App\Actions\Users\GetOrCreateUser::getUserByStripeId()
     * @dataProvider providerCanGetUserByStripeId
     */
    public function testCanGetUserByStripeId(string $stripeId, bool $shouldFindUser): void
    {
        $dbUser = User::factory()
            ->has(
                StripeCustomer::factory()
                    ->count(1)
                    ->state(fn(array $attributes, User $user) => ['stripe_id' => 'cus_123'])
            )
            ->create();

        $customer = new Customer(
            email: 'test@example.com',
            stripeCustomerId: $stripeId,
            name: 'Jane Doe'
        );

        $user = $this->invokeInaccessibleMethod(app(GetOrCreateUser::class), 'getUserByStripeId', $customer);

        if ($shouldFindUser) {
            $this->assertInstanceOf(User::class, $user);
            $this->assertSame($dbUser->id, $user->id);
        } else {
            $this->assertNull($user);
        }
    }

    /** @see testCanGetUserByStripeId */
    public function providerCanGetUserByStripeId(): \Generator
    {
        yield 'Stripe ID is in database' => [
            'stripe_id' => 'cus_123',
            'shouldFindUser' => true,
        ];

        yield 'Stripe ID not in database' => [
            'stripe_id' => 'cus_456',
            'shouldFindUser' => false,
        ];
    }

    /**
     * @covers \App\Actions\Users\GetOrCreateUser::getUserByEmail()
     * @dataProvider providerCanGetUserByEmail
     */
    public function testCanGetUserByEmail(string $email, bool $shouldFindUser): void
    {
        /** @var User $dbUser */
        $dbUser = User::factory()->create(['email' => 'test@example.com']);

        $customer = new Customer(
            email: $email,
            stripeCustomerId: 'cus_123',
            name: 'Jane Doe'
        );

        $user = $this->invokeInaccessibleMethod(app(GetOrCreateUser::class), 'getUserByEmail', $customer);

        if ($shouldFindUser) {
            $this->assertInstanceOf(User::class, $user);
            $this->assertSame($dbUser->id, $user->id);
        } else {
            $this->assertNull($user);
        }
    }

    /** @see testCanGetUserByEmail */
    public function providerCanGetUserByEmail(): \Generator
    {
        yield 'email is in database' => [
            'email' => 'test@example.com',
            'shouldFindUser' => true,
        ];

        yield 'email is not in database' => [
            'email' => 'invalid@example.com',
            'shouldFindUser' => false,
        ];
    }

    /**
     * @covers \App\Actions\Users\GetOrCreateUser::createStripeCustomerRecord()
     */
    public function testCanCreateStripeCustomerRecord(): void
    {
        /** @var User $dbUser */
        $user = User::factory()->create(['email' => 'test@example.com']);

        $customer = new Customer(
            email: 'test@example.com',
            stripeCustomerId: 'cus_123',
            name: 'Jane Doe',
            currency: Currency::GBP
        );

        $this->assertDatabaseMissing(StripeCustomer::class, [
            'user_id' => $user->id,
        ]);

        $this->invokeInaccessibleMethod(app(GetOrCreateUser::class), 'createStripeCustomerRecord', $user, $customer);

        $this->assertDatabaseHas(StripeCustomer::class, [
            'user_id' => $user->id,
            'currency' => 'gbp',
        ]);
    }

    /**
     * @covers \App\Actions\Users\GetOrCreateUser::createNewUser()
     */
    public function testCanCreateNewUser(): void
    {
        $customer = new Customer(
            email: 'test@example.com',
            stripeCustomerId: 'cus_123',
            name: 'Jane Doe',
            currency: Currency::GBP
        );

        $this->assertDatabaseMissing(User::class, ['email' => 'test@example.com']);

        Auth::expects('login');

        /** @var User $user */
        $user = $this->invokeInaccessibleMethod(app(GetOrCreateUser::class), 'createNewUser', $customer);

        $this->assertDatabaseHas(User::class, ['email' => 'test@example.com']);
        $this->assertSame('Jane Doe', $user->name);
        $this->assertSame('test@example.com', $user->email);

        $this->assertDatabaseHas(StripeCustomer::class, [
            'user_id' => $user->id,
            'currency' => 'gbp',
        ]);
    }
}
