<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PeriodService;
use App\Models\Weapon;

class WeaponController extends Controller
{
    /** @var PeriodService */
    protected $period_service;

    public function __construct(PeriodService $period_service)
    {
        $this->period_service = $period_service;
    }

    public function index()
    {
        return 'List of weapons';
    }

    public function create()
    {
        return 'Create a weapon';
    }

    public function store(Request $request)
    {
        // POST
    }

    public function show(Weapon $weapon)
    {
        return $weapon;
    }

    public function edit(Weapon $weapon)
    {
        return "Edit the {$weapon->title} weapon";
    }

    public function update(Request $request, Weapon $weapon)
    {
        // PUT
    }

    public function destroy(Weapon $weapons)
    {
        // DELETE
    }
}
