<?php

namespace App\Services;

use App\Models\Aluno;
use App\Models\Orientador;
use App\Models\Supervisor;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioService
{
    public static function create(array $data): Usuario
    {
        $tipo = strtolower((string) ($data['tipo'] ?? $data['atribuicao'] ?? ''));

        $atribuicaoId = DB::table('atribuicoes')
            ->whereRaw('LOWER(Nome_atribuicao) = ?', [$tipo])
            ->value('Atribuicao_ID');

        if (! $atribuicaoId) {
            throw new \InvalidArgumentException('Tipo de usuário inválido: ' . $tipo);
        }

        return DB::transaction(function () use ($data, $tipo, $atribuicaoId) {
            $usuario = Usuario::create([
                'Nome' => $data['nome'] ?? $data['Nome'] ?? null,
                'Email' => $data['email'] ?? $data['Email'] ?? null,
                'Senha' => Hash::make($data['senha'] ?? $data['Senha']),
                'cpf' => self::formatCpf($data['cpf'] ?? null),
                'atribuicao_id' => $atribuicaoId,
            ]);

            switch ($tipo) {
                case 'aluno':
                    Aluno::create([
                        'user_id' => $usuario->user_ID,
                        'nome' => $usuario->Nome,
                        'curso' => $data['curso'] ?? null,
                        'matricula' => $data['matricula'] ?? null,
                        'orientador' => $data['orientador'] ?? null,
                    ]);
                    break;

                case 'orientador':
                    Orientador::create([
                        'user_ID' => $usuario->user_ID,
                        'siape' => $data['siape'] ?? null,
                    ]);
                    break;

                case 'supervisor':
                    Supervisor::create([
                        'user_ID' => $usuario->user_ID,
                        'cargo' => $data['cargo'] ?? null,
                        'empresa' => $data['empresa'] ?? null,
                    ]);
                    break;

            }

            return $usuario;
        });
    }

    public static function formatCpf($value): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $value);
        if ($digits === '' || ! self::isValidCpf($digits)) {
            return null;
        }

        return substr($digits, 0, 3) . '.' . substr($digits, 3, 3) . '.' . substr($digits, 6, 3) . '-' . substr($digits, 9, 2);
    }

    public static function isValidCpf($value): bool
    {
        $digits = preg_replace('/\D/', '', (string) $value);

        if (strlen($digits) !== 11 || preg_match('/^(\d)\1{10}$/', $digits)) {
            return false;
        }

        for ($length = 9; $length <= 10; $length++) {
            $sum = 0;
            for ($i = 0; $i < $length; $i++) {
                $sum += (int) $digits[$i] * (($length + 1) - $i);
            }

            $check = ($sum * 10) % 11;
            if ($check === 10) {
                $check = 0;
            }

            if ($check !== (int) $digits[$length]) {
                return false;
            }
        }

        return true;
    }
}
