<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

        /** auth.failed es un mensaje de validacion que se encuentra en
         * resources/lang
         */
        if ( ! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')]
            ]);
        }

        /** Generate the Token.
         * Al momento de crear el token, como segundo parametros se añaden politicas
         * bajo la convencion que se ha escogido en donde se tiene el nombre del
         * recurso en singular seguido de la accion que puede realizar.
        */
        $plainTextToken = $user->createToken(
            $request->device_name,
            [
                'appointment:create',
                'appointment:update',
                'appointment:delete',
            ]
        )->plainTextToken;

        return response()->json([
            'plain_text_token' => $plainTextToken
        ]);
    }
}
