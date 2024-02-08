<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;

Route::get('appointments',                 [AppointmentController::class, 'index'])->name('api.v1.appoinments.index');
Route::get('appointments/{appointment}',   [AppointmentController::class, 'show'])->name('api.v1.appointments.show');
Route::post('appointments',                [AppointmentController::class, 'store'])->name('api.v1.appointments.store');
Route::patch('appointments/{appointment}', [AppointmentController::class, 'update'])->name('api.v1.appointments.update');
