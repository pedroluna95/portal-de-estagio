<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Usuario;

class AdminMiddleware
{
    /**
     * Verifica se o usuário autenticado na sessão é administrador.
     */
    public function handle(Request $request, Closure $next)
    {
        $usuarioId = $request->session()->get('usuario_id');
        if (! $usuarioId) {
            return redirect('/login');
        }

        $user = Usuario::find($usuarioId);
        if (! $user) {
            return redirect('/login');
        }

        $tipo = strtolower((string) $user->tipo);

        if (! $request->session()->has('user_type')) {
            $request->session()->put('user_type', $tipo);
        }
        if (! $request->session()->has('user_id')) {
            $request->session()->put('user_id', $user->user_ID);
        }
        if (! $request->session()->has('user_name')) {
            $request->session()->put('user_name', $user->Nome);
        }

        if ($tipo !== 'administrador') {
            abort(403, 'Acesso negado: apenas administradores.');
        }

        return $next($request);
    }
}
