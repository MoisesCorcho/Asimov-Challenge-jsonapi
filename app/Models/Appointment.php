<?php

namespace App\Models;

use DateTime;
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

    function areThereCrossHoursPHP(string $time)
    {
        $date = request()->input('data.attributes.date');

        $apmt = DB::table('appointments')
            ->select('start_time')
            ->where('date', $date)
            ->get();

        $fecha_y_hora_1 = "{$date} {$time}";
        $x2 = new DateTime($fecha_y_hora_1);

        $response = $apmt->map( function($messages, $field) use ($time, $date, $x2) {

            $fecha_y_hora_2 = "{$date} {$messages->start_time}";

            $x1 = new DateTime($fecha_y_hora_2);

            if (
                ($x1->diff($x2)->i > 0 && $x1->diff($x2)->h == 0) ||
                ($x1->diff($x2)->i == 0 && $x1->diff($x2)->h == 0)
            ){
                return true;
            }

        })->filter();

        $fecha_y_hora_3 = "{$date} ".env('END_TIME');
        $x3 = new DateTime($fecha_y_hora_3);

        if (
            ($x2->diff($x3)->h == 0 && $x2->diff($x3)->i > 0) ||
            ($x2->diff($x3)->h == 0 && $x2->diff($x3)->i == 0)
        ){
            return true;
        }

        if ($response->isEmpty()) {
            return false;
        }

        return true;
    }

    function isWeekend(string $date)
    {
        $dayName = Carbon::create($date)->format('l');

        if ($dayName === 'Sunday' || $dayName == 'Saturday') {
            return true;
        }

        return false;
    }

    function timeIsInThePast(string $time)
    {

        if (request()->input('data.attributes.date') == now()->format('Y-m-d')) {
            if ($time < now()->format('H:m')) {
                return true;
            }
        }

        return false;
    }

    function isOfficeTime(string $time)
    {

        if ($time < env('START_TIME') || $time > env('END_TIME')) {
            return false;
        }

        return true;
    }

    public function scopeYear(Builder $query, $year)
    {
        $query->whereYear('date', $year);
    }

    public function scopeMonth(Builder $query, $month)
    {
        $query->whereMonth('date', $month);
    }

}
