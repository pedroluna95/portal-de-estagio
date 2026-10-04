<?php

namespace App\Http\Controllers;

use App\Models\Estagio;
use App\Models\JornadaEstagio;
use App\Services\EstagioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class JornadaEstagioController extends Controller
{
    public function __construct(private readonly EstagioService $estagioService) {}

    public function store(Request $request, int $estagioId)
    {
        $aluno = $this->estagioService->alunoAtual($request);
        $estagio = $aluno->estagios()->findOrFail($estagioId);

        if ($this->estagioService->statusApresentacao($estagio) !== Estagio::STATUS_ATIVO) {
            throw ValidationException::withMessages([
                'estagio' => 'A jornada só pode ser configurada em um estágio ativo.',
            ]);
        }

        if ($estagio->jornada()->exists()) {
            throw ValidationException::withMessages([
                'jornada' => 'A jornada deste estágio já foi configurada e não pode ser alterada retroativamente.',
            ]);
        }

        $dias = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];
        $dados = $request->validate([
            'horas_diarias' => ['required', 'numeric', 'gt:0', 'lte:24'],
            ...array_fill_keys($dias, ['sometimes', 'boolean']),
        ], [
            'horas_diarias.required' => 'Informe as horas previstas por dia.',
            'horas_diarias.numeric' => 'As horas diárias precisam ser um número.',
            'horas_diarias.gt' => 'As horas diárias devem ser maiores que zero.',
            'horas_diarias.lte' => 'As horas diárias não podem ultrapassar 24.',
        ]);

        $diasSelecionados = collect($dias)->filter(fn (string $dia) => (bool) ($dados[$dia] ?? false));
        if ($diasSelecionados->isEmpty()) {
            throw ValidationException::withMessages([
                'dias' => 'Selecione pelo menos um dia da semana.',
            ]);
        }

        DB::transaction(function () use ($dados, $dias, $estagio) {
            $estagioBloqueado = Estagio::whereKey($estagio->id)->lockForUpdate()->firstOrFail();
            if ($estagioBloqueado->jornada()->exists()) {
                throw ValidationException::withMessages([
                    'jornada' => 'A jornada deste estágio já foi configurada.',
                ]);
            }

            $jornada = ['estagio_id' => $estagio->id, 'horas_diarias' => $dados['horas_diarias']];
            foreach ($dias as $dia) {
                $jornada[$dia] = (bool) ($dados[$dia] ?? false);
            }

            JornadaEstagio::create($jornada);
        });

        return redirect()->route('estagios.show', $estagio)
            ->with('success', 'Jornada configurada com sucesso.');
    }
}