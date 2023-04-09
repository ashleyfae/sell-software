<?php

namespace Tests\Unit\DataTransferObjects;

use App\DataTransferObjects\CartItem;
use App\Enums\OrderItemType;
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
            'expected' => new CartItem(price: $price, type: OrderItemType::New, licenseKey: null),
            'expectedException' => null,
        ];

        yield 'type is a string' => [
            'input' => [
                'type' => 'renewal',
                'price' => $price,
            ],
            'expected' => new CartItem(price: $price, type: OrderItemType::Renewal, licenseKey: null),
            'expectedException' => null,
        ];

        yield 'type is invalid' => [
            'input' => [
                'type' => 'invalid',
                'price' => $price,
            ],
            'expected' => new CartItem(price: $price, type: OrderItemType::Renewal, licenseKey: null),
            'expectedException' => ValueError::class,
        ];

        yield 'with license key' => [
            'input' => [
                'type' => 'renewal',
                'price' => $price,
                'licenseKey' => 'license-123'
            ],
            'expected' => new CartItem(price: $price, type: OrderItemType::Renewal, licenseKey: 'license-123'),
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

        $this->assertSame(
            [
                'price' => 123,
                'type' => 'new',
                'licenseKey' => 'license-123',
            ],
            (new CartItem(price: $price, type: OrderItemType::New, licenseKey: 'license-123'))->toArray()
        );
    }
}
