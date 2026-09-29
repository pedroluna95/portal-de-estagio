<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TablesController
{
    private const TABLES = [
        'usuarios' => ['user_ID', 'Nome', 'Email', 'atribuicao_id', 'cpf', 'matricula', 'siape'],
        'vagas' => ['id', 'titulo', 'descricao'],
    ];

    public function listTables()
    {
        return response()->json(array_keys(self::TABLES));
    }

    public function rows(Request $request, string $table)
    {
        if (! array_key_exists($table, self::TABLES)) return response()->json(['message' => 'Tabela não autorizada.'], 404);
        $limit = min(max((int) $request->query('limit', 100), 1), 1000);
        return response()->json(DB::table($table)->select(self::TABLES[$table])->limit($limit)->get());
    }
}
