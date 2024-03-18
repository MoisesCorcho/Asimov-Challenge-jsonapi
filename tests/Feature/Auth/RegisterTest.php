<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

class RegisterTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutJsonApiHeaders();
    }

    /** @test */
    public function can_register(): void
    {
        $url = route('api.v1.register');

        $data = $this->validCredentials();

        $response = $this->postJson($url, $data);

        $token = $response->json('plain_text_token');

        // Se verifica que si se cree un token al registrarse
        $this->assertNotNull(
            PersonalAccessToken::findToken($token),
            'The plain token is invalid.'
        );

        $this->assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    /** @test */
    public function authenticated_users_cannot_register_again(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.register'))->assertNoContent();
    }

    /** @test */
    public function name_is_required(): void
    {
        $this->postJson(route('api.v1.register'), $this->validCredentials([
            'name' => ''
        ]))->assertJsonValidationErrorFor('name');
    }

    /** @test */
    public function email_is_required(): void
    {
        $this->postJson(route('api.v1.register'), $this->validCredentials([
            'email' => ''
        ]))->assertJsonValidationErrorFor('email');
    }

    /** @test */
    public function email_must_be_valid(): void
    {
        $this->postJson(route('api.v1.register'), $this->validCredentials([
            'email' => 'invalid-email'
        ]))->assertJsonValidationErrorFor('email');
    }

    /** @test */
    public function email_must_be_unique(): void
    {
        $user = User::factory()->create();

        $this->postJson(route('api.v1.register'), $this->validCredentials([
            'email' => $user->email
        ]))->assertJsonValidationErrorFor('email');
    }

    /** @test */
    public function password_is_required(): void
    {
        $this->postJson(route('api.v1.register'), $this->validCredentials([
            'password' => ''
        ]))->assertJsonValidationErrorFor('password');
    }

    /** @test */
    public function password_must_be_confirmed(): void
    {
        $this->postJson(route('api.v1.register'), $this->validCredentials([
            'password' => 'password',
            'password_confirmation' => 'not-confirmed'
        ]))->assertJsonValidationErrorFor('password');
    }

    /** @test */
    public function device_name_is_required(): void
    {
        $this->postJson(route('api.v1.register'), $this->validCredentials([
            'device_name' => ''
        ]))->assertJsonValidationErrorFor('device_name');
    }

    protected function validCredentials(mixed $overrides = []): array
    {
        return array_merge([
            'name' => 'Chichi Peralta',
            'email' => 'default_email@gmail.com',
            'password' => 'password', //Se usa 'password' porque en el factory se usa esta contreña por defecto.
            'password_confirmation' => 'password',
            'device_name' => 'My Device'
        ], $overrides);
    }
}
