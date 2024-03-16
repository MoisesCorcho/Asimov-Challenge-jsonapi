<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;



class UserPermissionTest extends TestCase
{
    use LazilyRefreshDatabase;

    /** @test */
    public function can_assign_permissions_to_a_user(): void
    {
        $user = User::factory()->create();
        $permission = Permission::factory()->create();

        /** Este es un metodo creado manualmente en el modelo */
        $user->givePermissionTo($permission);

        /** Se verifica que se recibe 1, cuando se llama al usuario y permissions
         * como propiedad.
         *
         * Con el metodo 'fresh()' se hace que se vuelva a obtener el usuario
         * de la base de datos.
        */
        $this->assertCount(1, $user->fresh()->permissions);
    }

    /** @test */
    public function cannot_assign_the_same_permissions_twice(): void
    {
        $user = User::factory()->create();
        $permission = Permission::factory()->create();

        /** Este es un metodo creado manualmente en el modelo */
        $user->givePermissionTo($permission);
        $user->givePermissionTo($permission);

        /** Se verifica que se recibe 1, cuando se llama al usuario y permissions
         * como propiedad.
         *
         * Con el metodo 'fresh()' se hace que se vuelva a obtener el usuario
         * de la base de datos.
        */
        $this->assertCount(1, $user->fresh()->permissions);
    }
}
