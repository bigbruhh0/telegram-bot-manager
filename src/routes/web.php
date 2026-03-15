<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\BroadcastController;

// Публичная главная страница
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Управление ботами
    Route::post('/bots', [BotController::class, 'store'])->name('bots.store');
    Route::get('/bots/{bot}', [BotController::class, 'show'])->name('bots.show');
    Route::delete('/bots/{bot}', [BotController::class, 'destroy'])->name('bots.destroy');
    
    // Управление подписчиками
    Route::delete('/subscribers/{subscriber}', [SubscriberController::class, 'destroy'])
        ->name('subscribers.destroy');
    
    // Рассылки
    Route::post('/bots/{bot}/broadcast', [BroadcastController::class, 'send'])
        ->name('bots.broadcast');
    
    // Профиль (маршруты уже должны быть в auth.php, но на всякий случай добавим)
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Маршруты аутентификации Breeze
require __DIR__.'/auth.php';
