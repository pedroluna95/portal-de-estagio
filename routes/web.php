<?php

use App\Http\Controllers\AdminDataController;
use App\Http\Controllers\AdminEmpresasController;
use App\Http\Controllers\Aluno\PerfilController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EstagioController;
use App\Http\Controllers\JornadaEstagioController;
use App\Http\Controllers\PresencaController;
use App\Http\Controllers\TablesController;
use App\Http\Controllers\UsuarioController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SessionAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Redirecionamento da raiz e Home pública
Route::get('/', function (Request $request) {
    return $request->session()->has('usuario_id')
        ? redirect()->route('dashboard')
        : redirect('/home');
});

Route::get('/home', function (Request $request) {
    return $request->session()->has('usuario_id')
        ? redirect()->route('dashboard')
        : view('home');
});

// Autenticação (Login / Logout)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware(SessionAuthMiddleware::class)
    ->name('logout');

// Cadastro
Route::get('/cadastro/{tipo?}', [RegisterController::class, 'showRegistrationForm'])->name('cadastro');
Route::post('/cadastro', [RegisterController::class, 'register']);

// Rotas Autenticadas (Alunos, Professores, Orientadores, Supervisores)
Route::middleware(['web', SessionAuthMiddleware::class])->group(function () {
    Route::redirect('/aluno', '/dashboard');
    Route::view('/tutorial', 'tutorial')->name('tutorial');
    Route::get('/perfil', [PerfilController::class, 'show'])->name('perfil');
    Route::get('/dashboard', [DashboardController::class, 'show'])->name('dashboard');

    // Empresas conveniadas (visão do usuário)
    Route::get('/empresas', [EmpresaController::class, 'index'])->name('empresas.index');

    // Gestão de Estágios e Presenças
    Route::get('/controle-horas', [EstagioController::class, 'index'])->name('controle-horas');
    Route::get('/estagios', [EstagioController::class, 'index'])->name('estagios.index');
    Route::post('/estagios', [EstagioController::class, 'store'])->name('estagios.store');
    Route::get('/estagios/{estagioId}', [EstagioController::class, 'show'])
        ->whereNumber('estagioId')
        ->name('estagios.show');
    Route::post('/estagios/{estagioId}/finalizar', [EstagioController::class, 'finalizar'])
        ->whereNumber('estagioId')
        ->name('estagios.finalizar');
    Route::post('/estagios/{estagioId}/supervisor', [EstagioController::class, 'atribuirSupervisor'])
        ->whereNumber('estagioId')
        ->name('estagios.supervisor.store');
    Route::post('/estagios/{estagioId}/jornada', [JornadaEstagioController::class, 'store'])
        ->whereNumber('estagioId')
        ->name('estagios.jornada.store');
    Route::post('/estagios/{estagioId}/presencas', [PresencaController::class, 'store'])
        ->whereNumber('estagioId')
        ->name('estagios.presencas.store');
    Route::post('/presencas/{presencaId}/aprovar', [PresencaController::class, 'aprovar'])
        ->whereNumber('presencaId')
        ->name('presencas.aprovar');
    Route::post('/presencas/{presencaId}/recusar', [PresencaController::class, 'recusar'])
        ->whereNumber('presencaId')
        ->name('presencas.recusar');
});

// Rotas Administrativas
Route::prefix('admin')->middleware(AdminMiddleware::class)->group(function () {
    // Gestão de Empresas e Convênios
    Route::get('empresas', [AdminEmpresasController::class, 'index'])->name('admin.empresas');
    Route::post('empresas/importar', [AdminEmpresasController::class, 'importar'])->name('admin.empresas.importar');
    Route::post('empresas/limpar', [AdminEmpresasController::class, 'limpar'])->name('admin.empresas.limpar');
    Route::post('empresas/enriquecer-lote', [AdminEmpresasController::class, 'enriquecerLote'])->name('admin.empresas.enriquecer_lote');
    Route::post('empresas/salvar-seeder', [AdminEmpresasController::class, 'salvarSeeder'])->name('admin.empresas.salvar_seeder');
    Route::get('empresas/download-csv/{arquivo}', [AdminEmpresasController::class, 'downloadCsv'])->name('admin.empresas.download_csv');
    Route::get('empresas/exportar-csv', [AdminEmpresasController::class, 'exportarBancoCsv'])->name('admin.empresas.exportar_csv');

    // Gestão de Usuários
    Route::view('usuarios', 'admin.usuarios.index')->name('admin.usuarios');
    Route::view('usuarios/create', 'admin.usuarios.create')->name('admin.usuarios.create');
    Route::get('usuarios/{id}/edit', fn ($id) => view('admin.usuarios.edit', ['id' => $id]))->name('admin.usuarios.edit');

    // Visualizador de Tabelas
    Route::view('tables', 'admin.tables.index')->name('admin.tables');
    Route::get('api/tables', [TablesController::class, 'listTables']);
    Route::get('api/tables/{table}', [TablesController::class, 'rows']);
    Route::delete('api/dados', [AdminDataController::class, 'destroy']);

    // API de Usuários para CRUD dinâmico
    Route::get('api/usuarios', [UsuarioController::class, 'index']);
    Route::post('api/usuarios', [UsuarioController::class, 'store']);
    Route::get('api/usuarios/{id}', [UsuarioController::class, 'show']);
    Route::put('api/usuarios/{id}', [UsuarioController::class, 'update']);
    Route::delete('api/usuarios/{id}', [UsuarioController::class, 'destroy']);
});
