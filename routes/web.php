<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CitizenController;
use App\Http\Controllers\SecretaryController;
use App\Http\Controllers\ReportShareController;
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

    // Tela de recuperação de senha
    Route::get('/esqueci-senha', 'showPasswordRecoveryForm')
        ->name('password.forgot')
        ->middleware('guest');

    // Verifica se o e-mail existe no sistema e inicia a recuperação
    Route::post('/esqueci-senha', 'verifyPasswordRecoveryEmail')
        ->name('password.recovery.verify')
        ->middleware('guest');

    // Fluxo visual de recuperação de senha
    Route::get('/recuperar-senha', 'showPasswordRecoveryForm')
        ->name('password.recovery')
        ->middleware('guest');

    Route::get('/recuperar-senha/redefinir', 'showPasswordRecoveryResetForm')
        ->name('password.recovery.reset')
        ->middleware('guest');

    Route::post('/recuperar-senha/redefinir', 'updateRecoveredPassword')
        ->name('password.recovery.update')
        ->middleware('guest');

    Route::get('/recuperar-senha/sucesso', 'showPasswordRecoverySuccess')
        ->name('password.recovery.success')
        ->middleware('guest');

    Route::post('/recuperar-senha', [\App\Http\Controllers\AuthController::class, 'processDirectPasswordRecovery'])
        ->name('password.recovery.submit');    

    // Processa a redefinição de senha em um endpoint separado
    Route::post('/redefinir-senha', 'resetPassword')
        ->name('password.update')
        ->middleware('guest');

    // Logout - Funciona para cualquier tipo de usuário (admin, secretary, citizen)
    Route::post('/logout', 'logout')
        ->name('logout');
});

/**
 * Rotas de denúncias como visitante
 */
Route::controller(ReportController::class)->group(function () {
    Route::get('/visitante/denuncias/nova', 'createVisitor')
        ->name('visitor.reports.create');

    Route::post('/visitante/denuncias', 'storeVisitor')
        ->name('visitor.reports.store');
});

/**
 * Entrar como visitante
 */
Route::get('/visitante', function () {
    return view('citizen.home-visitante');
})->name('visitor.home');

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
 * Rotas de transferência de denúncias entre secretarias
 * Requer autenticação apenas de Secretário
 */
Route::prefix('secretary')->name('secretary.transfer.')->controller(\App\Http\Controllers\ReportTransferController::class)->middleware('auth:secretary')->group(function () {
    // Encaminhamentos recebidos + denúncias disponíveis para transferir
    Route::get('/transfer-reports', 'index')
        ->name('index');

    // Formulário de transferência de uma denúncia específica
    Route::get('/reports/{report}/transfer', 'create')
        ->name('create');

    // Registra o encaminhamento da denúncia (exige justificativa)
    Route::post('/reports/{report}/transfer', 'store')
        ->name('store');

    // Secretaria de destino aceita o encaminhamento
    Route::post('/transfers/{transfer}/accept', 'accept')
        ->name('accept');

    // Secretaria de destino rejeita o encaminhamento (exige justificativa)
    Route::post('/transfers/{transfer}/reject', 'reject')
        ->name('reject');
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
 * Detalhes e histórico completo de uma denúncia para a secretaria responsável
 * Requer autenticação apenas de Secretário
 */
Route::middleware('auth:secretary')->controller(ReportController::class)->group(function () {
    Route::get('/secretary/reports/{report}', 'showForSecretary')
        ->name('secretary.reports.show');
});

/**
 * Backend de compartilhamento de denúncias entre secretarias
 */
Route::middleware('auth:secretary')
    ->controller(ReportShareController::class)
    ->group(function () {

    // Lista de denúncias disponíveis para compartilhamento
    Route::get('/secretary/share-reports', 'index')
        ->name('secretary.share.index');

    // Realiza o compartilhamento
    Route::post('/secretary/reports/{report}/share', 'store')
        ->name('secretary.share.store');

    // Tela de compartilhamento de uma denúncia
    Route::get('/secretary/reports/{report}/share', 'create')
        ->name('secretary.share.create');

    // Registra uma atualização manual sobre o andamento da denúncia
    Route::post('/secretary/reports/{report}/updates', 'postUpdate')
        ->name('secretary.reports.updates.store');

    // Aceita o compartilhamento de uma denúncia
    Route::patch('/secretary/share/{share}/accept', 'accept')
        ->name('secretary.share.accept');

    // Rejeita o compartilhamento de uma denúncia
    Route::patch('/secretary/share/{share}/reject', 'reject')
        ->name('secretary.share.reject');

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

            // Retorna a secretaria responsável por essa categoria (ativa), em formato de lista
            $secretaries = \App\Models\Secretary::where('id', $category->secretary_id)
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

    // Resolve coordenadas em um endereço legível para o mapa da denúncia
    Route::get('/reports/location/resolve', [ReportController::class, 'resolveLocation'])
        ->name('reports.location.resolve');
});