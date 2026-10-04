<?php

namespace App\Services;

use App\Models\Aluno;
use App\Models\Estagio;
use App\Models\Orientador;
use App\Models\Presenca;
use App\Models\Supervisor;
use App\Models\Usuario;
use Illuminate\Http\Request;

class EstagioService
{
    public const CARGA_HORARIA_MINIMA = 240;

    public function usuarioAtual(Request $request): Usuario
    {
        return Usuario::findOrFail($request->session()->get('usuario_id'));
    }

    public function alunoAtual(Request $request): Aluno
    {
        $usuario = $this->usuarioAtual($request);
        abort_unless($usuario->tipo === 'aluno', 403, 'Somente alunos podem acessar esta operação.');

        return Aluno::where('user_id', $usuario->user_ID)->firstOrFail();
    }

    public function orientadorAtual(Request $request): Orientador
    {
        $usuario = $this->usuarioAtual($request);
        abort_unless($usuario->tipo === 'orientador', 403, 'Somente orientadores podem acessar esta operação.');

        return Orientador::where('user_ID', $usuario->user_ID)->firstOrFail();
    }

    public function supervisorAtual(Request $request): Supervisor
    {
        $usuario = $this->usuarioAtual($request);
        abort_unless($usuario->tipo === 'supervisor', 403, 'Somente supervisores podem validar frequências.');

        return Supervisor::where('user_ID', $usuario->user_ID)->firstOrFail();
    }

    public function estagioAcessivel(Request $request, int $estagioId): Estagio
    {
        $usuario = $this->usuarioAtual($request);

        if ($usuario->tipo === 'aluno') {
            return Estagio::where('aluno_id', $usuario->user_ID)->findOrFail($estagioId);
        }

        if ($usuario->tipo === 'orientador') {
            $orientador = Orientador::where('user_ID', $usuario->user_ID)->firstOrFail();

            return $orientador->estagios()->whereKey($estagioId)->firstOrFail();
        }

        if ($usuario->tipo === 'supervisor') {
            $supervisor = Supervisor::where('user_ID', $usuario->user_ID)->firstOrFail();

            return $supervisor->estagios()->whereKey($estagioId)->firstOrFail();
        }

        abort(403, 'Seu perfil não tem acesso a estágios.');
    }

    public function metricas(Estagio $estagio): array
    {
        $estagio->loadMissing(['jornada', 'presencas']);
        $presencasOficiais = $estagio->presencas->where('modo', Presenca::MODO_OFICIAL);
        $aprovadas = $presencasOficiais
            ->where('status', Presenca::STATUS_APROVADA)
            ->sum(fn (Presenca $presenca) => $presenca->minutos);
        $pendentes = $presencasOficiais
            ->where('status', Presenca::STATUS_PENDENTE)
            ->sum(fn (Presenca $presenca) => $presenca->minutos);
        $inicioSemana = today()->startOfWeek();
        $fimSemana = today()->endOfWeek();
        $previstasSemana = 0;

        if ($estagio->jornada) {
            for ($dia = $inicioSemana->copy(); $dia->lte($fimSemana); $dia->addDay()) {
                $data = $dia->toDateString();
                if (
                    $data >= $estagio->data_inicio->toDateString() &&
                    $data <= $estagio->data_fim->toDateString() &&
                    $estagio->jornada->trabalhaNoDia($dia->dayOfWeekIso)
                ) {
                    $previstasSemana += (int) round($estagio->jornada->horas_diarias * 60);
                }
            }
        }

        $realizadasSemana = $presencasOficiais
            ->filter(function (Presenca $presenca) use ($inicioSemana, $fimSemana) {
                $data = $presenca->data->toDateString();

                return $presenca->status === Presenca::STATUS_APROVADA &&
                    $data >= $inicioSemana->toDateString() &&
                    $data <= $fimSemana->toDateString();
            })
            ->sum(fn (Presenca $presenca) => $presenca->minutos);
        $diferencaSemana = $realizadasSemana - $previstasSemana;
        $metaMinutos = self::CARGA_HORARIA_MINIMA * 60;

        return [
            'aprovadas_minutos' => $aprovadas,
            'pendentes_minutos' => $pendentes,
            'aprovadas_formatadas' => $this->formatarMinutos($aprovadas),
            'pendentes_formatadas' => $this->formatarMinutos($pendentes),
            'restantes_formatadas' => $this->formatarMinutos(max(0, $metaMinutos - $aprovadas)),
            'percentual' => min(100, (int) round($aprovadas / $metaMinutos * 100)),
            'meta_horas' => self::CARGA_HORARIA_MINIMA,
            'semana_inicio' => $inicioSemana->format('d/m/Y'),
            'semana_fim' => $fimSemana->format('d/m/Y'),
            'previstas_semana_minutos' => $previstasSemana,
            'realizadas_semana_minutos' => $realizadasSemana,
            'diferenca_semana_minutos' => $diferencaSemana,
            'previstas_semana_formatadas' => $this->formatarMinutos($previstasSemana),
            'realizadas_semana_formatadas' => $this->formatarMinutos($realizadasSemana),
            'diferenca_semana_formatada' => ($diferencaSemana > 0 ? '+' : ($diferencaSemana < 0 ? '-' : '')) . $this->formatarMinutos(abs($diferencaSemana)),
        ];
    }

    public function formatarMinutos(int $minutos): string
    {
        return intdiv($minutos, 60) . 'h' . (($minutos % 60) > 0 ? sprintf('%02d', $minutos % 60) : '');
    }

    public function statusApresentacao(Estagio $estagio): string
    {
        if (
            $estagio->status === Estagio::STATUS_ATIVO &&
            $estagio->data_fim->toDateString() < now()->toDateString()
        ) {
            return Estagio::STATUS_FINALIZADO;
        }

        return $estagio->status;
    }
}