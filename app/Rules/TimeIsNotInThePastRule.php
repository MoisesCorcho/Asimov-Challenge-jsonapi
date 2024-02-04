<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Appointment;

class TimeIsNotInThePastRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $apmt = new Appointment();

        if ( $apmt->timeIsInThePast($value) ) {
            $fail('You can not create an appointment with a time before the current time.');
        }
    }
}
