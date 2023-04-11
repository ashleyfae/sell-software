<?php

namespace Tests\Feature\Actions\Checkout;

use App\Actions\Checkout\RequestToCartItemsAdapter;
use App\DataTransferObjects\CartItem;
use App\Exceptions\Checkout\MissingProductsToPurchaseException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * @covers \App\Actions\Checkout\RequestToCartItemsAdapter
 */
class RequestToCartItemsAdapterTest extends TestCase
{
    /**
     * @covers \App\Actions\Checkout\RequestToCartItemsAdapter::execute()
     * @dataProvider providerCanExecute
     */
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
    public function providerCanExecute(): \Generator
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
     * @covers \App\Actions\Checkout\RequestToCartItemsAdapter::getPriceUuidsFromRequest()
     * @dataProvider providerCanGetPriceUuidsFromRequest
     */
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
    public function providerCanGetPriceUuidsFromRequest(): \Generator
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
     * @covers \App\Actions\Checkout\RequestToCartItemsAdapter::getPricesFromIds()
     */
    public function testCanGetPricesFromIds(): void
    {
        $this->markTestIncomplete(__METHOD__);
    }

    /**
     * @covers \App\Actions\Checkout\RequestToCartItemsAdapter::makeCartItems()
     */
    public function testCanMakeCartItems(): void
    {
        $this->markTestIncomplete(__METHOD__);
    }
}
