<?php

namespace Tests\Models;

use App\Models\Price;
use App\Models\Weapon;
use Carbon\Carbon;
use Tests\TestCase;

class PriceTest extends Testcase
{
    protected function setUp()
    {
        parent::setUp();

        Price::truncate();
    }

    public function testAPriceCanBeFree()
    {
        /** @var Price $price */
        $price = factory(Price::class)->states('free', 'weapon')->create();

        $this->assertTrue($price->is_free);
    }

    public function testAPriceCanBeNonFree()
    {
        /** @var Price $price */
        $price = factory(Price::class)->states('weapon')->create();

        $this->assertFalse($price->is_free);
    }

    public function testAPriceCanBeIndefinite()
    {
        /** @var Price $price */
        $price = factory(Price::class)->states('weapon', 'indefinite')->create();

        $this->assertNull($price->to);
    }

    public function invalidPeriodProvider()
    {
        /** All the provided periods are set up in regards to period from 2018-01-15 00:00:00 to 2018-02-15 00:00:00 */
        return [
            /** #0 Overlap crossover */
            [
                Carbon::create(2018, 1, 1, 0, 0, 0),
                Carbon::create(2018, 1, 31, 0, 0, 0),
            ],
            /** #1 Period $to overlaps with 1 second */
            [
                Carbon::create(2018, 1, 1, 0, 0, 0),
                Carbon::create(2018, 1, 15, 0, 0, 1),
            ],
            /** #2 Period $from overlaps with 1 second */
            [
                Carbon::create(2018, 2, 14, 23, 59, 59),
                Carbon::create(2018, 3, 1, 0, 0, 0),
            ],
            /** #3 Period overlaps entire exisiting period */
            [
                Carbon::create(2018, 1, 1, 0, 0, 0),
                Carbon::create(2018, 3, 1, 0, 0, 0),
            ],
            /** #4 Period overlaps entire exisiting period, touching $to */
            [
                Carbon::create(2018, 1, 1, 0, 0, 0),
                Carbon::create(2018, 2, 15, 0, 0, 0),
            ],
            /** #5 Period overlaps entire exisiting period, touching $from */
            [
                Carbon::create(2018, 1, 15, 0, 0, 0),
                Carbon::create(2018, 3, 1, 0, 0, 0),
            ],
            /** #6 Period is within exisiting period */
            [
                Carbon::create(2018, 1, 20, 0, 0, 0),
                Carbon::create(2018, 1, 25, 0, 0, 0),
            ],
            /** #7 Period is within exisiting period, touching $from */
            [
                Carbon::create(2018, 1, 15, 0, 0, 0),
                Carbon::create(2018, 1, 25, 0, 0, 0),
            ],
            /** #8 Period is within exisiting period, touching $to */
            [
                Carbon::create(2018, 1, 20, 0, 0, 0),
                Carbon::create(2018, 2, 15, 0, 0, 0),
            ],
            /** #9 Indefinite period, starting before exisiting period */
            [
                Carbon::create(2018, 1, 1, 0, 0, 0),
                null,
            ],
            /** #10 Indefinite period, starting before exisiting period, touching $from */
            [
                Carbon::create(2018, 1, 15, 0, 0, 0),
                null,
            ],
            /** #11 Indefinite period, starting within exisiting period */
            [
                Carbon::create(2018, 1, 20, 0, 0, 0),
                null,
            ],
            /** #12 Indefinite period, starting 1 second before end of exisiting period */
            [
                Carbon::create(2018, 2, 14, 23, 59, 59),
                null,
            ],
        ];
    }

    /**
     * @dataProvider invalidPeriodProvider
     * @param Carbon $from
     * @param Carbon|null $to
     */
    public function testAPriceCannotBeSavedWithAnInvalidPeriod(Carbon $from, Carbon $to = null)
    {
        /** @var Price $existing_price */
        $existing_price = factory(Price::class)->states('weapon')->create([
            'from' => Carbon::create(2018, 1, 15, 0, 0, 0),
            'to' => Carbon::create(2018, 2, 15, 0, 0, 0),
        ]);

        /** @var Weapon $weapon */
        $weapon = $existing_price->priceable;

        $this->expectException(\InvalidArgumentException::class);

        factory(Price::class)->create([
            'from' => $from,
            'to' => $to,
            'priceable_id' => $weapon->id,
            'priceable_type' => Weapon::class,
        ]);
    }

