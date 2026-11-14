<?php

namespace App\Services;

use App\Models\Aluno;
use App\Models\EmpresasConveniadas;
use App\Models\Estagio;
use App\Models\Orientador;
use App\Models\Presenca;
use App\Models\Supervisor;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardService
{
    /**
     * Retorna os dados completos necessários para o painel administrativo.
     */
    public function getDashboardData(Request $request): array
    {
        $abaAtiva = $request->query('aba', 'visao-geral');

        $contadores = [
            'total_usuarios'     => Usuario::count(),
            'total_alunos'       => Aluno::count(),
            'total_orientadores' => Orientador::count(),
            'total_supervisores' => Supervisor::count(),
            'total_empresas'     => EmpresasConveniadas::count(),
            'total_estagios'     => Estagio::count(),
            'estagios_ativos'    => Estagio::where('status', Estagio::STATUS_ATIVO)->count(),
            'presencas_totais'   => Presenca::count(),
        ];

        // UFs disponíveis para filtros
        $ufsDisponiveis = EmpresasConveniadas::whereNotNull('uf')
            ->distinct()
            ->orderBy('uf')
            ->pluck('uf');

        // Cursos disponíveis
        $cursosDisponiveis = ['Informatica', 'Automacao Industrial', 'Telecomunicações', 'Enfermagem'];

        $dadosAba = [];

        switch ($abaAtiva) {
            case 'usuarios':
                $dadosAba['usuarios'] = $this->queryUsuarios($request);
                break;

            case 'alunos':
                $dadosAba['alunos'] = $this->queryAlunos($request);
                break;

            case 'orientadores':
                $dadosAba['orientadores'] = $this->queryOrientadores($request);
                break;

            case 'supervisores':
                $dadosAba['supervisores'] = $this->querySupervisores($request);
                break;

            case 'empresas':
                $dadosAba['empresas'] = $this->queryEmpresas($request);
                break;

            case 'estagios':
                $dadosAba['estagios'] = $this->queryEstagios($request);
                break;

            case 'visao-geral':
            default:
                $dadosAba['ultimos_usuarios'] = Usuario::orderByDesc('user_ID')->limit(5)->get();
                $dadosAba['ultimos_estagios'] = Estagio::with(['aluno', 'orientador.usuario'])->orderByDesc('created_at')->limit(5)->get();
                break;
        }

        return array_merge([
            'abaAtiva'          => $abaAtiva,
            'contadores'        => $contadores,
            'ufsDisponiveis'    => $ufsDisponiveis,
            'cursosDisponiveis' => $cursosDisponiveis,
            'total_usuarios'    => $contadores['total_usuarios'],
            'total_empresas'    => $contadores['total_empresas'],
        ], $dadosAba);
    }

    public function queryUsuarios(Request $request)
    {
        $q      = trim((string) $request->query('q', ''));
        $tipo   = strtolower(trim((string) $request->query('tipo', '')));
        $status = $request->query('status');

        $query = Usuario::query();

        // Filtro por termo de busca usando when()
        $query->when(! empty($q), function ($query) use ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('Nome', 'like', "%{$q}%")
                    ->orWhere('Email', 'like', "%{$q}%")
                    ->orWhere('cpf', 'like', "%{$q}%")
                    ->orWhereIn('user_ID', DB::table('alunos')->select('user_id')->where('matricula', 'like', "%{$q}%"))
                    ->orWhereIn('user_ID', DB::table('orientadores')->select('user_ID')->where('siape', 'like', "%{$q}%"));

                $cpfDigits = preg_replace('/\D/', '', $q);
                if ($cpfDigits !== '') {
                    $sub->orWhereRaw(
                        "REPLACE(REPLACE(REPLACE(cpf, '.', ''), '-', ''), ' ', '') LIKE ?",
                        ["%{$cpfDigits}%"]
                    );
                }
            });
        });

        // Filtro por tipo/atribuição usando when()
        $query->when(! empty($tipo), function ($query) use ($tipo) {
            $atribuicaoId = DB::table('atribuicoes')
                ->whereRaw('LOWER(Nome_atribuicao) = ?', [$tipo])
                ->value('Atribuicao_ID');

            if ($atribuicaoId) {
                $query->where('atribuicao_id', $atribuicaoId);
            }
        });

        // Filtro por status ativo/inativo usando when()
        $query->when($status !== null && $status !== '', function ($query) use ($status) {
            $query->where('ativo', $status === 'ativo' ? 1 : 0);
        });

        return $query->orderByDesc('user_ID')->paginate(10)->withQueryString();
    }

    public function queryAlunos(Request $request)
    {
        $q      = trim((string) $request->query('q', ''));
        $curso  = $request->query('curso');
        $status = $request->query('status');

        $query = Aluno::with(['usuario', 'estagios']);

        $query->when(! empty($q), function ($query) use ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('nome', 'like', "%{$q}%")
                    ->orWhere('matricula', 'like', "%{$q}%")
                    ->orWhereHas('usuario', fn ($u) => $u->where('Email', 'like', "%{$q}%")->orWhere('cpf', 'like', "%{$q}%"));
            });
        });

        $query->when(! empty($curso), function ($query) use ($curso) {
            $query->where('curso', $curso);
        });

        $query->when($status !== null && $status !== '', function ($query) use ($status) {
            $query->whereHas('usuario', fn ($u) => $u->where('ativo', $status === 'ativo' ? 1 : 0));
        });

        return $query->orderBy('nome')->paginate(10)->withQueryString();
    }

    public function queryOrientadores(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $query = Orientador::with(['usuario', 'estagios']);

        $query->when(! empty($q), function ($query) use ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('siape', 'like', "%{$q}%")
                    ->orWhereHas('usuario', fn ($u) => $u->where('Nome', 'like', "%{$q}%")->orWhere('Email', 'like', "%{$q}%"));
            });
        });

        return $query->paginate(10)->withQueryString();
    }

    public function querySupervisores(Request $request)
    {
        $q       = trim((string) $request->query('q', ''));
        $empresa = trim((string) $request->query('empresa', ''));

        $query = Supervisor::with(['usuario', 'estagios']);

        $query->when(! empty($q), function ($query) use ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('empresa', 'like', "%{$q}%")
                    ->orWhere('cargo', 'like', "%{$q}%")
                    ->orWhereHas('usuario', fn ($u) => $u->where('Nome', 'like', "%{$q}%")->orWhere('Email', 'like', "%{$q}%"));
            });
        });

        $query->when(! empty($empresa), function ($query) use ($empresa) {
            $query->where('empresa', 'like', "%{$empresa}%");
        });

        return $query->paginate(10)->withQueryString();
    }

    public function queryEmpresas(Request $request)
    {
        $q      = trim((string) $request->query('q', ''));
        $status = $request->query('status');
        $uf     = $request->query('uf');

        $query = EmpresasConveniadas::query();

        $query->when(! empty($q), fn ($query) => $query->busca($q));

        $query->when($status === 'vigente', fn ($query) => $query->vigentes());
        $query->when($status === 'expirado', fn ($query) => $query->expirados());

        $query->when(! empty($uf), fn ($query) => $query->porUf($uf));

        return $query->orderBy('empresa', 'asc')->paginate(10)->withQueryString();
    }

    public function queryEstagios(Request $request)
    {
        $q      = trim((string) $request->query('q', ''));
        $status = $request->query('status');

        $query = Estagio::with(['aluno', 'orientador.usuario', 'supervisor.usuario']);

        $query->when(! empty($q), function ($query) use ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('local', 'like', "%{$q}%")
                    ->orWhereHas('aluno', fn ($a) => $a->where('nome', 'like', "%{$q}%"));
            });
        });

        $query->when(! empty($status), fn ($query) => $query->where('status', $status));

        return $query->orderByDesc('created_at')->paginate(10)->withQueryString();
    }
}
