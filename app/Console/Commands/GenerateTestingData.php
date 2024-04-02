<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Appointment;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\Schema;

class GenerateTestingData extends Command
{
    use ConfirmableTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:test-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Test Data for the API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Si el usuario no confirma la ejecución del comando, se detiene
        if (! $this->confirmToProceed()) {
            return 1;
        }

        // Deshabilita las restricciones de clave externa en la base de datos
        Schema::disableForeignKeyConstraints();

        // Elimina todos los registros de las tablas relacionadas
        User::query()->delete();
        Appointment::query()->delete();
        Category::query()->delete();
        Comment::query()->delete();

        // Trunca (borra todos los registros pero no reinicia la auto-incrementación) las tablas relacionadas
        Comment::truncate();
        Category::truncate();
        Appointment::truncate();
        User::truncate();

        // Habilita nuevamente las restricciones de clave externa en la base de datos
        Schema::enableForeignKeyConstraints();

        // Crea un usuario con un nombre y correo específicos y relaciona una cita con este usuario
        $user = User::factory()->hasAppointments(1)->create([
            'name' => 'Moises',
            'email' => 'moises@gmail.com'
        ]);

        // Crea múltiples citas con comentarios relacionados y establece la misma hora de inicio para todas las citas
        $appointments = Appointment::factory(14)->hasComments(5)->create([
            'start_time' => '10:00'
        ]);

        // Muestra por consola la UUID del usuario creado
        $this->info('User UUID:');
        $this->line($user->id);

        // Muestra por consola el token de autenticación del usuario creado
        $this->info('Token:');
        $this->line($user->createToken('Moises')->plainTextToken);

        // Muestra por consola el ID de la primera cita del usuario
        $this->info('Appointment ID:');
        $this->line($user->appointments->first()->id);

        // Muestra por consola el ID de la categoría de la primera cita creada
        $this->info('Category ID:');
        $this->line($appointments->first()->category->id);

        // Muestra por consola el ID de un comentario aleatorio relacionado con la primera cita creada
        $this->info('Comment ID:');
        $this->line($appointments->first()->comments->random()->id);
    }
}
