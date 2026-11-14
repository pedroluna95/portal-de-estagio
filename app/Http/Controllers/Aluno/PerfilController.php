<?php

namespace App\Http\Controllers\Aluno;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;

class PerfilController extends Controller
{
    /**
     * Exibe o perfil do usuário logado.
     */
    public function show(Request $request)
    {
        $usuario = Usuario::findOrFail($request->session()->get('usuario_id'));

        return view('perfil', [
            'usuario'    => $usuario,
            'cursoAluno' => $usuario->curso,
        ]);
    }
}
