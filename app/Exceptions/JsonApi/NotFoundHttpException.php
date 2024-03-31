<?php

namespace App\Exceptions\JsonApi;

use Exception;

class NotFoundHttpException extends Exception
{
    public function render($request)
    {
        /** Para cuando se intente acceder a rutas que comienzan con el prefijo 'api'
         * pero que NO tienen un modelo asociado como pueden ser las rutas para los
         * metodos 'show' o 'update'.
         *
         * Ej. 'api/route'
         */
        $detail = "The route {$request->path()} could not be found.";

        /** Para cuando se intente acceder a rutas que comienzan con el prefijo 'api'
         * y que SI tiene un modelo asociado como lo son las rutas para los metodos
         * 'show' o 'update'
         *
         * Ej. 'api/v1/appointments/1'
         */
        if ( str($this->getMessage())->startsWith('No query results for model') ) {
            $detail = "No records found with that id.";
        }

        return response()->json([
            'errors' => [
                [
                    'title' => 'Not Found',
                    'detail' => $detail,
                    'status' => '404'
                ]
            ]
        ], 404);
    }
}
