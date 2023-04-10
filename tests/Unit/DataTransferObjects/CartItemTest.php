<?php

namespace Tests\Unit\DataTransferObjects;

use App\DataTransferObjects\CartItem;
use App\Enums\OrderItemType;
use App\Models\License;
use App\Models\ProductPrice;
use Generator;
use PHPUnit\Framework\TestCase;
use ValueError;

/**
 * @covers \App\DataTransferObjects\CartItem
 */
class CartItemTest extends TestCase
{
    /**
     * @covers \App\DataTransferObjects\CartItem::fromArray()
     * @dataProvider providerCanMakeFromArray
     */
    public function testCanMakeFromArray(array $input, ?CartItem $expected, ?string $expectedException): void
    {
        if ($expectedException) {
            $this->expectException($expectedException);
        }

        $this->assertEquals($expected, CartItem::fromArray($input));
    }

    /** @see testCanMakeFromArray */
    public function providerCanMakeFromArray(): Generator
    {
        $price = new ProductPrice();
        $license = new License();

        yield 'missing price' => [
            'input' => [
                'type' => OrderItemType::New,
            ],
            'expected' => null,
            'expectedException' => \InvalidArgumentException::class,
        ];

        yield 'missing type' => [
            'input' => [
                'price' => $price,
            ],
            'expected' => new CartItem(price: $price, type: OrderItemType::New, license: null),
            'expectedException' => null,
        ];

        yield 'type is a string' => [
            'input' => [
                'type' => 'renewal',
                'price' => $price,
            ],
            'expected' => new CartItem(price: $price, type: OrderItemType::Renewal, license: null),
            'expectedException' => null,
        ];

        yield 'type is invalid' => [
            'input' => [
                'type' => 'invalid',
                'price' => $price,
            ],
            'expected' => new CartItem(price: $price, type: OrderItemType::Renewal, license: null),
            'expectedException' => ValueError::class,
        ];

        yield 'with license key' => [
            'input' => [
                'type' => 'renewal',
                'price' => $price,
                'license' => $license,
            ],
            'expected' => new CartItem(price: $price, type: OrderItemType::Renewal, license: $license),
            'expectedException' => null,
        ];
    }

    /**
     * @covers \App\DataTransferObjects\CartItem::toArray()
     */
    public function testCanConvertToArray(): void
    {
        $price = new ProductPrice();
        $price->id = 123;

        $license = new License();
        $license->id = 456;

        $this->assertSame(
            [
                'price' => 123,
                'type' => 'new',
                'license' => 456,
            ],
            (new CartItem(price: $price, type: OrderItemType::New, license: $license))->toArray()
        );
    }
}
