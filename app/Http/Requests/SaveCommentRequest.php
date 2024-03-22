<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SaveCommentRequest extends FormRequest
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
            'data.attributes.body' => ['required'],
            'data.relationships.appointment.data.id' => [
                'required',
                Rule::exists('appointments', 'id')
            ],
            'data.relationships.author.data.id' => [
                'required',
                Rule::exists('users', 'id')
            ]
        ];
    }
}
