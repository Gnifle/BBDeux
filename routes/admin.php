<?php

Route::group(
    [
        'prefix' => 'admin',
        'middleware' => [
            'auth',
            'role:admin',
        ],
    ],
    function () {

        /** Dashboard */
        Route::get('/', function () {
            return view('admin.dashboard');
        });

        /** Characters */
        Route::resource('characters', 'CharacterController');

        /** Classes */
        Route::resource('classes', 'CharacterClassController');

        /** Weapons */
        Route::resource('weapons', 'WeaponController');
    }
);
