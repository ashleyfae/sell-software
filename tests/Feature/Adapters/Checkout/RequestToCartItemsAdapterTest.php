<?php

namespace Tests\Feature\Adapters\Checkout;

use App\Adapters\Checkout\RequestToCartItemsAdapter;
use App\DataTransferObjects\CartItem;
use App\Enums\OrderItemType;
use App\Exceptions\Checkout\InvalidProductsToPurchaseException;
use App\Exceptions\Checkout\MissingProductsToPurchaseException;
use App\Models\ProductPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[CoversClass(RequestToCartItemsAdapter::class)]
class RequestToCartItemsAdapterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @see \App\Adapters\Checkout\RequestToCartItemsAdapter::execute()
     */
    #[DataProvider('providerCanExecute')]
    public function testCanExecute(array $priceUuids, ?string $expectedException): void
    {
        /** @var RequestToCartItemsAdapter&MockInterface $adapter */
        $adapter = $this->partialMock(RequestToCartItemsAdapter::class);
        $adapter->shouldAllowMockingProtectedMethods();

        $request = \Mockery::mock(Request::class);

        $adapter->expects('getPriceUuidsFromRequest')
            ->once()
            ->with($request)
            ->andReturn($priceUuids);

        $collection = \Mockery::mock(Collection::class);

        $adapter->expects('getPricesFromIds')
            ->times($expectedException ? 0 : 1)
            ->with($priceUuids)
            ->andReturn($collection);

        $cartItems = [\Mockery::mock(CartItem::class)];

        $adapter->expects('makeCartItems')
            ->times($expectedException ? 0 : 1)
            ->with($collection)
            ->andReturn($cartItems);

        if ($expectedException) {
            $this->expectException($expectedException);
        }

        $this->assertSame($cartItems, $adapter->execute($request));
    }

    /** @see testCanExecute */
    public static function providerCanExecute(): \Generator
    {
        yield 'empty uuids throws exception' => [
            'priceUuids' => [],
            'expectedException' => MissingProductsToPurchaseException::class,
        ];

        yield 'valid uuids' => [
            'priceUuids' => ['uuid-1'],
            'expectedException' => null,
        ];
    }

    /**
     * @see \App\Adapters\Checkout\RequestToCartItemsAdapter::getPriceUuidsFromRequest()
     */
    #[DataProvider('providerCanGetPriceUuidsFromRequest')]
    public function testCanGetPriceUuidsFromRequest(mixed $productsInput, array $expected): void
    {
        $request = \Mockery::mock(Request::class);
        $request->expects('input')
            ->once()
            ->with('products')
            ->andReturn($productsInput);

        $this->assertSame(
            $expected,
            $this->invokeInaccessibleMethod(app(RequestToCartItemsAdapter::class), 'getPriceUuidsFromRequest', $request)
        );
    }

    /** @see testCanGetPriceUuidsFromRequest */
    public static function providerCanGetPriceUuidsFromRequest(): \Generator
    {
        yield 'null input' => [
            'productsInput' => null,
            'expected' => [],
        ];

        yield 'string input with valid uuid' => [
            'productsInput' => '0c3a10b0-264d-4acf-a21b-4357ba81ecde',
            'expected' => ['0c3a10b0-264d-4acf-a21b-4357ba81ecde'],
        ];

        yield 'array input with 1 valid uuid and 1 invalid' => [
            'productsInput' => ['0c3a10b0-264d-4acf-a21b-4357ba81ecde', 'not-a-uuid'],
            'expected' => ['0c3a10b0-264d-4acf-a21b-4357ba81ecde'],
        ];
    }

    /**
     * @see \App\Adapters\Checkout\RequestToCartItemsAdapter::getPricesFromIds()
     */
    #[DataProvider('providerCanGetPricesFromIds')]
    public function testCanGetPricesFromIds(bool $uuidIsValid, bool $priceIsActive, ?string $expectedException): void
    {
        /** @var ProductPrice $price */
        $price = ProductPrice::factory()->create([
            'is_active' => $priceIsActive,
        ]);

        $uuidToCheck = $uuidIsValid ? $price->uuid : $this->faker->uuid;

        if ($expectedException) {
            $this->expectException($expectedException);
        }

        /** @var Collection $prices */
        $prices = $this->invokeInaccessibleMethod(app(RequestToCartItemsAdapter::class), 'getPricesFromIds', [$uuidToCheck]);

        if (! $expectedException) {
            $this->assertSame(1, $prices->count());
            $this->assertSame($price->id, $prices->first()->id);
        }
    }

    /** @see testCanGetPricesFromIds */
    public static function providerCanGetPricesFromIds(): \Generator
    {
        yield 'invalid uuid' => [
            'uuidIsValid' => false,
            'priceIsActive' => false,
            'expectedException' => InvalidProductsToPurchaseException::class,
        ];

        yield 'valid uuid but product is inactive' => [
            'uuidIsValid' => true,
            'priceIsActive' => false,
            'expectedException' => InvalidProductsToPurchaseException::class,
        ];

        yield 'valid uuid, product is active' => [
            'uuidIsValid' => true,
            'priceIsActive' => true,
            'expectedException' => null,
        ];
    }

    /**
     * @see \App\Adapters\Checkout\RequestToCartItemsAdapter::makeCartItems()
     */
    public function testCanMakeCartItems(): void
    {
        $price1 = ProductPrice::factory()->create();

        /** @var CartItem[] $cartItems */
        $cartItems = $this->invokeInaccessibleMethod(app(RequestToCartItemsAdapter::class), 'makeCartItems', collect([$price1]));

        $this->assertCount(1, $cartItems);
        $this->assertSame($price1, $cartItems[0]->price);
        $this->assertSame(OrderItemType::New, $cartItems[0]->type);
        $this->assertNull($cartItems[0]->license);
    }
}
