<?php

namespace App\Http\Responses;

use App\Models\User;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;

/**
 * Class responsible for generating the token and adding permissions to it.
 * This is done in order to encapsulate the logic and make the code
 * more readable and maintainable.
 *
 * By implementing the Responsable interface in a class, it ensures that the
 * class defines the toResponse($request) method. This guarantees that any
 * class implementing this interface has the ability to generate an HTTP response.
 * When using a class that implements the Responsable interface in a Laravel
 * controller and returning it as a response to a request, Laravel automatically
 * calls the toResponse($request) method of that class. This means that there is
 * no need to manually call the toResponse() method in the controller; Laravel
 * handles it automatically for you.
 */
class TokenResponse implements Responsable
{
    /**
     * The user instance.
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Create a new TokenResponse instance.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Generate a token response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request): JsonResponse
    {
        /** Generate the Token.
         * Al momento de crear el token, como segundo parametros se añaden politicas
         * bajo la convencion que se ha escogido en donde se tiene el nombre del
         * recurso en singular seguido de la accion que puede realizar.
        */
        $plainTextToken = $this->user->createToken(
            $request->device_name,
            $this->user->permissions->pluck('name')->toArray() // Se traen solo los nombres de los permisos relacionados.
        )->plainTextToken;

        // Return a JSON response containing the plain text token.
        return response()->json([
            'plain_text_token' => $plainTextToken
        ]);
    }

}
