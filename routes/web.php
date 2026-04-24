<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/**
 * Rota inicial / dashboard
 */
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

/**
 * Rota Home / Cidadão
 */
Route::get('/home', function () {
    return view('citizen.home');
})->name('citizen.home')->middleware('auth');

/**
 * Rotas de autenticação
 */
Route::controller(AuthController::class)->group(function () {
    // Formulário de registro
    Route::get('/register', 'showRegisterForm')
        ->name('register')
        ->middleware('guest');

    // Processa registro
    Route::post('/register', 'register')
        ->name('register.store')
        ->middleware('guest');

    // Formulário de login
    Route::get('/login', 'showLoginForm')
        ->name('login')
        ->middleware('guest');

    // Processa login
    Route::post('/login', 'login')
        ->name('login.store')
        ->middleware('guest');

    // Logout
    Route::post('/logout', 'logout')
        ->name('logout')
        ->middleware('auth');
});

/**
 * Rotas do painel administrativo
 * Requer autenticação e acesso de Admin ou Secretário
 */
Route::prefix('admin')->name('admin.')->controller(AdminController::class)->group(function () {
    // Dashboard - resumo das denúncias
    Route::get('/dashboard', 'dashboard')
        ->name('dashboard')
        ->middleware(['auth', 'admin.only']);

    // Lista de denúncias
    Route::get('/reports', 'listReports')
        ->name('reports')
        ->middleware(['auth', 'admin.only']);

    // Detalhes da denúncia
    Route::get('/reports/{id}', 'showReport')
        ->name('report.show')
        ->middleware(['auth', 'admin.only']);

    // Atualizar status da denúncia
    Route::put('/reports/{id}/status', 'updateReportStatus')
        ->name('report.status')
        ->middleware(['auth', 'admin.only']);
});