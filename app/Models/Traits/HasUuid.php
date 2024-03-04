<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

Trait HasUuid {

    public function getIncrementing()
    {
        return false;
    }

    /**
     * Esta funcion se ejecuta cuando el modelo está 'listo'
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating( function($model) {
            $model->id = Str::uuid()->toString();
        });
    }

}
