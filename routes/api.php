<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AppointmentAuthorController;
use App\Http\Controllers\Api\AppointmentCategoryController;

// Route::get('appointments',                  [AppointmentController::class, 'index'])->name('api.v1.appointments.index');
// Route::get('appointments/{appointment}',    [AppointmentController::class, 'show'])->name('api.v1.appointments.show');
// Route::post('appointments',                 [AppointmentController::class, 'store'])->name('api.v1.appointments.store');
// Route::patch('appointments/{appointment}',  [AppointmentController::class, 'update'])->name('api.v1.appointments.update');
// Route::delete('appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('api.v1.appointments.destroy');


Route::apiResource('appointments', AppointmentController::class);

Route::apiResource('categories', CategoryController::class)
    ->only('index', 'show');

Route::apiResource('authors', AuthorController::class)
    ->only('index', 'show');

// Son rutas necesarias para generar los links de las relaciones (self y related)
Route::get('appointments/{appointment}/relationships/category', [AppointmentCategoryController::class, 'index'])
    ->name('appointments.relationships.category');
Route::patch('appointments/{appointment}/relationships/category', [AppointmentCategoryController::class, 'update'])
    ->name('appointments.relationships.category');

Route::get('appointments/{appointment}/category', [AppointmentCategoryController::class, 'show'])
    ->name('appointments.category');

Route::get('appointments/{appointment}/relationships/author', [AppointmentAuthorController::class, 'index'])
    ->name('appointments.relationships.author');
Route::patch('appointments/{appointment}/relationships/author', [AppointmentAuthorController::class, 'update'])
    ->name('appointments.relationships.author');

Route::get('appointments/{appointment}/author', [AppointmentAuthorController::class, 'show'])
    ->name('appointments.author');
