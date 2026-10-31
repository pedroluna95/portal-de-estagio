<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TablesController
{
    private const TABLES = [
        'usuarios' => ['user_ID', 'Nome', 'Email', 'atribuicao_id', 'cpf', 'created_at', 'updated_at'],
    ];

    public function listTables()
    {
        return response()->json(array_keys(self::TABLES));
    }

    public function rows(Request $request, string $table)
    {
        if (! array_key_exists($table, self::TABLES)) return response()->json(['message' => 'Tabela não autorizada.'], 404);

        try {
            $limit = min(max((int) $request->query('limit', 100), 1), 1000);
            return response()->json(DB::table($table)->select(self::TABLES[$table])->limit($limit)->get());
        } catch (\Throwable $e) {
            $requestId = (string) str()->uuid();
            Log::error('Falha ao consultar tabela administrativa', [
                'request_id' => $requestId,
                'table' => $table,
                'exception' => $e,
            ]);

            return response()->json([
                'message' => "Não foi possível consultar esta tabela agora. Código de atendimento: {$requestId}",
                'request_id' => $requestId,
            ], 500);
        }
    }
}
