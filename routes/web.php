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

    // Logout - Funciona para cualquier tipo de usuário (admin, secretary, citizen)
    Route::post('/logout', 'logout')
        ->name('logout');
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

    // // Detalhes da denúncia
    // Route::get('/reports/{report}', 'showReport')
    //     ->name('report.show')
    //     ->middleware(['admin.auth', 'admin.only']);

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
 * Rotas para atualizar denúncias pelo Secretário
 * Requer autenticação apenas de Secretário
 */
Route::middleware('auth:secretary')->controller(\App\Http\Controllers\AdminController::class)->group(function () {
    // Atualizar status da denúncia (SECRETÁRIO)
    Route::put('/secretary/reports/{report}/status', 'updateReportStatus')
        ->name('secretary.report.status');
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
 * Requer autenticação apenas de Secretário
 */
Route::prefix('priority')->name('priority.')->controller(PriorityController::class)->group(function () {
    // Formulário para classificar denúncia por prioridade
    Route::get('/reports/{report}/edit', 'edit')
        ->name('edit')
        ->middleware('auth:secretary');

    // Atualizar prioridade da denúncia (APENAS SECRETÁRIO)
    Route::put('/reports/{report}', 'update')
        ->name('update')
        ->middleware('auth:secretary');
});

/**
 * Rotas API para carregamento dinâmico
 * Endpoints para requisições AJAX do frontend
 */
Route::prefix('api')->group(function () {
    // Retorna todas as secretárias responsáveis por uma categoria
    Route::get('/categories/{categoryName}/secretaries', function ($categoryName) {
        try {
            \Illuminate\Support\Facades\Log::info('API chamada', [
                'endpoint' => 'categories.secretaries',
                'category' => $categoryName,
            ]);

            // Busca a categoria pelo nome
            $category = \App\Models\Category::where('name', $categoryName)
                ->first();

            \Illuminate\Support\Facades\Log::info('Categoria buscada', [
                'category_name' => $categoryName,
                'found' => $category ? true : false,
            ]);

            if (!$category) {
                return response()->json([], 404);
            }

            // Retorna as secretárias responsáveis por essa categoria (ativas)
            $secretaries = $category->secretaries()
                ->where('is_active', true)
                ->select('id', 'name')
                ->get();

            \Illuminate\Support\Facades\Log::info('Secretárias encontradas', [
                'category' => $categoryName,
                'count' => $secretaries->count(),
            ]);

            return response()->json($secretaries);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erro ao buscar secretárias', [
                'category' => $categoryName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Erro ao carregar setores', 'message' => $e->getMessage()], 500);
        }
    });
});   