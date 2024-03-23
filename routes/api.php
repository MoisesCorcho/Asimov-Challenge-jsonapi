<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Middleware\ValidateJsonApiHeaders;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Middleware\ValidateJsonApiDocument;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AppointmentAuthorController;
use App\Http\Controllers\Api\CommentAppointmentController;
use App\Http\Controllers\Api\AppointmentCategoryController;

Route::apiResource('appointments', AppointmentController::class);

Route::apiResource('categories', CategoryController::class)
    ->only('index', 'show');

Route::apiResource('authors', AuthorController::class)
    ->only('index', 'show');

Route::apiResource('comments', CommentController::class);

// Son rutas necesarias para generar los links de las relaciones (self y related) de Category
// (Las categorias de los Appointments)
Route::get('appointments/{appointment}/relationships/category', [AppointmentCategoryController::class, 'index'])
    ->name('appointments.relationships.category');

Route::patch('appointments/{appointment}/relationships/category', [AppointmentCategoryController::class, 'update'])
    ->name('appointments.relationships.category');

Route::get('appointments/{appointment}/category', [AppointmentCategoryController::class, 'show'])
    ->name('appointments.category');

// Son rutas necesarias para generar los links de las relaciones (self y related) de Author
// (Los autores de los Appointemnts)
Route::get('appointments/{appointment}/relationships/author', [AppointmentAuthorController::class, 'index'])
    ->name('appointments.relationships.author');

Route::patch('appointments/{appointment}/relationships/author', [AppointmentAuthorController::class, 'update'])
    ->name('appointments.relationships.author');

Route::get('appointments/{appointment}/author', [AppointmentAuthorController::class, 'show'])
    ->name('appointments.author');

// Son rutas necesarias para generar los links de las relaciones (self y related) de Appointment
// (El Appointment relacion a los Comentarios)
Route::get('comments/{comment}/relationships/appointment', [CommentAppointmentController::class, 'index'])
    ->name('comments.relationships.appointment');

Route::patch('comments/{comment}/relationships/appointment', [CommentAppointmentController::class, 'update'])
    ->name('comments.relationships.appointment');

Route::get('comments/{comment}/appointment', [CommentAppointmentController::class, 'show'])
    ->name('comments.appointment');

// Authentication
Route::withoutMiddleware([
    ValidateJsonApiDocument::class,
    ValidateJsonApiHeaders::class
])->group(function() {
    Route::post('login', LoginController::class)->name('login');
    Route::post('logout', LogoutController::class)->name('logout');
    Route::post('register', RegisterController::class)->name('register');
});


