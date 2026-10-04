<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Models\Estagio;
use App\Models\Supervisor;
use App\Services\EstagioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EstagioController extends Controller
{
    public function __construct(private readonly EstagioService $estagioService) {}

    public function index(Request $request)
    {
        $usuario = $this->estagioService->usuarioAtual($request);

        if ($usuario->tipo === 'orientador') {
            $orientador = $this->estagioService->orientadorAtual($request);
            $estagios = $orientador->estagios()
                ->with([
                    'aluno',
                    'jornada',
                    'presencas' => fn ($query) => $query->where('modo', 'oficial'),
                    'orientador.usuario',
                    'supervisor.usuario',
                ])
                ->orderByDesc('created_at')
                ->get();
            $supervisores = Supervisor::with('usuario')->orderBy('empresa')->get();
            $alunosDisponiveis = Aluno::whereDoesntHave('estagios', function ($query) {
                $query->where('status', Estagio::STATUS_ATIVO)
                    ->whereDate('data_fim', '>=', today()->toDateString());
            })->orderBy('nome')->get();
            $metricas = $estagios->mapWithKeys(fn (Estagio $estagio) => [
                $estagio->id => $this->estagioService->metricas($estagio),
            ]);

            return view('estagios.index', [
                'papel' => 'orientador',
                'estagios' => $estagios,
                'alunosDisponiveis' => $alunosDisponiveis,
                'supervisores' => $supervisores,
                'metricas' => $metricas,
            ]);
        }

        if ($usuario->tipo === 'supervisor') {
            $supervisor = $this->estagioService->supervisorAtual($request);
            $estagios = $supervisor->estagios()
                ->with([
                    'aluno',
                    'orientador.usuario',
                    'supervisor.usuario',
                    'presencas' => fn ($query) => $query->where('modo', 'oficial'),
                ])
                ->orderByDesc('created_at')
                ->get();
            $frequenciasPendentes = $estagios->sum(fn (Estagio $estagio) =>
                $estagio->presencas->where('status', 'pendente')->count()
            );

            return view('estagios.index', [
                'papel' => 'supervisor',
                'estagios' => $estagios,
                'frequenciasPendentes' => $frequenciasPendentes,
            ]);
        }

        $aluno = $this->estagioService->alunoAtual($request);
        $estagios = $aluno->estagios()
            ->with(['aluno', 'jornada', 'presencas', 'orientador.usuario', 'supervisor.usuario'])
            ->orderByDesc('created_at')
            ->get();
        $estagioAtivo = $estagios->first(fn (Estagio $estagio) =>
            $this->estagioService->statusApresentacao($estagio) === Estagio::STATUS_ATIVO
        );
        $metricas = $estagios->mapWithKeys(fn (Estagio $estagio) => [
            $estagio->id => $this->estagioService->metricas($estagio),
        ]);

        return view('estagios.index', [
            'papel' => 'aluno',
            'estagios' => $estagios,
            'estagioAtivo' => $estagioAtivo,
            'metricas' => $metricas,
        ]);
    }

    public function store(Request $request)
    {
        $orientador = $this->estagioService->orientadorAtual($request);
        $dados = $request->validate([
            'aluno_id' => ['required', 'integer', 'exists:alunos,user_id'],
            'local' => ['required', 'string', 'max:150'],
            'data_inicio' => ['required', 'date'],
            'data_fim' => ['required', 'date', 'after:data_inicio'],
            'supervisor_id' => ['nullable', 'integer', 'exists:supervisores,supervisor_ID'],
        ], [
            'aluno_id.required' => 'Selecione o aluno para este estágio.',
            'aluno_id.exists' => 'O aluno selecionado não existe.',
            'local.required' => 'Informe o local do estágio.',
            'local.max' => 'O local deve ter no máximo 150 caracteres.',
            'data_inicio.required' => 'Informe a data inicial.',
            'data_inicio.date' => 'A data inicial é inválida.',
            'data_fim.required' => 'Informe a data final.',
            'data_fim.date' => 'A data final é inválida.',
            'data_fim.after' => 'A data final deve ser posterior à data inicial.',
            'supervisor_id.exists' => 'O supervisor selecionado não existe.',
        ]);

        $estagio = DB::transaction(function () use ($dados, $orientador) {
            $aluno = Aluno::where('user_id', $dados['aluno_id'])->lockForUpdate()->firstOrFail();

            Estagio::where('aluno_id', $aluno->user_id)
                ->where('status', Estagio::STATUS_ATIVO)
                ->whereDate('data_fim', '<', today()->toDateString())
                ->update(['status' => Estagio::STATUS_FINALIZADO]);

            $jaPossuiEstagioAtivo = Estagio::where('aluno_id', $aluno->user_id)
                ->where('status', Estagio::STATUS_ATIVO)
                ->exists();

            if ($jaPossuiEstagioAtivo) {
                throw ValidationException::withMessages([
                    'aluno_id' => 'Este aluno já possui um estágio ativo.',
                ]);
            }

            return Estagio::create([
                'aluno_id' => $aluno->user_id,
                'orientador_id' => $orientador->orientador_ID,
                'supervisor_id' => $dados['supervisor_id'] ?? null,
                'local' => $dados['local'],
                'data_inicio' => $dados['data_inicio'],
                'data_fim' => $dados['data_fim'],
                'status' => Estagio::STATUS_ATIVO,
            ]);
        });

        return redirect()->route('estagios.show', $estagio)
            ->with('success', 'Estágio inicializado com sucesso.');
    }

    public function show(Request $request, int $estagioId)
    {
        $estagio = $this->estagioService->estagioAcessivel($request, $estagioId);
        if ($this->estagioService->statusApresentacao($estagio) === Estagio::STATUS_FINALIZADO && $estagio->status === Estagio::STATUS_ATIVO) {
            $estagio->update(['status' => Estagio::STATUS_FINALIZADO]);
        }
        $papel = $this->estagioService->usuarioAtual($request)->tipo;
        $supervisoresDisponiveis = $papel === 'orientador'
            ? Supervisor::with('usuario')->orderBy('empresa')->get()
            : collect();
        $estagio->load([
            'aluno',
            'jornada',
            'presencas' => function ($query) use ($papel) {
                if ($papel !== 'aluno') {
                    $query->where('modo', 'oficial');
                }
                $query->orderByDesc('data');
            },
            'orientador.usuario',
            'supervisor.usuario',
        ]);

        return view('estagios.show', [
            'estagio' => $estagio,
            'papel' => $papel,
            'supervisoresDisponiveis' => $supervisoresDisponiveis,
            'metricas' => $this->estagioService->metricas($estagio),
            'estagioService' => $this->estagioService,
            'calendarData' => [
                'inicio' => $estagio->data_inicio->toDateString(),
                'fim' => $estagio->data_fim->toDateString(),
                'hoje' => today()->toDateString(),
                'dias' => $estagio->jornada
                    ? collect(['segunda' => 1, 'terca' => 2, 'quarta' => 3, 'quinta' => 4, 'sexta' => 5, 'sabado' => 6, 'domingo' => 7])
                        ->filter(fn (int $dia, string $campo) => $estagio->jornada->{$campo})
                        ->values()
                    : [],
                'presencas' => $estagio->presencas->mapWithKeys(fn ($presenca) => [
                    $presenca->data->toDateString() => [
                        'modo' => $presenca->modo,
                        'status' => $presenca->status,
                    ],
                ]),
            ],
        ]);
    }

    public function atribuirSupervisor(Request $request, int $estagioId)
    {
        $orientador = $this->estagioService->orientadorAtual($request);
        $estagio = $orientador->estagios()->findOrFail($estagioId);
        $dados = $request->validate([
            'supervisor_id' => ['required', 'integer', 'exists:supervisores,supervisor_ID'],
        ], [
            'supervisor_id.required' => 'Selecione o supervisor responsável.',
            'supervisor_id.exists' => 'O supervisor selecionado não existe.',
        ]);

        if (
            $estagio->supervisor_id !== null &&
            (int) $estagio->supervisor_id !== (int) $dados['supervisor_id'] &&
            $estagio->presencas()->where('modo', 'oficial')->exists()
        ) {
            throw ValidationException::withMessages([
                'supervisor_id' => 'O supervisor não pode ser trocado depois do envio de uma presença oficial.',
            ]);
        }

        $estagio->update(['supervisor_id' => $dados['supervisor_id']]);

        return redirect()->route('estagios.show', $estagio)
            ->with('success', 'Supervisor responsável vinculado ao estágio.');
    }

    public function finalizar(Request $request, int $estagioId)
    {
        $orientador = $this->estagioService->orientadorAtual($request);
        $estagio = $orientador->estagios()->findOrFail($estagioId);

        if ($this->estagioService->statusApresentacao($estagio) !== Estagio::STATUS_ATIVO) {
            throw ValidationException::withMessages([
                'status' => 'Somente um estágio ativo pode ser finalizado.',
            ]);
        }

        $estagio->update(['status' => Estagio::STATUS_FINALIZADO]);

        return redirect()->route('estagios.show', $estagio)
            ->with('success', 'Estágio finalizado. O histórico foi preservado.');
    }
}