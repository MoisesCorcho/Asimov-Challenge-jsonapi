<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ValidateJsonApidocument;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ValidateJsonApiDocumentTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::any('test_route', fn() => 'OK')->middleware(ValidateJsonApidocument::class);
    }

    /** @test */
    public function data_is_required(): void
    {
        $this->postJson('test_route', [])
            ->assertJsonApiValidationErrors('data');

        $this->patchJson('test_route', [])
            ->assertJsonApiValidationErrors('data');
    }

    /** @test */
    public function data_must_be_an_array(): void
    {
        $this->postJson('test_route', [
            'data' => 'string'
        ])->assertJsonApiValidationErrors('data');

        $this->patchJson('test_route', [
            'data' => 'string'
        ])->assertJsonApiValidationErrors('data');
    }

    /** @test */
    public function data_type_is_required(): void
    {
        $this->postJson('test_route', [
            'data' => [
                'attributes' => ['name' => 'test']
            ]
        ])->assertJsonApiValidationErrors('data.type');

        $this->patchJson('test_route', [
            'data' => [
                'attributes' => ['name' => 'test']
            ]
        ])->assertJsonApiValidationErrors('data.type');

        /** En caso de que las llaves 'id' y 'data' NO se encuentren directamente dentro
         * de la llave 'data', sino que se encuentren dentro de un objeto, se quiere que tambien
         * se un caso exitoso.
         */
        $this->patchJson('test_route', [
            'data' => [
                [
                    'id' => '1',
                    'type' => 'string'
                ]
            ]
        ])->assertSuccessful();
    }

    /** @test */
    public function data_type_must_be_a_string(): void
    {
        $this->postJson('test_route', [
            'data' => [
                'type' => 56,
                'attributes' => []
            ]
        ])->assertJsonApiValidationErrors('data.type');

        $this->patchJson('test_route', [
            'data' => [
                'type' => 56,
                'attributes' => ['name' => 'test']
            ]
        ])->assertJsonApiValidationErrors('data.type');
    }

    /** @test */
    public function data_attribute_is_required(): void
    {
        $this->postJson('test_route', [
            'data' => [
                'type' => 'appointments'
            ]
        ])->assertJsonApiValidationErrors('data.attributes');

        $this->patchJson('test_route', [
            'data' => [
                'type' => 'appointments'
            ]
        ])->assertJsonApiValidationErrors('data.attributes');
    }

    /** @test */
    public function data_attribute_must_be_an_array(): void
    {
        $this->postJson('test_route', [
            'data' => [
                'type' => 'appointments',
                'attributes' => 45
            ]
        ])->assertJsonApiValidationErrors('data.attributes');

        $this->patchJson('test_route', [
            'data' => [
                'type' => 'appointments',
                'attributes' => 45
            ]
        ])->assertJsonApiValidationErrors('data.attributes');
    }

    /** @test */
    public function data_id_is_required(): void
    {
        $this->patchJson('test_route', [
            'data' => [
                'type' => 'appointments',
                'attributes' => ['name' => 'test']
            ]
        ])->assertJsonApiValidationErrors('data.id');
    }

    /** @test */
    public function data_id_must_be_a_string(): void
    {
        $this->patchJson('test_route', [
            'data' => [
                'id' => 17,
                'type' => 'appointments',
                'attributes' => ['name' => 'test']
            ]
        ])->assertJsonApiValidationErrors('data.id');
    }

    /** @test */
    public function only_accepts_valid_json_api_document(): void
    {
        $this->postJson('test_route', [
            'data' => [
                'type' => 'appointments',
                'attributes' => [
                    'name' => 'test'
                ]
            ]
        ])->assertSuccessful();

        $this->patchJson('test_route', [
            'data' => [
                'id' => '7',
                'type' => 'appointments',
                'attributes' => [
                    'name' => 'test'
                ]
            ]
        ])->assertSuccessful();
    }
}
