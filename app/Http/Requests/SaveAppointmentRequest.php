<?php

namespace App\Http\Requests;

use App\Rules\WeekendsRule;
use App\Rules\CrossHoursRule;
use App\Rules\OfficeTimeRule;
use Illuminate\Validation\Rule;
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
            'data.relationships.category.data.id' => [
                // Solo será requerido si no se tiene un appointment en la ruta, es decir, si no se está actualizando.
                Rule::requiredIf( ! $this->route('appointment') ),
                Rule::exists('categories', 'id')
            ],
            'data.relationships.author' => []
        ];
    }


    // /**
    //  * Se sobreescribe el metodo validated() para que cuando se utilice
    //  * en el controlador solo se tengan los atributos que se necesitan
    //  * ya que al usar la especificacion JSON:API los atributos se
    //  * encuentran dentro ['data']['attributes']. De la misma forma
    //  * se añade al arreglo 'validated' los atributos referentes
    //  * a las relaciones.
    //  *
    //  * @return void
    //  */
    // public function validated($key = null, $default = null)
    // {
    //     // Obtenemos la llave data de la peticion recibida.
    //     $data = parent::validated()['data'];

    //     // Obtenemos la llave attributes de data
    //     $attributes = $data['attributes'];

    //     /** Vamos a añadir el atributo de llave foranea 'category_id' solo en caso
    //      * de que esta haya sido mandada en la peticion.*/
    //     if (isset($data['relationships'])) {

    //         $relationships = $data['relationships'];

    //         // Se añade al arreglo que se va a retornar las claves de las relaciones.
    //         foreach ($relationships as $key => $relationship) {
    //             $attributes = array_merge($attributes, $this->{$key}($relationship));
    //         }

    //     }

    //     return $attributes;
    // }

    // /**
    //  * Se obtiene el id de la categoria y se retorna un arreglo
    //  * ['category_id' => 1] para luego combinarlo con el array que
    //  * se retornará en la funcion 'validated()'
    //  *
    //  * @return array
    //  */
    // public function category(array $relationship): array
    // {
    //     $categoryId = $relationship['data']['id'];

    //     return ['category_id' => $categoryId];
    // }

    // /**
    //  * Se obtiene el id del autor y se retorna un arreglo
    //  * ['user_id' => uuid] para luego combinarlo con el array que
    //  * se retornará en la funcion 'validated()'
    //  *
    //  * @param array $relationship
    //  * @return array
    //  */
    // public function author(array $relationship): array
    // {
    //     $authorId = $relationship['data']['id'];

    //     return ['user_id' => $authorId];
    // }
}
