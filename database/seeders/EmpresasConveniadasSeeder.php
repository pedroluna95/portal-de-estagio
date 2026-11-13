<?php

namespace Database\Seeders;

use App\Models\EmpresasConveniadas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class EmpresasConveniadasSeeder extends Seeder
{
    /**
     * Popula a base com as empresas conveniadas e suas localizações pré-processadas.
     */
    public function run(): void
    {
        $jsonPath = database_path('seeders/data/empresas_conveniadas.json');

        if (! File::exists($jsonPath)) {
            $this->command?->warn("Arquivo de dados não encontrado: {$jsonPath}");
            return;
        }

        $dados = json_decode(File::get($jsonPath), true);
        if (! is_array($dados) || empty($dados)) {
            $this->command?->warn("O arquivo de dados de empresas está vazio ou inválido.");
            return;
        }

        $this->command?->info('Populando empresas conveniadas com base pré-enriquecida (' . count($dados) . ' registros)...');

        $agora = now();
        $chunks = array_chunk($dados, 200);

        foreach ($chunks as $chunk) {
            $registros = [];
            foreach ($chunk as $item) {
                $registros[] = [
                    'empresa'                  => mb_substr((string) ($item['empresa'] ?? ''), 0, 255),
                    'cnpj'                     => mb_substr((string) ($item['cnpj'] ?? ''), 0, 30),
                    'vig_inicial'              => ! empty($item['vig_inicial']) ? $item['vig_inicial'] : null,
                    'vig_final'                => ! empty($item['vig_final']) ? $item['vig_final'] : null,
                    'municipio'                => ! empty($item['municipio']) ? mb_substr((string) $item['municipio'], 0, 120) : null,
                    'uf'                       => ! empty($item['uf']) ? strtoupper(mb_substr((string) $item['uf'], 0, 2)) : null,
                    'cep'                      => ! empty($item['cep']) ? mb_substr((string) $item['cep'], 0, 9) : null,
                    'logradouro'               => ! empty($item['logradouro']) ? mb_substr((string) $item['logradouro'], 0, 255) : null,
                    'numero'                   => ! empty($item['numero']) ? mb_substr((string) $item['numero'], 0, 40) : null,
                    'complemento'              => ! empty($item['complemento']) ? mb_substr((string) $item['complemento'], 0, 120) : null,
                    'bairro'                   => ! empty($item['bairro']) ? mb_substr((string) $item['bairro'], 0, 120) : null,
                    'natureza_juridica'        => ! empty($item['natureza_juridica']) ? mb_substr((string) $item['natureza_juridica'], 0, 120) : null,
                    'localizacao_verificada'   => ! empty($item['localizacao_verificada']) ? 1 : 0,
                    'localizacao_verificada_em'=> ! empty($item['localizacao_verificada_em']) ? $item['localizacao_verificada_em'] : null,
                    'created_at'               => $agora,
                    'updated_at'               => $agora,
                ];
            }

            // Upsert para garantir que atualiza caso já exista ou insere se não existir
            EmpresasConveniadas::upsert(
                $registros,
                ['cnpj'],
                [
                    'empresa',
                    'vig_inicial',
                    'vig_final',
                    'municipio',
                    'uf',
                    'cep',
                    'logradouro',
                    'numero',
                    'complemento',
                    'bairro',
                    'natureza_juridica',
                    'localizacao_verificada',
                    'localizacao_verificada_em',
                    'updated_at',
                ]
            );
        }

        $this->command?->info('Empresas conveniadas populadas com sucesso! Total no banco: ' . EmpresasConveniadas::count());
    }
}
