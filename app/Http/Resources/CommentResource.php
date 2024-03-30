<?php

namespace App\Http\Resources;

use App\JsonApi\Traits\JsonApiResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    use JsonApiResource;

    public function toJsonApi(): array
    {
        return [
            'body' => $this->body
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
        return ['appointment', 'author'];
    }
}
