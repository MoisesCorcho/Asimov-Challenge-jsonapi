<?php

namespace App\Http\Resources;

use App\JsonApi\Traits\JsonApiResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    use JsonApiResource;

    /**
     * Se especifican en un arreglo los atributos del recurso
     * que se quiere convertir en JSON.
     *
     * @return array
     */
    public function toJsonApi(): array
    {
        return [
            'date' => $this->date,
            'start-time' => $this->start_time,
            'email' => $this->email
        ];
    }

    /**
     * Se especifican las relaciones de los links que se quieran
     * generar.
     *
     * @return array
     */
    public function getRelationshipLinks(): array
    {
        return ['category', 'author'];
    }

    /**
     * Se especifican las relaciones que se quieran incluir dentro
     * del documento JSON:API.
     *
     * @return array
     */
    public function getIncludes(): array
    {
        // Se utiliza $this->whenLoaded() para verificar si la relación ha sido precargada.
        return [
            CategoryResource::make($this->whenLoaded('category')),
            AuthorResource::make($this->whenLoaded('author')),
            // Se llama al metodo collection ya que es una relacion de uno a muchos.
            CommentResource::collection($this->whenLoaded('comments'))
        ];
    }

}
