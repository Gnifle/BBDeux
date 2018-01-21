<?php

namespace Tests\Models;

use App\Models\Stat;
use App\Models\CharacterClass;
use App\Models\Weapon;
use Tests\TestCase;

class CharacterClassTest extends Testcase
{
    public function testACharacterClassHasOneAssociatedCharacter()
    {
        /** @var CharacterClass $class */
        $class = factory(CharacterClass::class)->create();

        $this->assertNotNull($class->character);
    }

    public function testACharacterClassCanHaveNoWeapons()
    {
        /** @var CharacterClass $class */
        $class = factory(CharacterClass::class)->create();

        $this->assertEmpty($class->weapons);
    }

    public function numberOfWeaponsForCharacterClassProvider()
    {
        return [
            [1],
            [2],
            [10],
        ];
    }

    /**
     * @dataProvider numberOfWeaponsForCharacterClassProvider
     * @param int $weapon_count Number of weapons to associate to a class in the test
     */
    public function testACharacterClassCanHaveOneOrMultipleWeapons($weapon_count)
    {
        /** @var CharacterClass $class */
        $class = factory(CharacterClass::class)->create();

        factory(Weapon::class, $weapon_count)->create([
            'class_id' => $class->id,
        ]);

        $this->assertCount($weapon_count, $class->weapons);
    }

    public function testACharacterClassCanHaveNoStats()
    {
        /** @var CharacterClass $class */
        $class = factory(CharacterClass::class)->create();

        $this->assertEmpty($class->stats);
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
    public function testACharacterClassCanHaveMultipleStats($stat_count)
    {
        /** @var CharacterClass $class */
        $class = factory(CharacterClass::class)->create();

        factory(Stat::class, $stat_count)->create([
            'statable_id' => $class->id,
            'statable_type' => CharacterClass::class,
        ]);

        $this->assertCount($stat_count, $class->stats);
    }
}
