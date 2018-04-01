<?php

namespace Tests\Http\Controllers\Admin;

use function App\Helpers\model_table;
use App\Traits\Tests\ImitatesUsersWithRolesAndPermissions;
use App\Models\Character;
use Tests\TestCase;

class CharacterControllerTest extends Testcase
{
    use ImitatesUsersWithRolesAndPermissions;

    protected function setUp()
    {
        parent::setUp();

        Character::truncate();
    }

    public function testIndex()
    {
        factory(Character::class, 3)->create();

        $this->beUserWithRoles('admin');

        $response = $this->call('GET', 'admin/characters');

        $response->assertStatus(200);

        $characters = json_decode($response->content());

        $this->assertCount(3, $characters);
    }

    public function testStoreSuccesfully()
    {
        $this->beUserWithRoles('admin');

        $this->call('POST', 'admin/characters', [
            'name' => 'Oliver',
            'gender' => Character::GENDER_MALE,
        ]);

        $this->assertDatabaseHas(
            model_table(Character::class),
            [
                'name' => 'Oliver',
                'slug' => 'oliver',
                'gender' => 'Male',
            ]
        );
    }

    public function testUpdateSuccesfully()
    {
        /** @var Character $character */
        $character = factory(Character::class)->create([
            'name' => 'Olive',
            'gender' => 'Female',
        ]);

        $this->beUserWithRoles('admin');

        $this->call('PUT', "admin/characters/{$character->slug}", [
            'name' => 'Oliver',
            'gender' => Character::GENDER_MALE,
        ]);

        $this->assertDatabaseHas(
            model_table(Character::class),
            [
                'name' => 'Oliver',
                'slug' => 'oliver',
                'gender' => 'Male',
            ]
        );

        $this->assertDatabaseMissing(
            model_table(Character::class),
            [
                'name' => 'Olive',
                'slug' => 'olive',
                'gender' => 'Female',
            ]
        );
    }

    public function testDeleteSuccessfully()
    {
        /** @var Character $character */
        $character = factory(Character::class)->create([
            'name' => 'Oliver',
            'gender' => 'Male',
        ]);

        $this->beUserWithRoles('admin');

        $this->call('DELETE', "admin/characters/{$character->slug}");

        $this->assertNotNull($character->fresh()->deleted_at);
    }

    public function testStoreFailInvalidArguments()
    {
        $this->beUserWithRoles('admin');

        $response = $this->call('POST', 'admin/characters', [
            'name' => 1,
            'gender' => 'None',
        ]);

        $response->assertRedirect('admin/characters/create');
        $response->assertSessionHasErrorsIn('create', ['name', 'gender']);
    }

    public function testUpdateFailInvalidArgument()
    {
        /** @var Character $character */
        $character = factory(Character::class)->create([
            'name' => 'Olive',
            'gender' => 'Female',
        ]);

        $this->beUserWithRoles('admin');

        $response = $this->call('PUT', "admin/characters/{$character->slug}", [
            'name' => 1,
            'gender' => 'None',
        ]);

        $response->assertRedirect("admin/characters/{$character->slug}");
        $response->assertSessionHasErrorsIn('update', ['name', 'gender']);
    }

    public function testDeleteFailInvalidCharacter()
    {
        factory(Character::class)->create([
            'name' => 'Oliver',
            'gender' => 'Male',
        ]);

        $this->beUserWithRoles('admin');

        $response = $this->call('DELETE', "admin/characters/non-existing-character-slug");

        $response->assertStatus(404);
    }

    public function testResourcesDenyAccessWithoutAdminRights()
    {
        // INDEX
        $response = $this->call('GET', "admin/characters");
        $response->assertRedirect('/login');

        // CREATE
        $response = $this->call('GET', "admin/characters/creates");
        $response->assertRedirect('/login');

        // STORE
        $response = $this->call('POST', 'admin/characters');
        $response->assertRedirect('/login');

        /** @var Character $character */
        $character = factory(Character::class)->create([
            'name' => 'Oliver',
            'gender' => 'Male',
        ]);

        // SHOW
        $response = $this->call('GET', "admin/characters/{$character->slug}");
        $response->assertRedirect('/login');

        // EDIT
        $response = $this->call('GET', "admin/characters/{$character->slug}");
        $response->assertRedirect('/login');

        // UPDATE
        $response = $this->call('PUT', "admin/characters/{$character->slug}");
        $response->assertRedirect('/login');

        // DELETE
        $response = $this->call('DELETE', "admin/characters/{$character->slug}");
        $response->assertRedirect('/login');
    }

    protected function tearDown()
    {
        Character::truncate();

        parent::tearDown();
    }
}
