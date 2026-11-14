<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\UsuarioService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    private const TIPOS_CADASTRO = ['aluno', 'supervisor', 'orientador'];

    /**
     * Exibe o formulário de cadastro.
     */
    public function showRegistrationForm(?string $tipo = null)
    {
        if ($tipo !== null && ! in_array(strtolower($tipo), self::TIPOS_CADASTRO, true)) {
            abort(404);
        }

        return view('auth.cadastro', ['tipoInicial' => $tipo ? strtolower($tipo) : null]);
    }

    /**
     * Processa a criação do novo usuário via UsuarioService.
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

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
    }
}
