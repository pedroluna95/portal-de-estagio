<?php

namespace App\Console\Commands;

use App\Models\EmpresasConveniadas;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SalvarBaseEmpresasSeederCommand extends Command
{
    /**
     * O nome e assinatura do comando no console.
     */
    protected $signature = 'empresas:salvar-seeder
                            {--arquivo= : Caminho customizado para o JSON do seeder}';

    /**
     * A descrição do comando.
     */
    protected $description = 'Salva os registros atuais da tabela empresas_conveniadas (com suas localizações) no arquivo JSON do Seeder.';

    public function handle(): int
    {
        $destino = $this->option('arquivo') ?: database_path('seeders/data/empresas_conveniadas.json');

        File::ensureDirectoryExists(dirname($destino));

        $empresas = EmpresasConveniadas::orderBy('empresa', 'asc')->get();
        $total = $empresas->count();

        if ($total === 0) {
            $this->warn('Nenhuma empresa encontrada no banco de dados para exportar.');
            return Command::FAILURE;
        }

        $dados = [];
        $comLocalizacao = 0;

        foreach ($empresas as $emp) {
            if ($emp->localizacao_verificada) {
                $comLocalizacao++;
            }

            $dados[] = [
                'empresa'                  => $emp->empresa,
                'cnpj'                     => $emp->cnpj,
                'vig_inicial'              => $emp->vig_inicial ? $emp->vig_inicial->format('Y-m-d') : null,
                'vig_final'                => $emp->vig_final ? $emp->vig_final->format('Y-m-d') : null,
                'municipio'                => $emp->municipio,
                'uf'                       => $emp->uf,
                'cep'                      => $emp->cep,
                'logradouro'               => $emp->logradouro,
                'numero'                   => $emp->numero,
                'complemento'              => $emp->complemento,
                'bairro'                   => $emp->bairro,
                'natureza_juridica'        => $emp->natureza_juridica,
                'localizacao_verificada'   => (bool) $emp->localizacao_verificada,
                'localizacao_verificada_em'=> $emp->localizacao_verificada_em ? $emp->localizacao_verificada_em->toDateTimeString() : null,
            ];
        }

        File::put($destino, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("Base de empresas salva com sucesso!");
        $this->line(" -> Arquivo: {$destino}");
        $this->line(" -> Total de empresas: {$total}");
        $this->line(" -> Com localização verificada: {$comLocalizacao}");
        $this->line(" -> Pendentes: " . ($total - $comLocalizacao));

        return Command::SUCCESS;
    }
}
