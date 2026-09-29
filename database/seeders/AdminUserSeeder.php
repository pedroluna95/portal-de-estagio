<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminAtribuicaoId = DB::table('atribuicoes')
            ->whereRaw('LOWER(Nome_atribuicao) = ?', ['administrador'])
            ->value('Atribuicao_ID');

        if (! $adminAtribuicaoId) {
            throw new \RuntimeException('Atribuição Administrador não encontrada.');
        }

        Usuario::updateOrCreate(
            ['Email' => 'admin@admin.com'],
            [
                'Nome' => 'Administrador',
                'Senha' => Hash::make('cefet123'),
                'cpf' => '000.000.000-00',
                'atribuicao_id' => $adminAtribuicaoId,
            ]
        );
    }
}
