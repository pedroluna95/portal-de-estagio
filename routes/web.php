<?php

use App\Http\Controllers\TablesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstagioController;
use App\Http\Controllers\JornadaEstagioController;
use App\Http\Controllers\PresencaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Middleware\AdminMiddleware;
use App\Models\Usuario;
use App\Services\UsuarioService;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
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
    ], [
        'email.required' => 'Informe seu e-mail.',
        'email.email' => 'Informe um endereço de e-mail válido.',
        'senha.required' => 'Informe sua senha.',
    ]);

    try {
        $user = Usuario::where('Email', $data['email'])->first();
        $senhaValida = $user && Hash::check($data['senha'], $user->Senha);
    } catch (\Throwable $e) {
        $requestId = (string) str()->uuid();
        Log::error('Falha ao autenticar usuário', ['request_id' => $requestId, 'exception' => $e]);

        return back()->withErrors([
            'error' => "Não foi possível verificar seu acesso agora. Tente novamente. Código de atendimento: {$requestId}",
        ])->withInput($request->only('email'));
    }

    if (! $senhaValida) {
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
        'matricula' => [$tipo === 'aluno' ? 'required' : 'nullable', 'string', 'max:30', Rule::unique('alunos', 'matricula')],
        'curso' => [$tipo === 'aluno' ? 'required' : 'nullable', 'in:Informatica,Automacao Industrial,Telecomunicações,Enfermagem'],
        'siape' => [$tipo === 'orientador' ? 'required' : 'nullable', 'digits:8', Rule::unique('orientadores', 'siape')],
    ], [
        'senha.same' => 'A confirmação de senha não confere.',
        'senha.required' => 'Informe uma senha.',
        'senha.min' => 'A senha deve ter pelo menos 6 caracteres.',
        'confirmar_senha.required' => 'Confirme sua senha.',
        'cpf.cpf_valido' => 'Informe um CPF válido.',
        'email.required' => 'Informe seu e-mail.',
        'email.email' => 'Informe um endereço de e-mail válido.',
        'email.unique' => 'Este e-mail já está cadastrado. Use outro endereço ou faça login.',
        'nome.required' => 'Informe seu nome completo.',
        'nome.max' => 'O nome deve ter no máximo 60 caracteres.',
        'matricula.required' => 'Informe sua matrícula de aluno.',
        'matricula.max' => 'A matrícula deve ter no máximo 30 caracteres.',
        'matricula.unique' => 'Esta matrícula já está cadastrada.',
        'curso.required' => 'Selecione seu curso.',
        'curso.in' => 'Selecione um dos cursos disponíveis.',
        'siape.required' => 'Informe seu SIAPE.',
        'siape.digits' => 'O SIAPE deve conter exatamente 8 dígitos.',
        'siape.unique' => 'Este SIAPE já está cadastrado.',
        'tipo.in' => 'Selecione um tipo de usuário válido.',
    ]);

    try {
        UsuarioService::create($data);
    } catch (\Throwable $e) {
        $requestId = (string) str()->uuid();
        Log::error('Falha ao criar usuário', ['request_id' => $requestId, 'exception' => $e]);

        $message = match (true) {
            $e instanceof QueryException && (int) ($e->errorInfo[1] ?? 0) === 1062
                => 'Já existe um cadastro usando este e-mail, CPF, matrícula ou SIAPE.',
            $e instanceof QueryException && in_array((int) ($e->errorInfo[1] ?? 0), [1048, 1364], true)
                => 'Faltam dados obrigatórios para o tipo de usuário selecionado. Confira os campos e tente novamente.',
            $e instanceof QueryException && in_array((int) ($e->errorInfo[1] ?? 0), [2002, 2006, 2013], true)
                => 'O banco de dados está indisponível no momento. Tente novamente mais tarde.',
            $e instanceof \InvalidArgumentException
                => 'O tipo de usuário selecionado não está disponível. Volte e escolha outro tipo.',
            default => 'Não foi possível concluir o cadastro agora. Tente novamente ou informe este código ao suporte.',
        };

        return back()
            ->withErrors(['error' => "{$message} Código de atendimento: {$requestId}"])
            ->withInput($request->except(['senha', 'confirmar_senha']));
    }

    return redirect('/login')->with('success', 'Conta criada com sucesso.');
});

Route::middleware('web')->group(function () {
    Route::middleware(\App\Http\Middleware\SessionAuthMiddleware::class)->group(function () {
        Route::view('/aluno', 'dashboard');
        Route::view('/tutorial', 'tutorial');
        Route::get('/perfil', function (Request $request) {
            $usuario = Usuario::findOrFail($request->session()->get('usuario_id'));

            return view('perfil', ['cursoAluno' => $usuario->curso]);
        });
        Route::view('/empresas', 'empresas');
        Route::view('/vagas', 'vagas');
        Route::view('/inscricoes', 'vagas');
        Route::view('/vagas/{id}', 'vaga-detalhe');
        Route::get('/dashboard', [DashboardController::class, 'show'])->name('dashboard');
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
