<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataController extends Controller
{
    public function destroy(Request $request)
    {
        $request->validate([
            'confirmacao' => ['required', 'in:APAGAR TODOS OS DADOS'],
        ], [
            'confirmacao.required' => 'Digite a confirmação para apagar os dados.',
            'confirmacao.in' => 'A confirmação informada não corresponde ao texto exigido.',
        ]);

        try {
            $counts = DB::transaction(function () use ($request) {
                $adminId = (int) $request->session()->get('usuario_id');
                $admin = Usuario::query()->lockForUpdate()->findOrFail($adminId);

                if (strtolower((string) $admin->tipo) !== 'administrador') {
                    abort(403, 'Apenas um administrador pode limpar os dados do portal.');
                }

                $counts = [];
                foreach ([
                    'presencas',
                    'jornadas_estagio',
                    'estagios',
                    'alunos',
                    'orientadores',
                    'supervisores',
                    'empresas_conveniadas',
                ] as $table) {
                    $counts[$table] = DB::table($table)->delete();
                }

                $counts['usuarios'] = DB::table('usuarios')
                    ->where('user_ID', '<>', $adminId)
                    ->delete();

                return $counts;
            });

            return response()->json([
                'message' => 'Os dados do portal foram apagados. O login do administrador foi preservado.',
                'apagados' => $counts,
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            $requestId = (string) str()->uuid();
            Log::error('Falha ao limpar os dados do portal.', [
                'request_id' => $requestId,
                'exception' => $exception,
            ]);

            return response()->json([
                'message' => "Não foi possível limpar os dados do portal. Código de atendimento: {$requestId}",
                'request_id' => $requestId,
            ], 500);
        }
    }
}
