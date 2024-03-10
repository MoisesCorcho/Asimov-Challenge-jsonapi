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

        $response = $this->postJson($url, [
            'email' => $user->email,
            'password' => 'password', //Se usa 'password' porque en el factory se usa esta contreña por defecto.
            'device_name' => 'My Device'
        ]);

        $token = $response->json('plain_text_token');

        $dbToken = PersonalAccessToken::findToken($token);

        $this->assertTrue($dbToken->tokenable->is($user));
    }
}
