<?php

namespace Tests\Feature\Actions\Licenses;

use App\Actions\Licenses\CalculateExpirationDate;
use App\Enums\PeriodUnit;
use Generator;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[CoversClass(CalculateExpirationDate::class)]
class CalculateExpirationDateTest extends TestCase
{
    /**
     * @see \App\Actions\Licenses\CalculateExpirationDate::calculate()
     */
    #[DataProvider('providerCanCalculate')]
    public function testCanCalculate(
        ?string $baseDate,
        ?int $period,
        PeriodUnit $periodUnit,
        ?string $expectedDate
    ) {
        $actualDate = app(CalculateExpirationDate::class)
            ->calculate(
                baseDate: Carbon::parse($baseDate),
                period: $period,
                periodUnit: $periodUnit
            );

        if (is_null($expectedDate)) {
            $this->assertNull($expectedDate);
        } else {
            $this->assertSame($expectedDate, $actualDate->format('Y-m-d'));
        }
    }

    /** @see testCanCalculate */
    public static function providerCanCalculate(): Generator
    {
        yield 'no base date uses current date' => [
            'baseDate' => null,
            'period' => 1,
            'periodUnit' => PeriodUnit::Year,
            'expectedDate' => date('Y-m-d', strtotime('+1 year')),
        ];

        yield 'date in past uses current date' => [
            'baseDate' => '2022-01-01',
            'period' => 1,
            'periodUnit' => PeriodUnit::Year,
            'expectedDate' => date('Y-m-d', strtotime('+1 year')),
        ];

        yield 'base date in future, 1 month' => [
            'baseDate' => '2075-01-01',
            'period' => 1,
            'periodUnit' => PeriodUnit::Month,
            'expectedDate' => '2075-02-01',
        ];

        yield 'null period returns null' => [
            'baseDate' => '2075-01-01',
            'period' => null,
            'periodUnit' => PeriodUnit::Month,
            'expectedDate' => null,
        ];

        yield 'lifetime unit returns null' => [
            'baseDate' => '2075-01-01',
            'period' => 5,
            'periodUnit' => PeriodUnit::Lifetime,
            'expectedDate' => null,
        ];

        yield 'null period and lifetime unit returns null' => [
            'baseDate' => '2075-01-01',
            'period' => null,
            'periodUnit' => PeriodUnit::Lifetime,
            'expectedDate' => null,
        ];
    }
}
