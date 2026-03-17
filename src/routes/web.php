<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\BroadcastController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/bots', [BotController::class, 'store'])->name('bots.store');

    Route::get('/bots/{id}', [BotController::class, 'show'])
        ->name('bots.show')
        ->where('id', '[0-9]+');

    Route::delete('/bots/{id}', [BotController::class, 'destroy'])
        ->name('bots.destroy')
        ->where('id', '[0-9]+');

    Route::delete('/subscribers/{subscriber}', [SubscriberController::class, 'destroy'])
        ->name('subscribers.destroy');

    Route::post('/bots/{id}/broadcast', [BroadcastController::class, 'send'])
        ->name('bots.broadcast')
        ->where('id', '[0-9]+');

    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Маршруты аутентификации Breeze
require __DIR__ . '/auth.php';
