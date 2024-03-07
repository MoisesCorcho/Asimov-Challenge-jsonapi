<?php

namespace App\Exceptions\JsonApi;

use Exception;

class NotFoundHttpException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'errors' => [
                [
                    'title' => 'Not Found',
                    'detail' => "No records found with that id.",
                    'status' => '404'
                ]
            ]
        ], 404);
    }
}
