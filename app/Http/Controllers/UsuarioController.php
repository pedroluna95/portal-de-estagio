<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Aluno;
use App\Models\Orientador;
use App\Models\Supervisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use App\Rules\ValidarCpf;


class UsuarioController
{
    private const TIPOS = ['aluno', 'orientador', 'supervisor'];

    public function index(Request $request)
    {
        try {
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

            return response()->json($query->orderByDesc('user_ID')->get());
        } catch (\Throwable $e) {
            return $this->error('Não foi possível carregar a lista de usuários.', 500, $e);
        }
    }

    public function show($id)
    {
        try {
            $usuario = Usuario::find($id);

            return $usuario
                ? response()->json($usuario)
                : $this->error('Usuário não encontrado.', 404);
        } catch (\Throwable $e) {
            return $this->error('Não foi possível carregar os dados do usuário.', 500, $e);
        }
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
                            'user_id' => $usuario->user_ID,
                            'nome' => $usuario->Nome,
                            'matricula' => $data['matricula'],
                            'curso' => $data['curso'],
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

                }

                return $usuario;
            });

            return response()->json(
                $usuario->fresh(),
                201
            );

        } catch (\Throwable $e) {
            return $this->error(
                'Não foi possível cadastrar o usuário.',
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
            'cpf' => ['sometimes','nullable','string',new ValidarCpf,Rule::unique('usuarios', 'cpf')->ignore($usuario->user_ID, 'user_ID')],
            'tipo' => ['sometimes','required', Rule::in(self::TIPOS)],
            'matricula' => ['sometimes','nullable','string','max:30', Rule::unique('alunos', 'matricula')->ignore($usuario->user_ID, 'user_id')],
            'curso' => ['sometimes','required_if:tipo,aluno','nullable','string',Rule::in(['Informatica', 'Automacao Industrial', 'Telecomunicações', 'Enfermagem'])],
            'siape' => ['sometimes','nullable','digits:8', Rule::unique('orientadores', 'siape')->ignore($usuario->user_ID, 'user_ID')],
        ], [
            'nome.required' => 'Informe o nome do usuário.',
            'nome.max' => 'O nome deve ter no máximo 60 caracteres.',
            'email.required' => 'Informe o e-mail do usuário.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'senha.min' => 'A senha deve ter pelo menos 6 caracteres.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'tipo.in' => 'Selecione um tipo de usuário válido.',
            'matricula.required_if' => 'Informe a matrícula do aluno.',
            'curso.required_if' => 'Selecione o curso do aluno.',
            'curso.in' => 'Selecione um dos cursos disponíveis.',
            'siape.digits' => 'O SIAPE deve conter exatamente 8 dígitos.',
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
            $tipoNovo === 'aluno' &&
            array_key_exists('tipo', $data) &&
            blank($data['curso'] ?? null)
        ) {
            return $this->error(
                'Selecione o curso do aluno.',
                422,
                null,
                ['curso' => ['Selecione um dos cursos disponíveis.']]
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
                    $alunoExistente = Aluno::where('user_id', $usuario->user_ID)->first();
                    Aluno::updateOrCreate(
                        ['user_id' => $usuario->user_ID],
                        [
                            'nome' => $usuario->Nome,
                            'matricula' => $data['matricula'] ?? $alunoExistente?->getRawOriginal('matricula'),
                            'curso' => $data['curso'] ?? $alunoExistente?->getRawOriginal('curso'),
                        ]
                    );

                    Orientador::where('user_ID', $usuario->user_ID)->delete();
                    Supervisor::where('user_ID', $usuario->user_ID)->delete();
                }
                elseif ($tipoNovo === 'orientador') {
                    Orientador::updateOrCreate(
                        ['user_ID' => $usuario->user_ID],
                        ['siape' => $data['siape'] ?? null]
                    );

                    Aluno::where('user_id', $usuario->user_ID)->delete();
                    Supervisor::where('user_ID', $usuario->user_ID)->delete();
                }
                elseif ($tipoNovo === 'supervisor') {
                    Supervisor::updateOrCreate(['user_ID' => $usuario->user_ID]);
                    Aluno::where('user_id', $usuario->user_ID)->delete();
                    Orientador::where('user_ID', $usuario->user_ID)->delete();
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
        try {
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
        } catch (\Throwable $e) {
            return $this->error('Não foi possível excluir o usuário.', 500, $e);
        }
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
                'unique:usuarios,cpf',
                new ValidarCpf,
            ],


            'tipo' => [
                'required',
                Rule::in(self::TIPOS)
            ],

            'matricula' => [
                'nullable',
                'required_if:tipo,aluno',
                'string',
                'max:30',
                Rule::unique('alunos', 'matricula')
            ],

            'curso' => [
                'nullable',
                'required_if:tipo,aluno',
                'string',
                Rule::in(['Informatica', 'Automacao Industrial', 'Telecomunicações', 'Enfermagem'])
            ],

            'siape' => [
                'nullable',
                'required_if:tipo,orientador',
                'digits:8',
                Rule::unique('orientadores', 'siape')
            ],
        ], [
            'nome.required' => 'Informe o nome do usuário.',
            'nome.max' => 'O nome deve ter no máximo 60 caracteres.',
            'email.required' => 'Informe o e-mail do usuário.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'senha.required' => 'Informe uma senha.',
            'senha.min' => 'A senha deve ter pelo menos 6 caracteres.',
            'cpf.required' => 'Informe o CPF do usuário.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'tipo.in' => 'Selecione um tipo de usuário válido.',
            'matricula.required_if' => 'Informe a matrícula do aluno.',
            'matricula.max' => 'A matrícula deve ter no máximo 30 caracteres.',
            'matricula.unique' => 'Esta matrícula já está cadastrada.',
            'curso.required_if' => 'Selecione o curso do aluno.',
            'curso.in' => 'Selecione um dos cursos disponíveis.',
            'siape.required_if' => 'Informe o SIAPE do orientador.',
            'siape.digits' => 'O SIAPE deve conter exatamente 8 dígitos.',
            'siape.unique' => 'Este SIAPE já está cadastrado.',
        ]);

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
            $message = $this->describeException($message, $exception);
            Log::error($message, [
                'request_id' => $requestId,
                'exception' => $exception
            ]);
            $message .= " Tente novamente. Código de atendimento: {$requestId}";
        }

        return response()->json([
            'message' => $message,
            'errors' => $errors,
            'request_id' => $requestId
        ], $status);
    }

    private function describeException(string $fallback, \Throwable $exception): string
    {
        if (! $exception instanceof QueryException) {
            return $fallback;
        }

        return match ((int) ($exception->errorInfo[1] ?? 0)) {
            1062 => 'Já existe um cadastro usando este e-mail, CPF, matrícula ou SIAPE.',
            1048, 1364 => 'Faltam dados obrigatórios para o tipo de usuário selecionado.',
            1452 => 'Não foi possível vincular o usuário porque uma referência relacionada não existe.',
            2002, 2006, 2013 => 'O banco de dados está indisponível no momento.',
            default => $fallback,
        };
    }
}
