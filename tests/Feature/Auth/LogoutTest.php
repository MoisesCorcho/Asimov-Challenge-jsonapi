<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;

class LogoutTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_logout(): void
    {
        $user = User::factory()->create();

        $accessToken = $user->createToken($user->name)->plainTextToken;

        // Se manda el header en la peticion (request).
        $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->postJson(route('api.v1.logout'))
            ->assertNoContent();

        $this->assertNull(PersonalAccessToken::findToken($accessToken));
    }
}
