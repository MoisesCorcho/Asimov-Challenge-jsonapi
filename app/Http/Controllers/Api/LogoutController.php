<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LogoutController extends Controller
{

    /**
     * Todas las solicitudes a los metodos de este controlador
     * seran interceptadas por el middleware 'auth' con el guard
     * 'sanctum'que garantiza que el usurio este autenticado
     * mediante sanctum
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // Se elimina el token actual asociado al usuario.
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
