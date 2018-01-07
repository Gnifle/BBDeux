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
            'statable_type' => 'weapon',
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
            'availability_type' => 'weapon',
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
            'availability_type' => 'weapon',
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
            'availability_type' => 'weapon',
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
            'availability_type' => 'weapon',
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

    public function testAWeaponCanHaveAFreePrice()
    {
        /** @var Weapon $weapon */
        $weapon = factory(Weapon::class)->create();

        factory(Price::class)->states('free')->create([
            'priceable_id' => $weapon->id,
            'priceable_type' => 'weapon',
        ] + static::activePeriodMonth());

        $this->assertTrue($weapon->is_free);
    }

    public function testAWeaponCanHaveANonFreePrice()
    {
        /** @var Weapon $weapon */
        $weapon = factory(Weapon::class)->create();

        factory(Price::class)->create([
            'priceable_id' => $weapon->id,
            'priceable_type' => 'weapon',
        ]);

        $this->assertFalse($weapon->is_free);
    }

    protected function tearDown()
    {
        parent::tearDown();

        Carbon::setTestNow();
    }
}
