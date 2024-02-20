<?php

namespace App\JsonApi\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

Trait JsonApiResource
{
    abstract public function toJsonApi(): array;

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => $this->getResourceType(),
            'id' => (string) $this->getRouteKey(),
            'attributes' => $this->filterAttributes( $this->toJsonApi() ),
            'links' => [
                'self' => route('api.v1.'.$this->getResourceType().'.show', $this)
            ]
        ];
    }

    /**
     * Customize the response for a request
     *
     * @param Request $request
     * @param JsonResponse $response
     * @return void
     */
    public function withResponse(Request $request, JsonResponse $response)
    {
        $response->header(
            'Location',
            route('api.v1.'.$this->getResourceType().'.show', $this)
        );
    }

    /**
     * Filtra los atributos para incluir en la representación del recurso.
     *
     * @param array $attributes Los atributos del recurso.
     * @return array El array de atributos filtrados.
     */
    public function filterAttributes(array $attributes): array
    {
        return array_filter($attributes, function($value) {

            // Verifica si no se han especificado campos específicos para la respuesta.
            if (request()->isNotFilled('fields')) {
                return true; // Si no se han especificado campos, se incluye el atributo.
            }

            // Obtiene los campos solicitados para este tipo de recurso.
            $fields = explode(',', request('fields.'.$this->getResourceType()));

            /**
             * Verifica si el valor actual es la clave de la ruta del recurso.
             * Debemos hacer esta verificacion ya que el identificador del recurso
             * es siempre incluido en App\JsonApi\JsonApiQueryBuilder::sparseFieldset()
             */
            if ($value === $this->getRouteKey()) {
                // Si es la clave de la ruta, verifica si la clave de la ruta está presente en los campos solicitados.
                return in_array($this->getRouteKeyName(), $fields);
            }

            // Si no es la clave de la ruta, se incluye el atributo sin filtrar.
            return $value;
        });
    }

    public static function collection($resource): AnonymousResourceCollection
    {
        $collection = parent::collection($resource);

        $collection->with['links'] = ['self' => $resource->path()];

        return $collection;
    }

}
