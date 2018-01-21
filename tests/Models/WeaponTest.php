<?php

namespace Tests\Models;

use App\Models\Availability;
use App\Models\Currency;
use App\Models\Price;
use App\Models\Stat;
use App\Models\Weapon;
use App\Traits\Tests\ProvidesPeriods;
use Carbon\Carbon;
use Tests\TestCase;

class WeaponTest extends TestCase
{
    use ProvidesPeriods;

    protected function setUp()
    {
        parent::setUp();

        Weapon::truncate();
        Availability::truncate();
        Stat::truncate();
        Price::truncate();
        Currency::truncate();
    }

    public function testAWeaponCanHaveNoStats()
    {
        /** @var Weapon $weapon */
        $weapon = factory(Weapon::class)->create();

        $this->assertEmpty($weapon->stats);
    }

    public function statCountProvider()
    {
        return [
            [1],
            [2],
            [10],
        ];
    }

    /**
     * @dataProvider statCountProvider
     * @param int $stat_count
     */
    public function testAWeaponCanHaveMultipleStats($stat_count)
    {
        /** @var Weapon $weapon */
        $weapon = factory(Weapon::class)->create();

        factory(Stat::class, $stat_count)->create([
            'statable_id' => $weapon->id,
            'statable_type' => Weapon::class,
        ]);

        $this->assertCount($stat_count, $weapon->stats);
    }

    public function weaponUnavailableDates()
    {
        return [
            [Carbon::create(2017, 10, 31, 0, 0, 0)],
            [Carbon::create(2017, 2, 1, 0, 0, 0)],
        ];
    }

    /**
     * @dataProvider weaponUnavailableDates
     * @param Carbon $test_now
     */
    public function testAWeaponCanBeUnavailableForPurchaseWithFixedPeriods(Carbon $test_now)
    {
        Carbon::setTestNow($test_now);

        /** @var Weapon $weapon */
        $weapon = factory(Weapon::class)->create();

        factory(Availability::class)->create([
            'availability_id' => $weapon->id,
            'availability_type' => Weapon::class,
            'from' => Carbon::create(2017, 11, 1, 0, 0, 0),
            'to' => Carbon::create(2018, 1, 31, 0, 0, 0),
        ]);

        $this->assertNotTrue($weapon->is_available);
    }

    public function weaponAvailabilityDates()
    {
        return [
            [Carbon::create(2017, 11, 1, 0, 0, 0)],
            [Carbon::create(2017, 12, 1, 0, 0, 0)],
            [Carbon::create(2018, 1, 1, 0, 0, 0)],
            [Carbon::create(2018, 1, 31, 0, 0, 0)],
        ];
    }

    /**
     * @dataProvider weaponAvailabilityDates
     * @param Carbon $test_now
     */
    public function testAWeaponCanBeAvailableForPurchaseWithFixedPeriods(Carbon $test_now)
    {
        Carbon::setTestNow($test_now);

        /** @var Weapon $weapon */
        $weapon = factory(Weapon::class)->create();

        factory(Availability::class)->create([
            'availability_id' => $weapon->id,
            'availability_type' => Weapon::class,
            'from' => Carbon::create(2017, 11, 1, 0, 0, 0),
            'to' => Carbon::create(2018, 1, 31, 0, 0, 0),
        ]);

        $this->assertTrue($weapon->is_available);
    }

    public function testAWeaponCanBeUnavailableForPurchaseWithUnfixedPeriods()
    {
        Carbon::setTestNow(Carbon::create(2017, 10, 31, 0, 0, 0));

        /** @var Weapon $weapon */
        $weapon = factory(Weapon::class)->create();

        factory(Availability::class)->create([
            'availability_id' => $weapon->id,
            'availability_type' => Weapon::class,
            'from' => Carbon::create(2017, 11, 1, 0, 0, 0),
            'to' => null,
        ]);

        $this->assertNotTrue($weapon->is_available);
    }

    public function testAWeaponCanBeAvailableForPurchaseWithUnfixedPeriods()
    {
        Carbon::setTestNow(Carbon::create(2019, 5, 24, 0, 0, 0));

        /** @var Weapon $weapon */
        $weapon = factory(Weapon::class)->create();

        factory(Availability::class)->create([
            'availability_id' => $weapon->id,
            'availability_type' => Weapon::class,
            'from' => Carbon::create(2017, 11, 1, 0, 0, 0),
            'to' => null,
        ]);

        $this->assertTrue($weapon->is_available);
    }

