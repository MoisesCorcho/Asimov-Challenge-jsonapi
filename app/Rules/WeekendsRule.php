<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Appointment;

class WeekendsRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $aptm = new Appointment();

        if ( $aptm->isWeekend($value) ) {
            $fail('You can not create an appointment on weekends.');
        }

    }
}
