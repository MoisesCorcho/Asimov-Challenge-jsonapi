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
            ],
            'data.relationships' => []
        ];
    }

    /**
     * Se sobreescribe el metodo validated() para que cuando se utilice
     * en el controlador solo se tengan los atributos que se necesitan
     * ya que al usar la especificacion JSON:API los atributos se
     * encuentran dentro ['data']['attributes']. De la misma forma
     * se añade al arreglo 'validated' los atributos referentes
     * a las relaciones.
     *
     * @return void
     */
    public function validated($key = null, $default = null)
    {
        // Obtenemos la llave data de la peticion recibida.
        $data = parent::validated()['data'];
        // Obtenemos la llave attributes de data
        $attributes = $data['attributes'];

        /** Vamos a añadir el atributo de llave foranea 'category_id' solo en caso
         * de que esta haya sido mandada en la peticion.*/
        if (isset($data['relationships'])) {

            $relationships = $data['relationships'];

            // Se añade al arreglo que se va a retornar la clave de la relacion.
            $attributes['category_id'] = $relationships['category']['data']['id'];
        }

        return $attributes;
    }
}
