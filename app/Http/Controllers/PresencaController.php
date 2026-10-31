<?php

namespace App\Http\Controllers;

use App\Models\Estagio;
use App\Models\Presenca;
use App\Services\EstagioService;
use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PresencaController extends Controller
{
    public function __construct(private readonly EstagioService $estagioService) {}

    public function store(Request $request, int $estagioId)
    {
        $aluno = $this->estagioService->alunoAtual($request);
        $estagio = $aluno->estagios()->with('jornada')->findOrFail($estagioId);

        if ($this->estagioService->statusApresentacao($estagio) !== Estagio::STATUS_ATIVO) {
            throw ValidationException::withMessages([
                'estagio' => 'Só é possível registrar frequência em um estágio ativo.',
            ]);
        }

        if (! $estagio->jornada) {
            throw ValidationException::withMessages([
                'jornada' => 'Configure a jornada antes de registrar uma frequência.',
            ]);
        }

        $dados = $request->validate([
            'modo' => ['required', 'in:oficial,pessoal'],
            'data' => [
                'bail',
                'required',
                'date_format:Y-m-d',
                'before:today',
                function (string $attribute, mixed $value, Closure $fail) use ($estagio): void {
                    if (
                        $value < $estagio->data_inicio->toDateString() ||
                        $value > $estagio->data_fim->toDateString()
                    ) {
                        $fail('A data deve estar dentro do período do estágio.');
                    }
                },
            ],
            'entrada' => ['required', 'date_format:H:i'],
            'saida' => ['required', 'date_format:H:i', 'after:entrada'],
            'observacao' => ['nullable', 'string', 'max:500'],
        ], [
            'data.required' => 'Informe a data da frequência.',
            'modo.required' => 'Escolha se o registro será oficial ou pessoal.',
            'modo.in' => 'Escolha um modo de registro válido.',
            'data.date_format' => 'Informe uma data válida.',
            'data.before' => 'Registre somente períodos encerrados. A data deve ser anterior a hoje.',
            'data.unique' => 'Já existe uma frequência registrada para esse dia.',
            'entrada.required' => 'Informe o horário de entrada.',
            'entrada.date_format' => 'O horário de entrada é inválido.',
            'saida.required' => 'Informe o horário de saída.',
            'saida.date_format' => 'O horário de saída é inválido.',
            'saida.after' => 'A saída deve ser posterior à entrada.',
            'observacao.max' => 'A observação deve ter no máximo 500 caracteres.',
        ]);

        if ($dados['modo'] === Presenca::MODO_OFICIAL && ! $estagio->supervisor_id) {
            throw ValidationException::withMessages([
                'modo' => 'Este estágio ainda não tem um supervisor responsável. Peça ao orientador para vinculá-lo antes de enviar uma presença oficial.',
            ]);
        }

        $diaIso = (int) \Illuminate\Support\Carbon::parse($dados['data'])->dayOfWeekIso;
        if (! $estagio->jornada->trabalhaNoDia($diaIso)) {
            throw ValidationException::withMessages([
                'data' => 'A data selecionada não está prevista na jornada deste estágio.',
            ]);
        }

        if (Presenca::where('estagio_id', $estagio->id)->whereDate('data', $dados['data'])->exists()) {
            throw ValidationException::withMessages([
                'data' => 'Já existe uma frequência registrada para esse dia.',
            ]);
        }

        try {
            $presenca = Presenca::create([
                'estagio_id' => $estagio->id,
                'data' => $dados['data'],
                'entrada' => $dados['entrada'],
                'saida' => $dados['saida'],
                'modo' => $dados['modo'],
                'status' => $dados['modo'] === Presenca::MODO_OFICIAL
                    ? Presenca::STATUS_PENDENTE
                    : Presenca::STATUS_PESSOAL,
                'observacao' => $dados['observacao'] ?? null,
            ]);
        } catch (QueryException $exception) {
            $erro = strtolower($exception->getMessage());
            if (
                (int) ($exception->errorInfo[1] ?? 0) === 1062 ||
                str_contains($erro, 'unique constraint failed') ||
                $exception->getCode() === '23505'
            ) {
                throw ValidationException::withMessages([
                    'data' => 'Já existe uma frequência registrada para esse dia.',
                ]);
            }

            throw $exception;
        }

        $mensagem = $dados['modo'] === Presenca::MODO_OFICIAL
            ? 'Presença oficial enviada ao supervisor para validação.'
            : 'Registro pessoal salvo. Ele não será enviado ao supervisor nem contará nas horas oficiais.';

        return redirect()->route('estagios.show', $estagio)
            ->with('success', $mensagem . ' Duração: ' . $this->estagioService->formatarMinutos($presenca->minutos) . '.');
    }

    public function aprovar(Request $request, int $presencaId)
    {
        return $this->decidir($request, $presencaId, Presenca::STATUS_APROVADA, null);
    }

    public function recusar(Request $request, int $presencaId)
    {
        $dados = $request->validate([
            'parecer_supervisor' => ['required', 'string', 'min:3', 'max:500'],
        ], [
            'parecer_supervisor.required' => 'Informe o motivo da recusa.',
            'parecer_supervisor.min' => 'O motivo deve ter pelo menos 3 caracteres.',
            'parecer_supervisor.max' => 'O motivo deve ter no máximo 500 caracteres.',
        ]);

        return $this->decidir($request, $presencaId, Presenca::STATUS_RECUSADA, $dados['parecer_supervisor']);
    }

    private function decidir(Request $request, int $presencaId, string $status, ?string $parecer)
    {
        $supervisor = $this->estagioService->supervisorAtual($request);
        $presenca = Presenca::with('estagio')->findOrFail($presencaId);
        abort_unless($presenca->modo === Presenca::MODO_OFICIAL, 404);
        $estagio = $supervisor->estagios()->findOrFail($presenca->estagio_id);

        DB::transaction(function () use ($presencaId, $status, $parecer) {
            $presencaBloqueada = Presenca::whereKey($presencaId)->lockForUpdate()->firstOrFail();
            if (
                $presencaBloqueada->modo !== Presenca::MODO_OFICIAL ||
                $presencaBloqueada->status !== Presenca::STATUS_PENDENTE
            ) {
                throw ValidationException::withMessages([
                    'status' => 'Somente presenças oficiais pendentes podem ser analisadas pelo supervisor.',
                ]);
            }

            $presencaBloqueada->update([
                'status' => $status,
                'parecer_supervisor' => $parecer,
            ]);
        });

        $mensagem = $status === Presenca::STATUS_APROVADA
            ? 'Presença oficial validada pelo supervisor.'
            : 'Presença oficial recusada pelo supervisor.';

        return redirect()->route('estagios.show', $estagio)
            ->with('success', $mensagem);
    }
}