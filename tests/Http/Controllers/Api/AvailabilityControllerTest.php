<?php

namespace Tests\Http\Controllers\Api;

use function App\Helpers\model_table;
use App\Http\Controllers\Api\AvailabilityController;
use App\Models\Availability;
use App\Models\Weapon;
use Tests\TestCase;

class CharacterTest extends Testcase
{
    /** @var AvailabilityController */
    protected $controller;

    protected function setUp()
    {
        parent::setUp();

        $this->controller = $this->app->make(AvailabilityController::class);
    }

    public function testStoreSuccesfully()
    {
        /** @var Weapon $weapon */
        $weapon = factory(Weapon::class)->create();

        $availability_data = [
            'availability_id' => $weapon->id,
            'availability_type' => Weapon::class,
            'from' => '2018-01-01',
            'to' => '2018-02-01',
        ];

        $this->json('POST', 'api/availability', $availability_data)
            ->assertStatus(201);

        $this->assertDatabaseHas(
            model_table(Availability::class),
            $availability_data
        );
    }

    public function testStoreFailValidationInvalidType()
    {
        /** @var Weapon $weapon */
        $weapon = factory(Weapon::class)->create();

        $availability_data = [
            'availability_id' => $weapon->id,
            'availability_type' => 'App\Models\Availability',
            'from' => '2018-01-01',
            'to' => '2018-02-01',
        ];

        $response = $this->json('POST', 'api/availability', $availability_data)
            ->assertStatus(400)
            ->json();

        $this->assertTrue(
            in_array('Given model does not implement given interface', $response['errors'])
        );

        $this->assertDatabaseMissing(
            model_table(Availability::class),
            $availability_data
        );
    }

    public function testStoreFailStoringInvalidPeriod()
    {
        /** @var Weapon $weapon */
        $weapon = factory(Weapon::class)->create();

        $availability_data = [
            'availability_id' => $weapon->id,
            'availability_type' => Weapon::class,
            'from' => '2018-02-01',
            'to' => '2018-01-01',
        ];

        $response = $this->json('POST', 'api/availability', $availability_data);

        $response->assertStatus(400);
    }

    public function testStoreFailStoringOverlappingPeriod()
    {
        /** @var Weapon $weapon */
        $weapon = factory(Weapon::class)->create();

        factory(Availability::class)->states('weapon')->create([
            'availability_id' => $weapon->id,
            'from' => '2018-01-01',
            'to' => '2018-02-01',
        ]);

        $availability_data = [
            'availability_id' => $weapon->id,
            'availability_type' => Weapon::class,
            'from' => '2018-01-15',
            'to' => '2018-02-28',
        ];

        $response = $this->json('POST', 'api/availability', $availability_data);

        $response->assertStatus(400);
    }

    public function testUpdateSuccesfully()
    {
        /** @var Weapon $weapon */
        $weapon = factory(Weapon::class)->create();

        /** @var Availability $availability */
        $availability = factory(Availability::class)->states('weapon')->create([
            'availability_id' => $weapon->id,
            'from' => '2018-01-01',
            'to' => '2018-02-01',
        ]);

        $updated_data = [
            'from' => '2018-02-01',
            'to' => '2018-03-01',
        ];

        $response = $this->json('PUT', "api/availability/{$availability->id}", $updated_data);

        $response->assertStatus(204);

        $this->assertDatabaseHas(
            model_table(Availability::class),
            ['id' => $availability->id] + $updated_data
        );
    }

    public function testDeleteSuccessfully()
    {
        /** @var Weapon $weapon */
        $weapon = factory(Weapon::class)->create();

        /** @var Availability $availability */
        $availability = factory(Availability::class)->states('weapon')->create([
            'availability_id' => $weapon->id,
            'from' => '2018-01-01',
            'to' => '2018-02-01',
        ]);

        $this->json('DELETE', "api/availability/{$availability->id}");

        $this->assertDatabaseMissing(
            model_table(Availability::class),
            [
                'id' => $availability->id,
            ]
        );
    }

    public function testDeleteFailNonExistingPeriod()
    {
        /** @var Weapon $weapon */
        $weapon = factory(Weapon::class)->create();

        /** @var Availability $availability */
        $availability = factory(Availability::class)->states('weapon')->create([
            'availability_id' => $weapon->id,
            'from' => '2018-01-01',
            'to' => '2018-02-01',
        ]);

        $invalid_id = $availability->id + 1;

        $this->json('DELETE', "api/availability/{$invalid_id}")
            ->assertStatus(404);
    }
}
