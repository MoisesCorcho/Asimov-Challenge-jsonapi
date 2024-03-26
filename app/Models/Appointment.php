<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    public function author()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\Comment::class);
    }

    /**
     * Propiedad creada para la funcion sparseFielset dentro de la clase JsonApiQueryBuilder, la cual es usada
     * para un mixin, para añadir funcionalidad al modelo.
     */
    // public $resourceType = 'appointments';

    function areThereCrossHours(string $time)
    {
        $date = request()->input('data.attributes.date');

        $apmt = DB::table('appointments')
            ->selectRaw('TIMEDIFF(?, start_time) AS diferencia', [$time])
            ->where('date', $date)
            ->havingRaw("diferencia < '01:00:00' AND diferencia > '-01:00:00'")
            ->count();

        return $apmt;
    }

    // function areThereCrossHoursPHP(string $time)
    // {
    //     $date = request()->input('data.attributes.date');

    //     $apmt = DB::table('appointments')
    //         ->select('start_time')
    //         ->where('date', $date)
    //         ->get();

    //     $fecha_y_hora_1 = "{$date} {$time}";
    //     $x2 = new DateTime($fecha_y_hora_1);

    //     $response = $apmt->map( function($messages, $field) use ($time, $date, $x2) {

    //         $fecha_y_hora_2 = "{$date} {$messages->start_time}";

    //         $x1 = new DateTime($fecha_y_hora_2);

    //         if (
    //             ($x1->diff($x2)->i > 0 && $x1->diff($x2)->h == 0) ||
    //             ($x1->diff($x2)->i == 0 && $x1->diff($x2)->h == 0)
    //         ){
    //             return true;
    //         }

    //     })->filter();

    //     $fecha_y_hora_3 = "{$date} ".env('END_TIME');
    //     $x3 = new DateTime($fecha_y_hora_3);

    //     if (
    //         ($x2->diff($x3)->h == 0 && $x2->diff($x3)->i > 0) ||
    //         ($x2->diff($x3)->h == 0 && $x2->diff($x3)->i == 0)
    //     ){
    //         return true;
    //     }

    //     if ($response->isEmpty()) {
    //         return false;
    //     }

    //     return true;
    // }

    /**
     * Verifica si hay citas cruzadas para la hora proporcionada en la fecha recibida.
     *
     * @param string $time La hora para la cual se verifica la disponibilidad.
     * @return bool Devuelve true si hay citas cruzadas, de lo contrario, devuelve false.
     */
    function areThereCrossHoursPHP(string $time)
    {
        // Se toma la fecha que se recibe en la peticion.
        $date = request()->input('data.attributes.date');

        // Se crea una una instancia de Carbon con la fecha recibida.
        $scheduledFor = Carbon::parse("$date $time");

        // Fecha recibida añadiendole una hora.
        $endOfAppointment = $scheduledFor->copy()->addHour();

        // Fecha recibida restandole una hora.
        $appointmentOneHourBefore = $scheduledFor->copy()->subHour();

        // Una hora antes de la hora final.
        $oneHourBeforeEndTime = Carbon::parse("$date ".env('END_TIME'))->subHour()->format("H:i");

        // Si la hora es igual o mayor a la hora final del horario laboral salta error.
        if ( $time == env('END_TIME') || $time > $oneHourBeforeEndTime ) {
            return true;
        }

        $existingAppointments = Appointment::where(function ($query) use ($scheduledFor, $endOfAppointment) {
            $query->where('start_time', '<', $endOfAppointment->format('H:i'))
                ->where('start_time', '>=', $scheduledFor->format('H:i'));
        })->orWhere(function ($query) use ($scheduledFor, $appointmentOneHourBefore) {
            $query->where('start_time', '<=', $scheduledFor->format('H:i'))
                ->where('start_time', '>', $appointmentOneHourBefore->format('H:i'));
        })->exists();

        return $existingAppointments;
    }

    /**
     * Verifica si la fecha dada es un fin de semana.
     *
     * @param string $date La fecha para la cual se verifica si es un fin de semana.
     * @return bool Devuelve true si la fecha es un fin de semana, de lo contrario, devuelve false.
     */
    function isWeekend(string $date)
    {
        $dayName = Carbon::create($date)->format('l');

        if ($dayName === 'Sunday' || $dayName == 'Saturday') {
            return true;
        }

        return false;
    }

    /**
     * Verifica si la hora dada está en el pasado en comparación con la hora actual.
     *
     * @param string $time La hora para la cual se realiza la verificación.
     * @return bool Devuelve true si la hora está en el pasado, de lo contrario, devuelve false.
     */
    function timeIsInThePast(string $time)
    {

        if (request()->input('data.attributes.date') == now()->format('Y-m-d')) {
            if ($time < now()->format('H:m')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica si la hora dada está dentro del horario laboral establecido.
     *
     * @param string $time La hora para la cual se realiza la verificación.
     * @return bool Devuelve true si la hora está dentro del horario laboral, de lo contrario, devuelve false.
     */
    function isOfficeTime(string $time)
    {

        if ($time < env('START_TIME') || $time > env('END_TIME')) {
            return false;
        }

        return true;
    }

    /**
     * Query Scope para filtrar por años.
     *
     * @param Builder $query
     * @param string $year
     * @return void
     */
    public function scopeYear(Builder $query, $year)
    {
        $query->whereYear('date', $year);
    }

    /**
     * Query Scope para filtrar por meses.
     *
     * @param Builder $query
     * @param string $month
     * @return void
     */
    public function scopeMonth(Builder $query, $month)
    {
        $query->whereMonth('date', $month);
    }

    public function scopeCategories(Builder $query, $categories)
    {
        $categoryIds = explode(',', $categories);

        $query->whereIn('category_id', $categoryIds);
    }

}
