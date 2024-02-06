<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

use function Laravel\Prompts\error;

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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        $title = $exception->getMessage();

        /*
        Esta es otra manera de hacer el proceso de darle formato a los errores segun json api specification

        $errors = [];

        foreach($exception->errors() as $field => $messages) {
            $pointer = "/".str_replace('.', '/', $field);

            $errors[] = [
                'title' => $title,
                'detail' => $messages[0],
                'source' => [
                    'pointer' => $pointer
                ]
            ];
        }*/

        $errors = collect($exception->errors())
            ->map(function ($messages, $field) use ($title) {
                return [
                    'title' => $title,
                    'detail' => $messages[0],
                    'source' => [
                        'pointer' => "/".str_replace('.', '/', $field)
                    ]
                ];
            })->values();

        return response()->json([
            'errors' => $errors
        ], 422);
    }
}
