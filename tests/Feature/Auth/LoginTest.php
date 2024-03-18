<?php

namespace Tests\Feature\Auth;

use App\Models\Permission;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutJsonApiHeaders();
    }

    /** @test */
    public function can_issue_access_tokens(): void
    {
        $user = User::factory()->create();

        $url = route('api.v1.login');

        $data = $this->validCredentials([
            'email' => $user->email
        ]);

        $response = $this->postJson($url, $data);

        $token = $response->json('plain_text_token');

        $dbToken = PersonalAccessToken::findToken($token);

        /** A traves de la relacion polimorfica 'tokenable' se puede
         * acceder al usuario que tiene asociado y con el metodo 'is'
         * se compara el usuario asociado al token con el usuario que
         * se creó primeramente en el test.
         */
        $this->assertTrue($dbToken->tokenable->is($user));
    }

    /** @test */
    public function only_one_access_token_can_be_issued_at_a_time(): void
    {
        $user = User::factory()->create();

        $accessToken = $user->createToken($user->name)->plainTextToken;

        // Se manda el header en la peticion (request).
        $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->postJson(route('api.v1.login'))
            ->assertNoContent();

        // Se convierte a colecccion solo porque se necesita que se iterable
        // el segundo parametro.
        $this->assertCount(1, collect($user->tokens()->count()));
    }

    /** @test */
    public function user_permissions_are_assigned_as_abilities_to_the_token(): void
    {
        $user = User::factory()->create();

        $permission1 = Permission::factory()->create();
        $permission2 = Permission::factory()->create();
        $permission3 = Permission::factory()->create();

        /** En algun momento del registro o en la administracion, se hace
         * la supocision de que en algun momento se le asignan estos
         * permisos al usuario.
         */
        $user->givePermissionTo($permission1);
        $user->givePermissionTo($permission2);

        $url = route('api.v1.login');

        $data = $this->validCredentials([
            'email' => $user->email
        ]);

        $response = $this->postJson($url, $data);

        $token = $response->json('plain_text_token');

        $dbToken = PersonalAccessToken::findToken($token);

        // Se verifica si el token tiene la habilidad
        $this->assertTrue($dbToken->can($permission1->name));
        $this->assertTrue($dbToken->can($permission2->name));
        $this->assertFalse($dbToken->can($permission3->name));
    }

    /** @test */
    public function password_must_be_valid(): void
    {
        $user = User::factory()->create();

        $url = route('api.v1.login');

        $data = $this->validCredentials([
            'email' => $user->email,
            'password' => 'incorrect'
        ]);

        $response = $this->postJson($url, $data);

        $response->assertJsonValidationErrorFor('email');
    }

    /** @test */
    public function user_must_be_registered(): void
    {
        $url = route('api.v1.login');

        $data = $this->validCredentials();

        $response = $this->postJson($url, $data);

        $response->assertJsonValidationErrorFor('email');
    }

    /** @test */
    public function email_is_required(): void
    {
        $url = route('api.v1.login');

        $data = $this->validCredentials([
            'email' => null
        ]);

        $response = $this->postJson($url, $data);

        $response->assertJsonValidationErrors(['email' => 'required']);
    }

    /** @test */
    public function email_must_be_valid(): void
    {
        $url = route('api.v1.login');

        $data = $this->validCredentials([
            'email' => 'invalid-email'
        ]);

        $response = $this->postJson($url, $data);

        $response->assertJsonValidationErrors(['email' => 'email']);
    }

    /** @test */
    public function password_is_required(): void
    {
        $url = route('api.v1.login');

        $data = $this->validCredentials([
            'password' => null
        ]);

        $response = $this->postJson($url, $data);

        $response->assertJsonValidationErrors(['password' => 'required']);
    }

    /** @test */
    public function device_name_is_required(): void
    {
        $url = route('api.v1.login');

        $data = $this->validCredentials([
            'device_name' => null
        ]);

        $response = $this->postJson($url, $data);

        $response->assertJsonValidationErrors(['device_name' => 'required']);
    }

    protected function validCredentials(mixed $overrides = []): array
    {
        return array_merge([
            'email' => 'default_email@gmail.com',
            'password' => 'password', //Se usa 'password' porque en el factory se usa esta contreña por defecto.
            'device_name' => 'My Device'
        ], $overrides);
    }
}
