<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;

use Illuminate\Validation\ValidationException;
use App\Http\Responses\JsonApiValidationErrorResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (NotFoundHttpException $e) {
            throw new JsonApi\NotFoundHttpException;
        });

        $this->renderable(function (BadRequestHttpException $e) {
            throw new JsonApi\BadRequestHttpException($e->getMessage());
        });
    }

    /**
     * Se sobreescribe esta funcion que es la que se ejecuta cuando
     * ocurren errores de validacion. Se sobreescribe para hacer que
     * los errores que salgan tengan la estructura necesaria en la
     * especificaion JSON:API.
     *
     * En resumen, se interceptan los mensajes de error de las
     * validaciones.
     *
     * @param [type] $request
     * @param ValidationException $exception
     * @return JsonApiValidationErrorResponse
     */
    protected function invalidJson($request, ValidationException $exception): JsonResponse
    {
        /** No se quiere formatear las respuestas de error para
         * validaciones del login, con lo cual, cuando la ruta
         * no sea la del login se formatea con especificacion
         * JSON:API y cuando sea de login, se retorna la
         * respuesta por defecto de Laravel
         */
        if ( !$request->routeIs('api.v1.login') ) {
            return new JsonApiValidationErrorResponse($exception);
        }

        return parent::invalidJson($request, $exception);
    }
}
