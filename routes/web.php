<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('assignments', [\App\Http\Controllers\AssignmentController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('assignments');

Route::get('assignments/{assignment}', [\App\Http\Controllers\AssignmentController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('assignment.show');

Route::post('assignments/{assignment}/upload', [\App\Http\Controllers\AssignmentController::class, 'upload'])
    ->middleware(['auth', 'verified'])
    ->name('assignment.upload');

require __DIR__.'/settings.php';
