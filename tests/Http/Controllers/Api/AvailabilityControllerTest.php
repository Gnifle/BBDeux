<?php

namespace Tests\Http\Controllers\Api;

use function App\Helpers\model_table;
use App\Http\Controllers\Api\AvailabilityController;
use App\Models\Availability;
use App\Models\Weapon;
use Illuminate\Http\Request;
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

        $this->assertContains('Given model does not implement given interface', $response['errors'][0]);

        $this->assertDatabaseMissing(
            model_table(Availability::class),
            $availability_data
        );
    }
}
