<?php

namespace App\Console\Commands;

use App\Models\EmpresasConveniadas;
use App\Services\CnpjEnriquecimentoService;
use Illuminate\Console\Command;

class EnriquecerEmpresasCnpjCommand extends Command
{
    /**
     * O nome e assinatura do comando no console.
     */
    protected $signature = 'empresas:enriquecer
                            {--limite=50 : Quantidade máxima de empresas para consultar neste lote}
                            {--tudo : Continua processando em lotes até que todas as empresas estejam verificadas}
                            {--id= : ID específico de uma empresa para verificar}
                            {--cnpj= : CNPJ específico para verificar}';

    /**
     * A descrição do comando.
     */
    protected $description = 'Enriquece os dados de empresas conveniadas com localização (município, UF, endereço) via BrasilAPI.';

    public function handle(CnpjEnriquecimentoService $service): int
    {
        $id = $this->option('id');
        $cnpj = $this->option('cnpj');

        if ($id) {
            $empresa = EmpresasConveniadas::find($id);
            if (! $empresa) {
                $this->error("Empresa com ID {$id} não encontrada.");
                return Command::FAILURE;
            }
            return $this->processarUnica($service, $empresa);
        }

        if ($cnpj) {
            $cnpjLimpo = preg_replace('/\D/', '', $cnpj);
            $empresa = EmpresasConveniadas::where('cnpj', 'like', "%{$cnpjLimpo}%")->first();
            if (! $empresa) {
                $this->error("Empresa com CNPJ {$cnpj} não encontrada.");
                return Command::FAILURE;
            }
            return $this->processarUnica($service, $empresa);
        }

        $totalPendentes = EmpresasConveniadas::where('localizacao_verificada', false)->count();
        $this->info("Total de empresas pendentes de verificação: {$totalPendentes}");

        if ($totalPendentes === 0) {
            $this->info("Todas as empresas já possuem localização verificada!");
            return Command::SUCCESS;
        }

        $processarTudo = (bool) $this->option('tudo');
        $limite = (int) $this->option('limite');

        if (! $processarTudo) {
            $this->info("Processando lote de até {$limite} empresas...");
            $resultado = $service->enriquecerLote($limite);

            $this->table(
                ['Consultadas', 'Sucessos', 'Falhas', 'Restantes'],
                [[$resultado['processadas'], $resultado['sucessos'], $resultado['falhas'], $resultado['pendentes']]]
            );

            if (! empty($resultado['erros'])) {
                $this->warn("Amostra de erros encontrados:");
                foreach ($resultado['erros'] as $err) {
                    $this->line(" - {$err}");
                }
            }

            return Command::SUCCESS;
        }

        $this->info("Processamento contínuo ativado (rate limit de ~3 req/s respeitado).");
        $loteNum = 1;

        while (true) {
            $pendentes = EmpresasConveniadas::where('localizacao_verificada', false)->count();
            if ($pendentes === 0) {
                $this->info("Todas as empresas foram processadas com sucesso!");
                break;
            }

            $this->line("Iniciando lote #{$loteNum} (restam {$pendentes} pendentes)...");
            $resultado = $service->enriquecerLote($limite);

            $this->line("  -> Lote #{$loteNum}: {$resultado['sucessos']} sucessos, {$resultado['falhas']} falhas.");

            if ($resultado['processadas'] === 0) {
                break;
            }

            $loteNum++;
            sleep(1);
        }

        return Command::SUCCESS;
    }

    private function processarUnica(CnpjEnriquecimentoService $service, EmpresasConveniadas $empresa): int
    {
        $this->info("Consultando: {$empresa->empresa} ({$empresa->cnpj})...");
        $resultado = $service->enriquecerEmpresa($empresa);

        if ($resultado['sucesso']) {
            $empresa->refresh();
            $this->info("Localização obtida com sucesso!");
            $this->line(" - Município/UF: {$empresa->municipio} / {$empresa->uf}");
            $this->line(" - Logradouro: {$empresa->logradouro}, {$empresa->numero}");
            $this->line(" - Bairro/CEP: {$empresa->bairro} - {$empresa->cep}");
            $this->line(" - Natureza Jurídica: {$empresa->natureza_juridica}");
            return Command::SUCCESS;
        }

        $this->error("Falha ao consultar localização: {$resultado['mensagem']}");
        return Command::FAILURE;
    }
}
