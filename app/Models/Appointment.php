<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected $guarded = [];

    function areThereCrossHours(string $time)
    {
        $date = request('date');

        $apmt = DB::table('appointments')
            ->selectRaw('TIMEDIFF(?, start_time) AS diferencia', [$time])
            ->where('date', $date)
            ->havingRaw("diferencia < '01:00:00' AND diferencia > '-01:00:00'")
            ->count();

        return $apmt;
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
        if (request('date') == now()->format('Y-m-d')) {
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
}
