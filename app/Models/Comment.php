<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    /**
     * En Eloquent, cuando defines una relación de pertenencia (belongsTo),
     * por defecto, Laravel asume que la clave foránea en la tabla relacionada
     * es el nombre de la relación seguido de "_id". Por ejemplo, si tienes una
     * relación author, Laravel buscará una columna llamada author_id en la tabla
     * relacionada, pero como no se sigue esa convencion se debe especificar el
     * nombre de la llave foranea con la que se deberia trabajar.
     *
     * @return void
     */
    public function author(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'user_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->BelongsTo(Appointment::class);
    }

}
