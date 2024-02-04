<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Appointment;

class OfficeTimeRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $apmt = new Appointment();

        if ( $apmt->isOfficeTime($value) === false) {
            $fail("The time must be into the accepted hours. From ". env('START_TIME') ." to ". env('END_TIME'));
        }

    }
}
