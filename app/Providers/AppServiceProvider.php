<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Validator::extendImplicit('class', function ($attribute, $value, $parameters, $validator) {
            return class_exists($value);
        });

        Validator::extendImplicit('instanceof', function ($attribute, $value, $parameters, $validator) {
            if (! $parameters || ! class_exists(array_get($parameters, 0, null))) {
                throw new \InvalidArgumentException(
                    "Invalid comparision class provided to instanceof:<class> validation"
                );
            }
            return class_exists($value) && is_a(new $value, $parameters[0], true);
        });

        Validator::extendImplicit('model', function ($attribute, $value, $parameters, $validator) {
            return $parameters && class_exists(array_get($parameters, 0, null)) ?
                class_exists($value) && is_a(new $value, 'App\Models\\' . $parameters[0], true) :
                class_exists($value) && new $value instanceof Model;
        });
    }
}
