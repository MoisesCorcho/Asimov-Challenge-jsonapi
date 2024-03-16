<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\TokenResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'email'       => ['required', 'email'],
            'password'    => ['required'],
            'device_name' => ['required']
        ]);

        $user = User::whereEmail($request->email)->first();

        /** Si no se encuentra el usuario en base de datos o si la
         * contraseña no es correcta, se arroja una excepcion
         * ValidationException.
         *
         * auth.failed es un mensaje de validacion que se encuentra en
         * resources/lang.
         */
        if ( ! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')]
            ]);
        }

        return new TokenResponse($user);
    }
}
