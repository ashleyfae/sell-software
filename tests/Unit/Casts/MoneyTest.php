<?php

namespace Tests\Unit\Casts;

use App\Casts\Money;
use App\Enums\Currency;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ValueError;

class MoneyTest extends TestCase
{
    /**
     * @covers \App\Casts\Money::get()
     * @dataProvider providerCanGet
     */
    public function testCanGet(mixed $value, array $attributes, ?Currency $expectedCurrency, ?int $expectedAmount, ?string $expectedException): void
    {
        if ($expectedException) {
            $this->expectException($expectedException);
        }

        $money = (new Money())->get(
            model: \Mockery::mock(Model::class),
            key: 'price',
            value: $value,
            attributes: $attributes
        );

        $this->assertEquals($expectedCurrency, $money->currency);
        $this->assertEquals($expectedAmount, $money->amount);
    }

    /** @see testCanGet */
    public function providerCanGet(): \Generator
    {
        yield 'missing currency' => [
            'value' => 500,
            'attributes' => [],
            'expectedCurrency' => null,
            'expectedAmount' => null,
            'expectedException' => InvalidArgumentException::class,
        ];

        yield 'integer amount with valid currency' => [
            'value' => 500,
            'attributes' => ['currency' => 'usd'],
            'expectedCurrency' => Currency::USD,
            'expectedAmount' => 500,
            'expectedException' => null,
        ];

        yield 'string amount with valid currency' => [
            'value' => '500',
            'attributes' => ['currency' => Currency::USD->value],
            'expectedCurrency' => Currency::USD,
            'expectedAmount' => 500,
            'expectedException' => null,
        ];

        yield 'integer amount with invalid currency' => [
            'value' => 500,
            'attributes' => ['currency' => 'rup'],
            'expectedCurrency' => Currency::USD,
            'expectedAmount' => 500,
            'expectedException' => ValueError::class,
        ];
    }

    /**
     * @covers \App\Casts\Money::set()
     * @dataProvider providerCanSet
     */
    public function testCanSet(mixed $value, array $attributes, mixed $expectedValue, ?string $expectedException): void
    {
        if ($expectedException) {
            $this->expectException($expectedException);
        }

        $money = (new Money())->set(
            model: \Mockery::mock(Model::class),
            key: 'price',
            value: $value,
            attributes: $attributes
        );

        $this->assertSame($expectedValue, $money);
    }

    /** @see testCanSet */
    public function providerCanSet() : \Generator
    {
        yield 'string value, missing currency attribute' => [
            'value' => '15.99',
            'attributes' => [],
            'expectedValue' => null,
            'expectedException' => InvalidArgumentException::class,
        ];

        yield 'string value, invalid currency attribute' => [
            'value' => '15.99',
            'attributes' => ['currency' => 'rup'],
            'expectedValue' => null,
            'expectedException' => ValueError::class,
        ];

        yield 'string value, valid currency attribute' => [
            'value' => '15.99',
            'attributes' => ['currency' => Currency::USD->value],
            'expectedValue' => 1599,
            'expectedException' => null,
        ];

        yield 'integer value' => [
            'value' => 1599,
            'attributes' => ['currency' => Currency::USD->value],
            'expectedValue' => 1599,
            'expectedException' => null,
        ];

        yield 'Money value' => [
            'value' => new \App\Helpers\Money(currency: Currency::GBP, amount: 10),
            'attributes' => ['currency' => Currency::GBP->value],
            'expectedValue' => [
                'price' => 10,
                'currency' => 'gbp',
            ],
            'expectedException' => null,
        ];
    }
}
