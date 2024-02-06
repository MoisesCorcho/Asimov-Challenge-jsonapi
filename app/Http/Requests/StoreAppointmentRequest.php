<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CrossHoursRule;
use App\Rules\WeekendsRule;
use App\Rules\TimeIsNotInThePastRule;
use App\Rules\OfficeTimeRule;

class StoreAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date'       => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:'.now('America/Bogota')->toDateString(),
                new WeekendsRule
            ],
            'start_time' => [
                'required',
                'date_format:H:i',
                new TimeIsNotInThePastRule,
                new CrossHoursRule,
                new OfficeTimeRule
            ],
            'email'      => [
                'required',
                'email'
            ]
        ];
    }

    public function messages()
    {
        return [];
    }
}
