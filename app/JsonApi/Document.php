<?php

namespace App\JsonApi;

use Illuminate\Support\Collection;

/**
 * Esta clase está diseñada para ayudar en la creación de documentos
 * JSON:API siguiendo la especificación JSON:API. esta clase
 * hereda de la clase Collection para que se puedan usar sus
 * metodos, por lo mismo, usamos el constructor de la clase
 * Colletion asi como el atributo fundamental de aquella clase
 * $items
 */
class Document extends Collection
{

    /**
     * Método estático para crear una nueva instancia de Document
     * con el tipo especificado.
     *
     * @param string $type El tipo del documento JSON:API.
     *
     * @return Document Una nueva instancia de Document con el
     * tipo especificado.
     */
    public static function type(string $type): Document
    {
        return new self([
            'data' => [
                'type' => $type
            ]
        ]);
    }

    /**
     * Establece el identificador del documento JSON:API.
     *
     * @param mixed $id El identificador del documento JSON:API.
     * @return Document La instancia actual de Document.
     */
    public function id($id): Document
    {
        // Establece el identificador en el array de elementos del documento.
        $this->items['data']['id'] = (string) $id;

        // Retorna la instancia actual para permitir el encadenamiento de métodos.
        return $this;
    }

    /**
     * Establece los atributos del documento JSON:API.
     *
     * @param array $attributes Los atributos del documento JSON:API.
     * @return Document La instancia actual de Document.
     */
    public function attributes(array $attributes): Document
    {
        // Establece los atributos en el array de elementos del documento.
        $this->items['data']['attributes'] = $attributes;

        // Retorna la instancia actual para permitir el encadenamiento de métodos.
        return $this;
    }

    /**
     * Establece los enlaces del documento JSON:API.
     *
     * @param array $links Los enlaces del documento JSON:API.
     * @return Document La instancia actual de Document.
     */
    public function links(array $links): Document
    {
        // Establece los enlaces en el array de elementos del documento.
        $this->items['data']['links'] = $links;

        // Retorna la instancia actual para permitir el encadenamiento de métodos.
        return $this;
    }

    /**
     * Establece las relaciones documento JSON:API.
     *
     * @param array $relationships las relaciones.
     * @return Document La instancia actual de Document.
     */
    public function relationships(array $relationships): Document
    {
        /**
         * Recorre las diferentes relaciones que se pueden mandar
         * y las establece en el array de elementos del documento.
         */
        foreach ($relationships as $key => $relationship) {
            $this->items['data']['relationships'][$key] = [
                'data' => [
                    'type' => $relationship->getResourceType(),
                    'id'   => (string) $relationship->getRouteKey()
                ]
            ];
        }

        // Retorna la instancia actual para permitir el encadenamiento de métodos.
        return $this;
    }
}
