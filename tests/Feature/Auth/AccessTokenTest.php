<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccessTokenTest extends TestCase
{

    use RefreshDatabase;

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

        $this->assertTrue($dbToken->tokenable->is($user));
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