    public function availabilityScopeWhenDates()
    {
        return [
            [1, null],
            [1, Carbon::now()->addDay()],
            [0, Carbon::create(2020, 1, 1, 0, 0, 0)],
        ];
    }

    /**
     * @dataProvider availabilityScopeWhenDates
     * @param int $expected_count
     * @param Carbon $when
     */
    public function testAvailabilityScope(int $expected_count, Carbon $when = null)
    {
        factory(Availability::class)->states('weapon')->create(static::activePeriodMonth());
        factory(Weapon::class)->create();

        $this->assertCount(2, Weapon::all());

        $this->assertEquals($expected_count, Weapon::available($when)->count());
    }

    public function unavailabilityScopeWhenDates()
    {
        return [
            [1, null],
            [1, Carbon::now()->addDay()],
            [2, Carbon::create(2020, 1, 1, 0, 0, 0)],
        ];
    }

    /**
     * @dataProvider unavailabilityScopeWhenDates
     * @param int $expected_count
     * @param Carbon $when
     */
    public function testUnavailabilityScope(int $expected_count, Carbon $when = null)
    {
        factory(Availability::class)->states('weapon')->create(static::activePeriodMonth());
        factory(Availability::class)->states('weapon')->create(static::inactiveFuturePeriodMonth());

        $this->assertCount(2, Weapon::all());

        $this->assertEquals($expected_count, Weapon::unavailable($when)->count());
    }

    public function testAWeaponCanHaveAPrice()
    {
        /** @var Price $price */
        $price = factory(Price::class)->states('weapon', 'active_month')->create([
            'amount' => 200,
        ]);

        /** @var Weapon $weapon */
        $weapon = $price->priceable;

        $this->assertEquals($price->amount, $weapon->current_price->amount);
        $this->assertEquals($price->currency, $weapon->current_price->currency);
    }

    public function testAWeaponCanHaveNoCurrentlyActivePrice()
    {
        /** @var Weapon $weapon */
        $weapon = factory(Weapon::class)->create();

        $this->assertCount(0, $weapon->prices);
        $this->assertNull($weapon->current_price);
        $this->assertFalse($weapon->is_free);
    }

    public function testAWeaponCanHaveAFreePrice()
    {
        /** @var Weapon $weapon */
        $weapon = factory(Weapon::class)->create();

        factory(Price::class)->states('free')->create([
            'priceable_id' => $weapon->id,
            'priceable_type' => Weapon::class,
        ] + static::activePeriodMonth());

        $this->assertCount(1, $weapon->prices);
        $this->assertTrue($weapon->is_free);
    }

    public function testAWeaponCanHaveANonFreePrice()
    {
        /** @var Weapon $weapon */
        $weapon = factory(Weapon::class)->create();

        factory(Price::class)->create([
            'priceable_id' => $weapon->id,
            'priceable_type' => Weapon::class,
        ] + static::activePeriodMonth());

        $this->assertCount(1, $weapon->prices);
        $this->assertFalse($weapon->is_free);
    }

    public function weaponStatsAtDateProvider()
    {
        return [
            /** #0 Date in November, outside any periods */
            [
                'expected' => [
                    'Damage' => null,
                    'Cooldown' => null,
                    'Duration' => null,
                ],
                'when' => Carbon::create(2017, 11, 7, 2, 15, 00),
            ],
            /** #1 Date in December period */
            [
                'expected' => [
                    'Damage' => 50,
                    'Cooldown' => 4,
                    'Duration' => 4,
                ],
                'when' => Carbon::create(2017, 12, 14, 15, 46, 00),
            ],
            /** #2 Date in January period */
            [
                'expected' => [
                    'Damage' => 55,
                    'Cooldown' => 3.5,
                    'Duration' => 3.5,
                ],
                'when' => Carbon::create(2018, 1, 2, 23, 07, 00),
            ],
            /** #3 Date in February period */
            [
                'expected' => [
                    'Damage' => 60,
                    'Cooldown' => 3,
                    'Duration' => 3.5,
                ],
                'when' => Carbon::create(2018, 2, 27, 0, 11, 00),
            ],
            /** #4 Date in March, partially outside periods */
            [
                'expected' => [
                    'Damage' => null,
                    'Cooldown' => null,
                    'Duration' => 3.5,
                ],
                'when' => Carbon::create(2018, 3, 19, 7, 30, 00),
            ],
        ];
    }

