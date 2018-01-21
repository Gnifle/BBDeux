<?php

namespace App\Helpers {

    use Illuminate\Database\Eloquent\Model;

    if (! function_exists('model_table')) {

        /**
         * @param string $class Absolute class name of a Model descendant
         *
         * @return string|null Return the model's table name, or null if non-Model given
         */
        function model_table(string $class)
        {
            $model = new $class;

            if (! $model instanceof Model) {
                return null;
            }

            return $model->getTable();
        }
    }
}
