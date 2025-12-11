<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('terms-of-service', function () {
    return Inertia::render('auth/TermsOfService');
})->name('terms');

Route::get('privacy-policy', function () {
    return Inertia::render('auth/PrivacyPolicy');
})->name('privacy');

Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('profile', [\App\Http\Controllers\UserController::class, 'profile'])
    ->middleware(['auth', 'verified'])
    ->name('profile');

Route::get('users/{user}', [\App\Http\Controllers\UserController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('users.show');

Route::get('courses', [\App\Http\Controllers\CoursesController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('courses');

Route::post('courses/{course}/enroll', [\App\Http\Controllers\CoursesController::class, 'enroll'])
    ->middleware(['auth', 'verified'])
    ->name('courses.enroll');

Route::get('quests', [\App\Http\Controllers\QuestsController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('quests');

Route::get('leaderboard', [\App\Http\Controllers\LeaderboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('leaderboard');

Route::get('achievements', [\App\Http\Controllers\AchievementsController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('achievements');

Route::get('progress', [\App\Http\Controllers\ProgressController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('progress');

Route::get('rewards', [\App\Http\Controllers\RewardsController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('rewards');

Route::get('community', [\App\Http\Controllers\CommunityController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('community');

Route::get('community/{post}', [\App\Http\Controllers\CommunityController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('community.show');

Route::post('community', [\App\Http\Controllers\CommunityController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('community.store');

Route::put('community/{post}', [\App\Http\Controllers\CommunityController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('community.update');

Route::delete('community/{post}', [\App\Http\Controllers\CommunityController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('community.destroy');

Route::post('community/{post}/react', [\App\Http\Controllers\CommunityController::class, 'react'])
    ->middleware(['auth', 'verified'])
    ->name('community.react');

Route::get('assignments', [\App\Http\Controllers\AssignmentController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('assignments');

Route::get('assignments/{assignment}', [\App\Http\Controllers\AssignmentController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('assignment.show');

Route::post('assignments/{assignment}/upload', [\App\Http\Controllers\AssignmentController::class, 'upload'])
    ->middleware(['auth', 'verified'])
    ->name('assignment.upload');

// API Routes
Route::prefix('api')->group(function () {
    Route::get('logo', [\App\Http\Controllers\LogoController::class, 'getActive']);
    
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('users/search', [\App\Http\Controllers\Api\UserSearchController::class, 'search']);
        Route::get('announcements/latest', [\App\Http\Controllers\AnnouncementController::class, 'getLatest']);
    });
});

require __DIR__.'/settings.php';
