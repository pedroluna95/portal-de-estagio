<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Aluno;
use App\Models\Orientador;
use App\Models\Supervisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController
{
    private const TIPOS = ['aluno', 'orientador', 'supervisor', 'contratante'];

    public function index(Request $request)
    {
        $query = Usuario::query();

        if ($tipo = strtolower(trim((string) $request->query('tipo')))) {
            $atribuicaoId = DB::table('atribuicoes')
                ->whereRaw('LOWER(Nome_atribuicao) = ?', [$tipo])
                ->value('Atribuicao_ID');

            if ($atribuicaoId) {
                $query->where('atribuicao_id', $atribuicaoId);
            }
        }

        if ($q = trim((string) $request->query('q'))) {
            $query->where(function ($qb) use ($q) {
                $qb->where('Nome', 'like', "%{$q}%")
                    ->orWhere('Email', 'like', "%{$q}%")
                    ->orWhere('cpf', 'like', "%{$q}%");
            });
        }

        return response()->json(
            $query->orderByDesc('user_ID')->get()
        );
    }

    public function show($id)
    {
        $usuario = Usuario::find($id);

        return $usuario
            ? response()->json($usuario)
            : $this->error('Usuário não encontrado.', 404);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        try {
            $usuario = DB::transaction(function () use ($data) {

                $atribuicaoId = DB::table('atribuicoes')
                    ->whereRaw(
                        'LOWER(Nome_atribuicao) = ?',
                        [$data['tipo']]
                    )
                    ->value('Atribuicao_ID');

                if (! $atribuicaoId) {
                    throw new \Exception(
                        'Atribuição não encontrada: ' . $data['tipo']
                    );
                }

                $usuario = Usuario::create([
                    'cpf' => $data['cpf'],
                    'Nome' => $data['nome'],
                    'Email' => $data['email'],
                    'Senha' => Hash::make($data['senha']),
                    'atribuicao_id' => $atribuicaoId,
                ]);

                switch ($data['tipo']) {
                    case 'aluno':
                        Aluno::create([
                            'user_ID' => $usuario->user_ID,
                            'matricula' => $data['matricula'],
                        ]);
                        break;

                    case 'orientador':
                        Orientador::create([
                            'user_ID' => $usuario->user_ID,
                            'siape' => $data['siape'],
                        ]);
                        break;

                    case 'supervisor':
                        Supervisor::create([
                            'user_ID' => $usuario->user_ID,
                        ]);
                        break;

                    case 'contratante':
                        \App\Models\Contratante::create([
                            'user_ID' => $usuario->user_ID,
                        ]);
                        break;
                }

                return $usuario;
            });

            return response()->json(
                $usuario->fresh(),
                201
            );

        } catch (\Throwable $e) {
            return $this->error(
                'Não foi possível salvar o usuário.',
                500,
                $e
            );
        }
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::find($id);

        if (! $usuario) {
            return $this->error(
                'Usuário não encontrado.',
                404
            );
        }

        $tipoAtual = DB::table('atribuicoes')
            ->where('Atribuicao_ID', $usuario->atribuicao_id)
            ->value('Nome_atribuicao');

        if (strtolower((string) $tipoAtual) === 'administrador') {
            return $this->error(
                'A conta administrativa não pode ser editada por esta tela.',
                422
            );
        }

        $data = $request->validate([
            'nome' => ['sometimes','required','string','max:60'],
            'email' => ['sometimes','required','email','max:45', Rule::unique('usuarios','Email')->ignore($usuario->user_ID,'user_ID')],
            'senha' => ['sometimes','nullable','string','min:6'],
            'cpf' => ['sometimes','nullable','string','cpf_valido'],
            'tipo' => ['sometimes','required', Rule::in(self::TIPOS)],
            'matricula' => ['sometimes','nullable','string','max:60'],
            'siape' => ['sometimes','nullable','digits:8'],
        ]);

        $tipoNovo = strtolower((string) ($data['tipo'] ?? $tipoAtual));

        if (
            $tipoNovo === 'aluno' &&
            array_key_exists('tipo', $data) &&
            blank($data['matricula'] ?? null)
        ) {
            return $this->error(
                'Matrícula é obrigatória para alunos.',
                422,
                null,
                ['matricula' => ['Informe a matrícula.']]
            );
        }

        if (
            $tipoNovo === 'orientador' &&
            array_key_exists('tipo', $data) &&
            blank($data['siape'] ?? null)
        ) {
            return $this->error(
                'SIAPE é obrigatório para orientadores.',
                422,
                null,
                ['siape' => ['Informe o SIAPE de 8 dígitos.']]
            );
        }

        try {
            DB::transaction(function () use ($usuario, $data, $tipoNovo, $tipoAtual) {
                if (array_key_exists('nome', $data)) {
                    $usuario->Nome = $data['nome'];
                }

                if (array_key_exists('email', $data)) {
                    $usuario->Email = $data['email'];
                }

                if (array_key_exists('cpf', $data)) {
                    $usuario->cpf = $data['cpf'];
                }

                if (! empty($data['senha'])) {
                    $usuario->Senha = Hash::make($data['senha']);
                }

                if (array_key_exists('tipo', $data)) {
                    $atribuicaoId = DB::table('atribuicoes')
                        ->whereRaw('LOWER(Nome_atribuicao) = ?', [$tipoNovo])
                        ->value('Atribuicao_ID');

                    if (! $atribuicaoId) {
                        throw new \Exception('Atribuição não encontrada.');
                    }

                    $usuario->atribuicao_id = $atribuicaoId;
                }

                $usuario->save();

                if ($tipoNovo === 'aluno') {
                    Aluno::updateOrCreate(
                        ['user_ID' => $usuario->user_ID],
                        ['matricula' => $data['matricula'] ?? null]
                    );

                    Orientador::where('user_ID', $usuario->user_ID)->delete();
                    Supervisor::where('user_ID', $usuario->user_ID)->delete();
                    \App\Models\Contratante::where('user_ID', $usuario->user_ID)->delete();
                }
                elseif ($tipoNovo === 'orientador') {
                    Orientador::updateOrCreate(
                        ['user_ID' => $usuario->user_ID],
                        ['siape' => $data['siape'] ?? null]
                    );

                    Aluno::where('user_ID', $usuario->user_ID)->delete();
                    Supervisor::where('user_ID', $usuario->user_ID)->delete();
                    \App\Models\Contratante::where('user_ID', $usuario->user_ID)->delete();
                }
                elseif ($tipoNovo === 'supervisor') {
                    Supervisor::updateOrCreate(['user_ID' => $usuario->user_ID]);
                    Aluno::where('user_ID', $usuario->user_ID)->delete();
                    Orientador::where('user_ID', $usuario->user_ID)->delete();
                    \App\Models\Contratante::where('user_ID', $usuario->user_ID)->delete();
                }
                elseif ($tipoNovo === 'contratante') {
                    \App\Models\Contratante::updateOrCreate(['user_ID' => $usuario->user_ID]);
                    Aluno::where('user_ID', $usuario->user_ID)->delete();
                    Orientador::where('user_ID', $usuario->user_ID)->delete();
                    Supervisor::where('user_ID', $usuario->user_ID)->delete();
                }
            });

            return response()->json($usuario->fresh());

        } catch (\Throwable $e) {
            return $this->error(
                'Não foi possível atualizar o usuário.',
                500,
                $e
            );
        }
    }

    public function destroy(Request $request, $id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return $this->error(
                'Usuário não encontrado.',
                404
            );
        }

        if (
            (int) $request->session()->get('usuario_id')
            === (int) $usuario->user_ID
        ) {
            return $this->error(
                'Não é permitido excluir a própria conta.',
                422
            );
        }

        /*
         * Verifica a atribuição pelo ID.
         */
        $tipo = DB::table('atribuicoes')
            ->where('Atribuicao_ID', $usuario->atribuicao_id)
            ->value('Nome_atribuicao');

        if ($tipo === 'administrador') {

            $totalAdmins = Usuario::whereIn(
                'atribuicao_id',
                DB::table('atribuicoes')
                    ->where('Nome_atribuicao', 'administrador')
                    ->pluck('Atribuicao_ID')
            )->count();

            if ($totalAdmins <= 1) {
                return $this->error(
                    'Não é permitido excluir o último administrador.',
                    422
                );
            }
        }

        $usuario->delete();

        return response()->json([
            'message' => 'Usuário excluído.'
        ]);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'nome' => [
                'required',
                'string',
                'max:60'
            ],

            'email' => [
                'required',
                'email',
                'max:45',
                'unique:usuarios,Email'
            ],

            'senha' => [
                'required',
                'string',
                'min:6'
            ],

            'cpf' => [
                'required',
                'string',
                'cpf_valido'
            ],

            'tipo' => [
                'required',
                Rule::in(self::TIPOS)
            ],

            'matricula' => [
                'nullable',
                'string',
                'max:60'
            ],

            'siape' => [
                'nullable',
                'digits:8'
            ],
        ]);

        if (
            $data['tipo'] === 'aluno'
            && blank($data['matricula'] ?? null)
        ) {
            $request->validate([
                'matricula' => [
                    'required',
                    'string'
                ]
            ]);
        }

        if (
            $data['tipo'] === 'orientador'
            && blank($data['siape'] ?? null)
        ) {
            $request->validate([
                'siape' => [
                    'required',
                    'digits:8'
                ]
            ]);
        }

        return $data;
    }

    private function error(
        string $message,
        int $status,
        ?\Throwable $exception = null,
        array $errors = []
    ) {
        $requestId = (string) str()->uuid();

        if ($exception) {
            \Log::error($message, [
                'request_id' => $requestId,
                'exception' => $exception
            ]);
        }

        return response()->json([
            'message' => $message,
            'errors' => $errors,
            'request_id' => $requestId
        ], $status);
    }
}
