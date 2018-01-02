<?php

namespace Tests\Models;

use App\Models\Stat;
use App\Models\Weapon;
use Tests\TestCase;

class WeaponTest extends TestCase
{
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
}
