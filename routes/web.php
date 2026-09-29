<?php

use App\Http\Controllers\TablesController;
use App\Http\Controllers\UsuarioController;
use App\Http\Middleware\AdminMiddleware;
use App\Models\Usuario;
use App\Services\UsuarioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

Route::redirect('/', '/home');
Route::view('/home', 'home');
Route::view('/login', 'login')->name('login');

Route::post('/login', function (Request $request) {
    $data = $request->validate([
        'email' => ['required', 'email'],
        'senha' => ['required', 'string'],
    ]);

    $user = Usuario::where('Email', $data['email'])->first();
    if (! $user || ! Hash::check($data['senha'], $user->Senha)) {
        return back()->withErrors(['email' => 'E-mail ou senha inválidos.'])->withInput($request->only('email'));
    }

    $tipo = $user->tipo;

    $request->session()->regenerate();
    $request->session()->put([
        'usuario_id' => $user->user_ID,
        'user_id' => $user->user_ID,
        'usuario_nome' => $user->Nome,
        'user_name' => $user->Nome,
        'user_type' => $tipo,
        'user_email' => $user->Email,
        'user_cpf' => $user->cpf ?? '',
        'user_matricula' => $user->matricula ?? '',
        'user_siape' => $user->siape ?? '',
    ]);

    return redirect('/dashboard');
});

$tiposCadastro = ['aluno', 'supervisor', 'orientador', 'contratante'];
Route::get('/cadastro/{tipo?}', function (?string $tipo = null) use ($tiposCadastro) {
    if ($tipo !== null && ! in_array(strtolower($tipo), $tiposCadastro, true)) {
        abort(404);
    }
    return view('cadastro', ['tipoInicial' => $tipo ? strtolower($tipo) : null]);
})->name('cadastro');

Route::post('/cadastro', function (Request $request) use ($tiposCadastro) {
    $tipo = strtolower((string) $request->input('tipo'));
    $data = $request->validate([
        'nome' => ['required', 'string', 'max:60'],
        'email' => ['required', 'email', 'max:45', 'unique:usuarios,Email'],
        'cpf' => ['required', 'string', 'cpf_valido'],
        'senha' => ['required', 'string', 'min:6', 'same:confirmar_senha'],
        'confirmar_senha' => ['required', 'string'],
        'tipo' => ['required', Rule::in($tiposCadastro)],
        'matricula' => [$tipo === 'aluno' ? 'required' : 'nullable', 'string', 'max:60'],
        'curso' => [$tipo === 'aluno' ? 'required' : 'nullable', 'in:Informatica,Automacao Industrial,Telecomunicações,Enfermagem'],
        'siape' => [$tipo === 'orientador' ? 'required' : 'nullable', 'digits:8'],
    ], [
        'senha.same' => 'A confirmação de senha não confere.',
        'cpf.cpf_valido' => 'Informe um CPF válido.',
    ]);

    try {
        UsuarioService::create($data);
    } catch (\Throwable $e) {
        $requestId = (string) str()->uuid();
        Log::error('Falha ao criar usuário', ['request_id' => $requestId, 'exception' => $e]);
        return back()->withErrors(['error' => "Não foi possível criar a conta. Código: {$requestId}"])->withInput();
    }

    return redirect('/login')->with('success', 'Conta criada com sucesso.');
});

Route::middleware('web')->group(function () {
    Route::middleware(\App\Http\Middleware\SessionAuthMiddleware::class)->group(function () {
        Route::view('/aluno', 'dashboard');
        Route::view('/controle-horas', 'controle-horas');
        Route::view('/tutorial', 'tutorial');
        Route::view('/perfil', 'perfil');
        Route::view('/empresas', 'empresas');
        Route::view('/vagas', 'vagas');
        Route::view('/inscricoes', 'vagas');
        Route::view('/vagas/{id}', 'vaga-detalhe');
        Route::get('/dashboard', function (Request $request) {
            return view('dashboard', ['total_usuarios' => Usuario::count()]);
        });
    });
});

Route::prefix('admin')->middleware(AdminMiddleware::class)->group(function () {
    Route::view('usuarios', 'admin.usuarios.index');
    Route::view('usuarios/create', 'admin.usuarios.create');
    Route::get('usuarios/{id}/edit', fn ($id) => view('admin.usuarios.edit', ['id' => $id]));
    Route::view('tables', 'admin.tables.index');
    Route::get('api/tables', [TablesController::class, 'listTables']);
    Route::get('api/tables/{table}', [TablesController::class, 'rows']);
    Route::get('api/usuarios', [UsuarioController::class, 'index']);
    Route::post('api/usuarios', [UsuarioController::class, 'store']);
    Route::get('api/usuarios/{id}', [UsuarioController::class, 'show']);
    Route::put('api/usuarios/{id}', [UsuarioController::class, 'update']);
    Route::delete('api/usuarios/{id}', [UsuarioController::class, 'destroy']);
});

Route::post('/logout', function (Request $request) {
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->middleware(\App\Http\Middleware\SessionAuthMiddleware::class);
