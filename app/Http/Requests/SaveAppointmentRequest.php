<?php

namespace App\Http\Requests;

use App\Rules\WeekendsRule;
use App\Rules\CrossHoursRule;
use App\Rules\OfficeTimeRule;
use App\Rules\TimeIsNotInThePastRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveAppointmentRequest extends FormRequest
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
            'data.attributes.date' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:'.now()->toDateString(),
                new WeekendsRule
            ],
            'data.attributes.start_time' => [
                'required',
                'date_format:H:i',
                new TimeIsNotInThePastRule,
                new OfficeTimeRule,
                new CrossHoursRule
            ],
            'data.attributes.email' => [
                'required',
                'email'
            ]
        ];
    }

    public function validated($key = null, $default = null)
    {
        return parent::validated()['data']['attributes'];
    }
}
