<?php

namespace App\Providers;

use Illuminate\Http\Request;
use App\JsonApi\JsonApiRequest;
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

        // Agrega el mixin JsonApiRequest a la clase TestResponse para agregar funciones nuevas que permitan trabajar con el documento JSON:API
        // mas facilmente.
        Request::mixin(new JsonApiRequest);

    }
}
