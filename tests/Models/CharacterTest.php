<?php

namespace Tests\Models;

use App\Models\Character;
use App\Models\CharacterClass;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class CharacterTest extends Testcase
{
    public function testACharacterMayHaveNoRelatedCharacterClass()
    {
        /** @var Character $character */
        $character = factory(Character::class)->create();

        $this->assertNull($character->class);
    }
    
    public function testACharacterMayHaveOneRelatedCharacterClass()
    {
        /** @var Character $character */
        $character = factory(Character::class)->create();

        /** @var CharacterClass $class */
        $class = factory(CharacterClass::class)->create();

        $class->character()->associate($character)->save();

        $this->assertNotNull($character->class);
    }

    public function genderProvider()
    {
        return [
            [Character::GENDER_MALE],
            [Character::GENDER_FEMALE],
            [Character::GENDER_UNKNOWN],
            ['_test'],
        ];
    }

    /**
     * @dataProvider genderProvider
     * @param string $gender
     */
    public function testACharacterCanOnlyHaveOneOfThreePreSpecifiedGender(string $gender)
    {
        if (! in_array($gender, [Character::GENDER_MALE, Character::GENDER_FEMALE, Character::GENDER_UNKNOWN])) {
            $this->expectException(QueryException::class);
        }

        /** @var Character $character */
        $character = factory(Character::class)->create([
            'gender' => $gender,
        ]);

        $this->assertNotNull($character);
    }
}