    /**
     * @dataProvider weaponStatsAtDateProvider
     * @param array $expected
     * @param Carbon $when
     */
    public function testRetrieveWeaponStatsAtAGivenTime(array $expected, Carbon $when)
    {
        /** @var Weapon $weapon */
        $weapon = factory(Weapon::class)->create();

        /** Damage */

        factory(Availability::class)->create([
            'availability_id' => factory(Stat::class)->create([
                'title' => 'Damage',
                'value' => 50,
                'unit' => 'per second',
                'statable_id' => $weapon->id,
                'statable_type' => Weapon::class,
            ]),
            'availability_type' => Stat::class,
            'from' => Carbon::create(2017, 12, 1, 0, 0, 0),
            'to' => Carbon::create(2017, 12, 31, 23, 59, 59),
        ]);

        factory(Availability::class)->create([
            'availability_id' => factory(Stat::class)->create([
                'title' => 'Damage',
                'value' => 55,
                'unit' => 'per second',
                'statable_id' => $weapon->id,
                'statable_type' => Weapon::class,
            ]),
            'availability_type' => Stat::class,
            'from' => Carbon::create(2018, 1, 1, 0, 0, 0),
            'to' => Carbon::create(2018, 1, 31, 23, 59, 59),
        ]);

        factory(Availability::class)->create([
            'availability_id' => factory(Stat::class)->create([
                'title' => 'Damage',
                'value' => 60,
                'unit' => 'per second',
                'statable_id' => $weapon->id,
                'statable_type' => Weapon::class,
            ]),
            'availability_type' => Stat::class,
            'from' => Carbon::create(2018, 2, 1, 0, 0, 0),
            'to' => Carbon::create(2018, 2, 28, 23, 59, 59),
        ]);

        /** Cooldown */

        factory(Availability::class)->create([
            'availability_id' => factory(Stat::class)->create([
                'title' => 'Cooldown',
                'value' => 4,
                'unit' => 'seconds',
                'statable_id' => $weapon->id,
                'statable_type' => Weapon::class,
            ]),
            'availability_type' => Stat::class,
            'from' => Carbon::create(2017, 12, 1, 0, 0, 0),
            'to' => Carbon::create(2017, 12, 31, 23, 59, 59),
        ]);

        factory(Availability::class)->create([
            'availability_id' => factory(Stat::class)->create([
                'title' => 'Cooldown',
                'value' => 3.5,
                'unit' => 'seconds',
                'statable_id' => $weapon->id,
                'statable_type' => Weapon::class,
            ]),
            'availability_type' => Stat::class,
            'from' => Carbon::create(2018, 1, 1, 0, 0, 0),
            'to' => Carbon::create(2018, 1, 31, 23, 59, 59),
        ]);

        factory(Availability::class)->create([
            'availability_id' => factory(Stat::class)->create([
                'title' => 'Cooldown',
                'value' => 3,
                'unit' => 'seconds',
                'statable_id' => $weapon->id,
                'statable_type' => Weapon::class,
            ]),
            'availability_type' => Stat::class,
            'from' => Carbon::create(2018, 2, 1, 0, 0, 0),
            'to' => Carbon::create(2018, 2, 28, 23, 59, 59),
        ]);

        /** Duration */

        factory(Availability::class)->create([
            'availability_id' => factory(Stat::class)->create([
                'title' => 'Duration',
                'value' => 4,
                'unit' => 'seconds',
                'statable_id' => $weapon->id,
                'statable_type' => Weapon::class,
            ]),
            'availability_type' => Stat::class,
            'from' => Carbon::create(2017, 12, 1, 0, 0, 0),
            'to' => Carbon::create(2017, 12, 31, 23, 59, 59),
        ]);

        factory(Availability::class)->create([
            'availability_id' => factory(Stat::class)->create([
                'title' => 'Duration',
                'value' => 3.5,
                'unit' => 'seconds',
                'statable_id' => $weapon->id,
                'statable_type' => Weapon::class,
            ]),
            'availability_type' => Stat::class,
            'from' => Carbon::create(2018, 1, 1, 0, 0, 0),
            'to' => null,
        ]);

        $stats = $weapon->statsAt($when)
            ->mapWithKeys(function (Stat $stat) {
                return [
                    $stat['title'] => $stat['value'],
                ];
            });

        $this->assertEquals($expected['Damage'], $stats->get('Damage'));
        $this->assertEquals($expected['Cooldown'], $stats->get('Cooldown'));
        $this->assertEquals($expected['Duration'], $stats->get('Duration'));
    }

    protected function tearDown()
    {
        parent::tearDown();

        Carbon::setTestNow();
    }
}
