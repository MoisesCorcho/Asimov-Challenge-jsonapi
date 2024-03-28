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
use App\Http\Controllers\Api\CommentAuthorController;
use App\Http\Controllers\Api\AppointmentAuthorController;
use App\Http\Controllers\Api\AppointmentCommentController;
use App\Http\Controllers\Api\CommentAppointmentController;
use App\Http\Controllers\Api\AppointmentCategoryController;

Route::apiResource('appointments', AppointmentController::class);

Route::apiResource('categories', CategoryController::class)
    ->only('index', 'show');

Route::apiResource('authors', AuthorController::class)
    ->only('index', 'show');

Route::apiResource('comments', CommentController::class);


// Todas estas rutas comparten el mismo inicio de URL asi que se agrupan para colocarle un prefijo
// y que de esta manera la ruta sea mas corta.
Route::prefix('appointments/{appointment}')->group(function () {

    // Son rutas necesarias para generar los links de las relaciones (self y related) de Category
    // (Las categorias de los Appointments)
    Route::controller(AppointmentCategoryController::class)->group(function () {

        // Obtener el identificador de la Categoria asociada al Appointment.
        Route::get('relationships/category', 'index')
            ->name('appointments.relationships.category');

        // Actualizar Categoria relacionada al Appointment.
        Route::patch('relationships/category', 'update')
            ->name('appointments.relationships.category');

        // Obtener la Categoria asociada al Appointment.
        Route::get('category', 'show')
            ->name('appointments.category');
    });

    // Son rutas necesarias para generar los links de las relaciones (self y related) de Author
    // (Los autores de los Appointemnts)
    Route::controller(AppointmentAuthorController::class)->group(function () {

        // Obtener el identificador del Autor relacionado al Appointment.
        Route::get('relationships/author', 'index')
            ->name('appointments.relationships.author');

        // Actualizar el Autor relacionado al Appointment.
        Route::patch('relationships/author', 'update')
            ->name('appointments.relationships.author');

        // Obtener el Autor relacionado al Appointment.
        Route::get('author', 'show')
            ->name('appointments.author');
    });

    // Son rutas necesarias para generar los links de las relaciones (self y related) de Comment
    // (Los comentarios de los Appointemnts)
    Route::controller(AppointmentCommentController::class)->group(function () {

        // Obtener los identificadores de los Comentarios relacionados al Appointment.
        Route::get('relationships/comments', 'index')
            ->name('appointments.relationships.comments');

        // Actualizar los Comentarios relacionados al Appointment.
        Route::patch('relationships/comments', 'update')
        ->name('appointments.relationships.comments');

        // Obtener los Comentarios relacionados al Appointment.
        Route::get('relationships', 'show')
            ->name('appointments.comments');

    });

});

// Todas estas rutas comparten el mismo inicio de URL asi que se agrupan para colocarle un prefijo
// y que de esta manera la ruta sea mas corta.
Route::prefix('comments/{comment}')->group(function () {

    // Son rutas necesarias para generar los links de las relaciones (self y related) de Appointment
    // (El Appointment relacionado a los Comentarios)
    Route::controller(CommentAppointmentController::class)->group(function () {

        // Obtener el identificador del Appointment asociado al Comentario.
        Route::get('relationships/appointment', 'index')
            ->name('comments.relationships.appointment');

        // Actualizar el Appointment relacionado al Comentario.
        Route::patch('relationships/appointment', 'update')
            ->name('comments.relationships.appointment');

        // Obtener el Appointment asociado al Comentario.
        Route::get('appointment', 'show')
            ->name('comments.appointment');
    });

    Route::controller(CommentAuthorController::class)->group(function () {

        // Obtener el identificador del Autor asociado al Comentario.
        Route::get('relationships/author', 'index')
            ->name('comments.relationships.author');

        // Actualizar el Autor asociado al Comentario.
        Route::patch('relationships/author', 'update')
            ->name('comments.relationships.author');

        // Obtener el Autor asociado al Comentario.
        Route::get('relationships', 'show')
            ->name('comments.author');

    });

});


// Authentication
Route::withoutMiddleware([
    ValidateJsonApiDocument::class,
    ValidateJsonApiHeaders::class
])->group(function() {
    Route::post('login', LoginController::class)->name('login');
    Route::post('logout', LogoutController::class)->name('logout');
    Route::post('register', RegisterController::class)->name('register');
});


