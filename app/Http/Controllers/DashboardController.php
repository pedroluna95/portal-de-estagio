<?php

namespace App\Http\Controllers;

use App\Models\EmpresasConveniadas;
use App\Models\Estagio;
use App\Models\Presenca;
use App\Models\Usuario;
use App\Services\AdminDashboardService;
use App\Services\EstagioService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly EstagioService $estagioService,
        private readonly AdminDashboardService $adminDashboardService,
    ) {}

    public function show(Request $request)
    {
        $usuario = $this->estagioService->usuarioAtual($request);
        $dados = [
            'total_usuarios' => Usuario::count(),
            'total_empresas' => EmpresasConveniadas::count(),
            'papelDashboard' => $usuario->tipo,
        ];

        if ($usuario->tipo === 'aluno') {
            $aluno = $this->estagioService->alunoAtual($request);
            $estagioAtivo = $aluno->estagios()
                ->with(['aluno', 'jornada', 'presencas', 'orientador.usuario'])
                ->where('status', Estagio::STATUS_ATIVO)
                ->whereDate('data_fim', '>=', today()->toDateString())
                ->orderByDesc('created_at')
                ->first();

            $dados['estagioAtivo'] = $estagioAtivo;
            $dados['metricas'] = $estagioAtivo
                ? $this->estagioService->metricas($estagioAtivo)
                : null;
        } elseif ($usuario->tipo === 'orientador') {
            $orientador = $this->estagioService->orientadorAtual($request);
            $estagios = $orientador->estagios()
                ->with([
                    'aluno',
                    'jornada',
                    'presencas' => fn ($query) => $query->where('modo', Presenca::MODO_OFICIAL),
                ])
                ->orderByDesc('created_at')
                ->get();

            $dados['estagios'] = $estagios;
            $dados['frequenciasPendentes'] = $estagios->sum(fn (Estagio $estagio) =>
                $estagio->presencas->where('modo', Presenca::MODO_OFICIAL)
                    ->where('status', Presenca::STATUS_PENDENTE)->count()
            );
        } elseif ($usuario->tipo === 'supervisor') {
            $supervisor = $this->estagioService->supervisorAtual($request);
            $estagios = $supervisor->estagios()
                ->with(['aluno', 'orientador.usuario', 'presencas' => fn ($query) => $query->where('modo', Presenca::MODO_OFICIAL)])
                ->orderByDesc('created_at')
                ->get();

            $dados['estagios'] = $estagios;
            $dados['frequenciasPendentes'] = $estagios->sum(fn (Estagio $estagio) =>
                $estagio->presencas->where('status', Presenca::STATUS_PENDENTE)->count()
            );
        } elseif ($usuario->tipo === 'administrador') {
            $dados = array_merge($dados, $this->adminDashboardService->getDashboardData($request));
        }

        return view('dashboard', $dados);
    }
}
