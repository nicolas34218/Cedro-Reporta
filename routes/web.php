<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CitizenController;
use App\Http\Controllers\SecretaryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PriorityController;
use Illuminate\Support\Facades\Route;

/**
 * Rota inicial / dashboard
 */
Route::get('/', function () {
    // Se está autenticado como admin
    if (auth('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }
    
    // Se está autenticado como secretary
    if (auth('secretary')->check()) {
        return redirect()->route('secretary.dashboard');
    }
    
    // Se está autenticado como citizen
    if (auth('citizen')->check()) {
        return redirect()->route('citizen.home');
    }
    
    // Se não está autenticado, vai para login
    return redirect()->route('login');
})->name('welcome');

/**
 * Rota Home / Cidadão
 */
Route::get('/home', [CitizenController::class, 'home'])
    ->name('citizen.home')
    ->middleware(['auth:citizen', 'citizen.only']);

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
 * Rotas de denúncias do cidadão
 */
Route::middleware(['auth:citizen', 'citizen.only'])->prefix('cidadao')->name('citizen.')->controller(ReportController::class)->group(function () {
    // Formulário de criação de denúncia
    Route::get('/denuncias/nova', 'create')
        ->name('reports.create');

    // Página de busca e filtro de denúncias
    Route::get('/denuncias/buscar', 'search')
        ->name('reports.search');

    // Lista de denúncias do usuário
    Route::get('/denuncias', 'index')
        ->name('reports.index');

    // Registra uma nova denúncia
    Route::post('/denuncias', 'store')
        ->name('reports.store');

    // Detalhes de uma denúncia específica
    Route::get('/denuncias/{report}', 'show')
        ->name('reports.show');

    // Imagem da denúncia
    Route::get('/denuncias/{report}/imagem', 'getImage')
        ->name('reports.image');

    // Acompanhamento de status da denúncia
    Route::get('/denuncias/{report}/status', 'trackStatus')
        ->name('reports.track-status');
});

/**
 * Rotas do painel administrativo
 * Requer autenticação e acesso de Admin ou Secretário
 */
Route::prefix('admin')->name('admin.')->controller(AdminController::class)->group(function () {
    // Dashboard - resumo das denúncias
    Route::get('/dashboard', 'dashboard')
        ->name('dashboard')
        ->middleware(['admin.auth', 'admin.only']);

    // Lista de denúncias
    Route::get('/reports', 'listReports')
        ->name('reports')
        ->middleware(['admin.auth', 'admin.only']);

    // Detalhes da denúncia
    Route::get('/reports/{report}', 'showReport')
        ->name('report.show')
        ->middleware(['admin.auth', 'admin.only']);

    // Atualizar status da denúncia
    Route::put('/reports/{report}/status', 'updateReportStatus')
        ->name('report.status')
        ->middleware(['admin.auth', 'admin.only']);
});

/**
 * Rotas de secretária
 * Requer autenticação e acesso de Admin (para criar) ou Secretário (para acessar dashboard)
 */
Route::prefix('secretary')->name('secretary.')->controller(SecretaryController::class)->group(function () {
    // Formulário para criar nova secretária (apenas admin)
    Route::get('/create', 'create')
        ->name('create')
        ->middleware(['admin.auth', 'admin.only']);

    // Armazena nova secretária (apenas admin)
    Route::post('/store', 'store')
        ->name('store')
        ->middleware(['admin.auth', 'admin.only']);

    // Dashboard da secretária com denúncias da categoria
    Route::get('/dashboard', 'dashboard')
        ->name('dashboard')
        ->middleware('auth:secretary');

    // Tela de classificação de prioridades das denúncias
    Route::get('/classify-reports', 'classifyReports')
        ->name('classify-reports')
        ->middleware('auth:secretary');
});

/**
 * Rotas de categorias
 * Requer autenticação e acesso de Admin
 */
Route::prefix('category')->name('category.')->controller(CategoryController::class)->group(function () {
    // Formulário para criar nova categoria (apenas admin)
    Route::get('/create', 'create')
        ->name('create')
        ->middleware(['admin.auth', 'admin.only']);

    // Armazena nova categoria (apenas admin)
    Route::post('/store', 'store')
        ->name('store')
        ->middleware(['admin.auth', 'admin.only']);
});

/**
 * Rotas de prioridade de denúncias
 * Requer autenticação e acesso de Admin ou Secretário
 */
Route::prefix('priority')->name('priority.')->controller(PriorityController::class)->group(function () {
    // Formulário para classificar denúncia por prioridade
    Route::get('/reports/{report}/edit', 'edit')
        ->name('edit')
        ->middleware('admin.auth');

    // Atualizar prioridade da denúncia
    Route::put('/reports/{report}', 'update')
        ->name('update')
        ->middleware('admin.auth');
});