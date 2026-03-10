<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OAuthController;
use Livewire\Livewire;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/sangkara');
    }
    return redirect('/sangkara/login');
});

Route::get('/oauth/{provider}/redirect', [OAuthController::class, 'redirect'])->name('oauth.redirect');
Route::get('/oauth/{provider}/callback', [OAuthController::class, 'callback'])->name('oauth.callback');

// 2FA Challenge Route
Route::get('/sangkara/two-factor-challenge', \App\Filament\Pages\Auth\TwoFactorChallenge::class)
    ->middleware(['web'])
    ->name('filament.sangkara.auth.two-factor-challenge');