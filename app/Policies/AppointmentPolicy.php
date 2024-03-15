<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AppointmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        /** Se verifica que el token del modelo tenga los permisos necesarios
         * para crear.*/
        return $user->tokenCan('appointment:create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Appointment $appointment): bool
    {
        /** Se verifica si el modelo '$user' es igual al modelo $appointment->author
         * y que el token del modelo tenga los permisos necesarios para actualizar.
        */
        return $user->is($appointment->author) && $user->tokenCan('appointment:update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Appointment $appointment): bool
    {
        /** Se verifica si el modelo '$user' es igual al modelo $appointment->author
         * y que el token del modelo tenga los permisos necesarios para eliminar.
        */
        return $user->is($appointment->author) && $user->tokenCan('appointment:delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Appointment $appointment): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Appointment $appointment): bool
    {
        //
    }
}
