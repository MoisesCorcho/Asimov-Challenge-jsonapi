<?php

namespace App\JsonApi\Mixins;

use Closure;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Clase llamada a traves del metodo mixin de Builder
 * cada una de estas funciones es un Macro, la cual es una
 * funcion extendida de la propia clase Builder
 * es decir, que se puede usar como una funcion mas de la
 * clase
 */
class JsonApiQueryBuilder
{

    /**
     * Esta función (Macro) devuelve una función de cierre que se encarga de aplicar la
     * clasificación (ordenamiento) a una consulta de la base de datos. Toma un array
     * $allowedSorts como parámetro, que contiene los campos permitidos para ordenar.
     *
     * @param array $allowedSorts Los campos de clasificación permitidos (Se recibe en el Closure).
     * @return Closure
     */
    public function allowedSorts(): Closure
    {
        return function($allowedSorts) {

            /** @var Builder $this */

            if (request()->filled('sort')) {

                $sortFields = explode(',', request()->input('sort'));

                foreach ($sortFields as $sortField) {

                    $sortDirection = Str::of($sortField)->startsWith('-') ? 'desc' : 'asc';

                    $sortField = ltrim($sortField, '-');

                    if ( !in_array($sortField, $allowedSorts) ) {
                        throw new BadRequestHttpException("The sort field '{$sortField}' is not allowed in the '{$this->getResourceType()}' resource.");
                    }

                    $this->orderBy($sortField, $sortDirection);
                }
            }

            return $this;
        };
    }

    /**
     * Esta función (Macro) devuelve una función de cierre que se encarga de aplicar filtros a una
     * consulta de la base de datos. Toma un array $allowedFilters como parámetro, que
     * contiene los campos permitidos para filtrar.
     *
     * @param array $allowedFilters Los campos de filtro permitidos (Se recibe en el Closure).
     * @return Closure
     */
    public function allowedFilters(): Closure
    {
        return function($allowedFilters) {
            /** @var Builder $this */
            foreach (request('filter', []) as $filter => $value) {

                if ( !in_array($filter, $allowedFilters) ) {
                    throw new BadRequestHttpException("The filter '{$filter}' is not allowed in the '{$this->getResourceType()}' resource.");
                }

                /** El metodo hasNamedScope es util para verificar la existencias
                 * de un Scope en el modelo.
                 */
                $this->hasNamedScope($filter)
                    ? $this->{$filter}($value)
                    : $this->where($filter, 'LIKE', '%'.$value.'%');

            }

            return $this;
        };
    }

    /**
     * Esta funcion (Macro) hace la precarga de relaciones en caso de que
     * sea un include (relacion) permitido.
     *
     * Se debe recordar, que se deben añadir las relaciones que se quieran
     * incluir en el metodo 'getIncludes' dentro del LaravelResource, el cual
     * a su vez, debe estar usando el Trait 'JsonApiResource'.
     *
     * Ej. CategoryResource::make($this->whenLoaded('category'))
     *
     * @param array $allowedIncludes Los includes permitidos. (Se recibe en el Closure).
     *
     * @return Closure
     */
    public function allowedIncludes(): Closure
    {
        return function ($allowedIncludes) {
            /** @var Builder $this */

            if (request()->isNotFilled('include')) {
                return $this;
            }

            // Convertimos el String recibido en un array.
            $includes = explode(',', request()->input('include'));

            // Se recorre el array de includes.
            foreach ($includes as $include) {

                if ( !in_array($include, $allowedIncludes) ) {
                    throw new BadRequestHttpException("The include relationship '{$include}' is not allowed in the '{$this->getResourceType()}' resource.");
                }

                // Añadimos el include para la precarga.
                $this->with($include);
            }

            return $this;
        };
    }

    /**
     * Esta funcion (Macro) Retorna una función de cierre que selecciona
     * un subconjunto de campos de la consulta.
     *
     * @return Closure Una función de cierre para seleccionar campos específicos.
     */
    public function sparseFieldset(): Closure
    {
        return function () {
            /** @var Builder $this */

            if (request()->isNotFilled('fields')) {
                return $this;
            }

            $fields = explode(',', request('fields.'.$this->getResourceType()));

            $getRouteKeyName = $this->model->getRouteKeyName();

            if ( !in_array($getRouteKeyName, $fields) ) {
                $fields[] = $getRouteKeyName;
            }

            return $this->addSelect($fields);
        };
    }

    /**
     * Esta funcion (Macro) Retorna una función de cierre que paginará
     * los resultados de la consulta.
     *
     * @return Closure Una función de cierre para paginar los resultados.
     */
    public function jsonPaginate(): Closure
    {
        return function () {
            /** @var Builder $this */

            return $this->paginate(
                $perPage = request('page.size', 15),
                $columns = ['*'],
                $pageName = 'page[number]',
                $page = request('page.number', 1)
            )->appends(request()->only('sort', 'filter', 'page.size'));
        };
    }

    /**
     * Esta funcion (Macro) Obtiene el tipo de recurso ya sea del nombre
     * de la tabla en la base de datos o en la propiedad
     * resourceType en el modelo creada por nosotros.
     *
     * @return Closure
     */
    public function getResourceType(): Closure
    {
        return function() {

            /** @var Builder $this */
            if (property_exists($this->model, 'resourceType')) {
                return $this->model->resourceType;
            }

            return $this->model->getTable();
        };
    }

}
