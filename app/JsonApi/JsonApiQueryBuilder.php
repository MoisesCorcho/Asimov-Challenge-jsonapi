<?php

namespace App\JsonApi;

use Closure;
use Illuminate\Support\Str;

class JsonApiQueryBuilder
{

    /**
     * Retorna una función de cierre que aplica la clasificación de los registros según los campos permitidos.
     *
     * @param array $allowedSorts Los campos de clasificación permitidos (Se recibe en el Closure).
     * @return Closure Una función de cierre para aplicar la clasificación.
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

                    abort_unless(in_array($sortField, $allowedSorts), 400);

                    $this->orderBy($sortField, $sortDirection);
                }
            }

            return $this;
        };
    }

    /**
     * Retorna una función de cierre que aplica la clasificación de los registros según los campos permitidos.
     *
     * @param array $allowedSorts Los campos de clasificación permitidos (Se recibe en el Closure).
     * @return Closure Una función de cierre para aplicar la clasificación.
     */
    public function allowedFilters(): Closure
    {
        return function($allowedFilters) {
            /** @var Builder $this */
            foreach (request('filter', []) as $filter => $value) {
                abort_unless(in_array($filter, $allowedFilters), 400);

                $this->hasNamedScope($filter)
                    ? $this->{$filter}($value)
                    : $this->where($filter, 'LIKE', '%'.$value.'%');

            }

            return $this;
        };
    }

    /**
     * Retorna una función de cierre que selecciona un subconjunto de campos de la consulta.
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

            $resourceType = $this->model->getTable();

            if (property_exists($this->model, 'resourceType')) {
                $resourceType = $this->model->resourceType;
            }

            $fields = explode(',', request('fields.'.$resourceType));

            $getRouteKeyName = $this->model->getRouteKeyName();

            if ( !in_array($getRouteKeyName, $fields) ) {
                $fields[] = $getRouteKeyName;
            }

            return $this->addSelect($fields);
        };
    }

    /**
     * Retorna una función de cierre que paginará los resultados de la consulta.
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

}
