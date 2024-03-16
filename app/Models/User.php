<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Traits\HasUuid;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public $resourceType = 'authors';

    public function permissions()
    {
        return $this->BelongsToMany(Permission::class);
    }

    /**
     * Añadir un permiso al modelo.
     *
     * A traves del usuario se tiene el metodo permissions() para acceder a la relacion
     * y con el metodo 'syncWithoutDetaching' se añaden un nuevo registro a la tabla
     * SIEMPRE Y CUANDO dicho registro no exista con anterioridad, en tal caso, simple-
     * mente lo ignorará y no realizará ningun proceso de inserción.
     *
     * @param Permission $permission
     * @return void
     */
    public function givePermissionTo(Permission $permission)
    {
        $this->permissions()->syncWithoutDetaching($permission);
    }

}
