<?php

namespace App\Http\Controllers;

use App\Models\EmpresasConveniadas;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    /**
     * Exibe a listagem pública/interna de empresas conveniadas com filtros ativos.
     */
    public function index(Request $request)
    {
        $busca  = trim((string) $request->query('busca', ''));
        $status = $request->query('status');
        $ordem  = $request->query('ordem', 'nome_asc');
        $uf     = $request->query('uf');

        $query = EmpresasConveniadas::query();

        if (! empty($busca)) {
            $query->busca($busca);
        }

        if ($status === 'vigente') {
            $query->vigentes();
        } elseif ($status === 'expirado') {
            $query->expirados();
        }

        if (! empty($uf)) {
            $query->porUf($uf);
        }

        if ($ordem === 'nome_desc') {
            $query->orderBy('empresa', 'desc');
        } elseif ($ordem === 'vig_fim_asc') {
            $query->orderBy('vig_final', 'asc');
        } elseif ($ordem === 'recentes') {
            $query->orderBy('id', 'desc');
        } else {
            $query->orderBy('empresa', 'asc');
        }

        $empresas = $query->paginate(18)->withQueryString();

        $stats = [
            'total'    => EmpresasConveniadas::count(),
            'vigentes' => EmpresasConveniadas::vigentes()->count(),
            'expiradas'=> EmpresasConveniadas::expirados()->count(),
        ];

        // UFs disponíveis para filtro
        $ufsDisponiveis = EmpresasConveniadas::whereNotNull('uf')
            ->distinct()
            ->orderBy('uf')
            ->pluck('uf');

        return view('empresas', [
            'empresas'       => $empresas,
            'stats'          => $stats,
            'busca'          => $busca,
            'status'         => $status,
            'ordem'          => $ordem,
            'uf'             => $uf,
            'ufsDisponiveis' => $ufsDisponiveis,
        ]);
    }
}
