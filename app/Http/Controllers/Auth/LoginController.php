<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /**
     * Exibe o formulário de login.
     */
    public function showLoginForm(Request $request)
    {
        if ($request->session()->has('usuario_id')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Processa a autenticação do usuário.
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        try {
            $user = Usuario::where('Email', $credentials['email'])->first();
            $senhaValida = $user && Hash::check($credentials['senha'], $user->Senha);
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
            'usuario_id'      => $user->user_ID,
            'user_id'         => $user->user_ID,
            'usuario_nome'    => $user->Nome,
            'user_name'       => $user->Nome,
            'user_type'       => $tipo,
            'user_email'      => $user->Email,
            'user_cpf'        => $user->cpf ?? '',
            'user_matricula'  => $user->matricula ?? '',
            'user_siape'      => $user->siape ?? '',
        ]);

        return redirect('/dashboard');
    }

    /**
     * Encerra a sessão do usuário.
     */
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