    public function validPeriodProvider()
    {
        /** All the provided periods are set up in regards to period from 2018-01-15 00:00:00 to 2018-02-15 00:00:00 */
        return [
            /** #0 Period starts and ends before */
            [
                Carbon::create(2018, 1, 1, 0, 0, 0),
                Carbon::create(2018, 1, 14, 0, 0, 0),
            ],
            /** #1 Period starts and ends before, touching $to */
            [
                Carbon::create(2018, 1, 1, 0, 0, 0),
                Carbon::create(2018, 1, 15, 0, 0, 0),
            ],
            /** #2 Period starts and ends after, touching $from */
            [
                Carbon::create(2018, 2, 15, 0, 0, 0),
                Carbon::create(2018, 2, 28, 0, 0, 0),
            ],
            /** #3 Period starts and ends after */
            [
                Carbon::create(2018, 2, 16, 0, 0, 0),
                Carbon::create(2018, 2, 28, 0, 0, 0),
            ],
            /** #4 Indefinite period starts after, touching $from */
            [
                Carbon::create(2018, 2, 15, 0, 0, 0),
                null,
            ],
            /** #4 Indefinite period starts after */
            [
                Carbon::create(2018, 2, 16, 0, 0, 0),
                null,
            ],
        ];
    }

    /**
     * @dataProvider validPeriodProvider
     * @param Carbon $from
     * @param Carbon|null $to
     */
    public function testAPriceCanBeSavedWithAValidPeriod(Carbon $from, Carbon $to = null)
    {
        /** @var Price $existing_price */
        $existing_price = factory(Price::class)->states('weapon')->create([
            'from' => Carbon::create(2018, 1, 15, 0, 0, 0),
            'to' => Carbon::create(2018, 2, 15, 0, 0, 0),
        ]);

        /** @var Weapon $weapon */
        $weapon = $existing_price->priceable;

        factory(Price::class)->create([
            'from' => $from,
            'to' => $to,
            'priceable_id' => $weapon->id,
            'priceable_type' => Weapon::class,
        ]);

        $this->addToAssertionCount(1);
    }

    public function validPeriodProviderMultiplePeriods()
    {
        /** All the provided periods are set up in regards to two periods:
         * from 2018-01-15 00:00:00 to 2018-02-15 00:00:00
         */
        return [
            /** #0 Period starts and ends before */
            [
                Carbon::create(2018, 1, 1, 0, 0, 0),
                Carbon::create(2018, 1, 14, 0, 0, 0),
            ],
            /** #1 Period starts after first period and ends before the second period */
            [
                Carbon::create(2018, 3, 1, 0, 0, 0),
                Carbon::create(2018, 4, 1, 0, 0, 0),
            ],
            /** #2 Period starts after first period and ends before the second period, touching both */
            [
                Carbon::create(2018, 2, 15, 0, 0, 0),
                Carbon::create(2018, 4, 27, 0, 0, 0),
            ],
            /** #3 Indefinite period starts after the second period, touching $from */
            [
                Carbon::create(2018, 5, 27, 0, 0, 0),
                null,
            ],
        ];
    }

    /**
     * @dataProvider validPeriodProviderMultiplePeriods
     * @param Carbon $from
     * @param Carbon|null $to
     */
    public function testAPriceCanBeSavedWithAValidPeriodAroundTwoOtherPeriods(Carbon $from, Carbon $to = null)
    {
        /** @var Price $price */
        $price = factory(Price::class)->states('weapon')->create([
            'from' => Carbon::create(2018, 1, 15, 0, 0, 0),
            'to' => Carbon::create(2018, 2, 15, 0, 0, 0),
        ]);

        /** @var Weapon $weapon */
        $weapon = $price->priceable;

        factory(Price::class)->create([
            'priceable_id' => $weapon->id,
            'priceable_type' => Weapon::class,
            'from' => Carbon::create(2018, 4, 27, 0, 0, 0),
            'to' => Carbon::create(2018, 5, 27, 0, 0, 0),
        ]);

        factory(Price::class)->create([
            'from' => $from,
            'to' => $to,
            'priceable_id' => $weapon->id,
            'priceable_type' => Weapon::class,
        ]);

        $this->addToAssertionCount(1);
    }

    protected function tearDown()
    {
        parent::tearDown();

        Carbon::setTestNow();
    }
}
