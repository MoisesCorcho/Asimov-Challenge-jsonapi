<?php

namespace App\Http\Resources;

use App\JsonApi\Traits\JsonApiResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    use JsonApiResource;

    /**
     * Se especifican en un arreglo los atributos del recurso
     * que queremos convertir en JSON
     *
     * @return array
     */
    public function toJsonApi(): array
    {
        return [
            'name' => $this->name
        ];
    }
}
