<?php

namespace App\Providers;

use Illuminate\Http\Request;
use App\JsonApi\JsonApiQueryBuilder;
use App\JsonApi\JsonApiTestResponse;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;

/**
 * JsonApiServiceProvider es responsable de registrar los mixins para proporcionar funcionalidades específicas para la API JSON.
 */
class JsonApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Se agregan mixins a las clases Builder y TestResponse para proporcionar funcionalidades específicas de la API JSON.

        // Agrega el mixin JsonApiQueryBuilder a la clase Builder de Eloquent para aplicar funcionalidades de construcción de consultas JSON API.
        Builder::mixin(new JsonApiQueryBuilder);

        // Agrega el mixin JsonApiTestResponse a la clase TestResponse para aplicar funcionalidades de respuesta de prueba JSON API.
        TestResponse::mixin(new JsonApiTestResponse);

        // Se verifica si se estan enviando los headers referentes a la especificacion JSON:API
        Request::macro('isJsonApi', function() {
            /** @var Request $this */
            if ($this->header('accept') === 'application/vnd.api+json') {
                return true;
            }

            return $this->header('content-type') === 'application/vnd.api+json';
        });
    }
}
