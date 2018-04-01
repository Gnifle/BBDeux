<?php

namespace App\Http\Controllers\Admin;

use App\Models\Character;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CharacterController extends Controller
{
    public function index()
    {
        return Character::all();
    }

    public function create()
    {
        return 'Create a character';
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Character::$validation_required);

        if ($validator->fails()) {
            return redirect('admin/characters/create')
                ->withErrors($validator->errors(), 'create')
                ->withInput();
        }

        Character::create($request->all());

        return redirect('admin/characters');
    }

    public function show(Character $character)
    {
        return $character;
    }

    public function edit(Character $character)
    {
        return "Edit the {$character->name} character";
    }

    public function update(Request $request, Character $character)
    {
        $validator = Validator::make($request->all(), Character::$validation);

        if ($validator->fails()) {
            return redirect("admin/characters/{$character->slug}")
                ->withErrors($validator->errors(), 'update')
                ->withInput();
        }

        $character->update($request->all());

        return redirect('admin/characters');
    }

    public function destroy(Character $character)
    {
        $character->delete();

        return redirect('admin/characters');
    }
}
