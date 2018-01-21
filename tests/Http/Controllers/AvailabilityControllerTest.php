<?php

namespace Tests\Http\Controllers;

use App\Http\Controllers\AvailabilityController;
use App\Models\Weapon;
use Carbon\Carbon;
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

    public function testStore()
    {
        /** @var Weapon $weapon */
        $weapon = factory(Weapon::class)->create();

        $request = new Request([
            'availability_id' => $weapon->id,
            'availability_type' => Weapon::class,
            'from' => '2018-01-01',
            'to' => '2018-02-01',
        ]);

        $this->controller->store($request);

        $this->addToAssertionCount(1);
    }
}
