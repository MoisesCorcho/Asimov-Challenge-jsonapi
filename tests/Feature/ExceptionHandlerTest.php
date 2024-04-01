<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExceptionHandlerTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function json_api_errors_are_only_shown_to_requests_with_the_prefix_api(): void
    {
        $this->getJson('api/route')
            ->assertJsonApiError(
                detail: "The route api/route could not be found."
            );

        $this->getJson('api/v1/invalid-resource/invalid-id')
            ->assertJsonApiError(
                detail: "The route api/v1/invalid-resource/invalid-id could not be found."
            );
    }

    /** @test */
    public function default_laravel_error_is_shown_to_requests_outside_the_prefix_api(): void
    {
        // Si la ruta NO comienza con 'api' se quiere ver el mensaje de error por defecto de Laravel.
        // En este caso, se siguen enviando los headers pertenecientes a Json Api Especification.
        $this->getJson('non/api/route')
            ->assertJson([
                'message' => 'The route non/api/route could not be found.'
            ]);

        // Si la ruta NO comienza con 'api' se quiere ver el mensaje de error por defecto de Laravel.
        // Se desactivan los Json Api Headers para que NO se envien en la peticion del este test.
        $this->withoutJsonApiHeaders()
            ->getJson('non/api/route')
            ->assertJson([
                'message' => 'The route non/api/route could not be found.'
            ]);
    }
}
