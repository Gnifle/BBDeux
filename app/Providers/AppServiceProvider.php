<?php

namespace App\Providers;

use App\Models\Character;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Validator::extend('class', function ($attribute, $value, $parameters, $validator) {
            return class_exists($value);
        });

        Validator::extend('instanceof', function ($attribute, $value, $parameters, $validator) {
            if (! $parameters || ! class_exists(array_get($parameters, 0, null))) {
                throw new \InvalidArgumentException(
                    "Invalid comparision class provided to instanceof:<class> validation"
                );
            }
            return class_exists($value) && is_a(new $value, $parameters[0], true);
        });

        Validator::extend('model', function ($attribute, $value, $parameters, $validator) {
            return $parameters && class_exists(array_get($parameters, 0, null)) ?
                class_exists($value) && is_a(new $value, 'App\Models\\' . $parameters[0], true) :
                class_exists($value) && new $value instanceof Model;
        });

        Validator::extend('interface', function ($attribute, $value, $parameters, $validator) {
            return $parameters ? is_a(new $value, 'App\Contracts\\' . $parameters[0], true) : false;
        }, 'Given model does not implement given interface');

        Validator::extend('gender', function ($attribute, $value, $parameters, $validator) {
            return in_array($value, Character::genders());
        }, 'Invalid gender provided');
    }
}
