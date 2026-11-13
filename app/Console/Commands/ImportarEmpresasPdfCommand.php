<?php

namespace App\Console\Commands;

use App\Services\EmpresaConveniadaImportService;
use Illuminate\Console\Command;

class ImportarEmpresasPdfCommand extends Command
{
    /**
     * O nome e assinatura do comando no console.
     */
    protected $signature = 'empresas:importar {pdf? : Caminho do arquivo PDF para importar} {--csv= : Caminho customizado para salvar o CSV intermediário}';

    /**
     * A descrição do comando.
     */
    protected $description = 'Extrai empresas conveniadas de um PDF oficial, gera o CSV intermediário e persiste na base de dados.';

    public function handle(EmpresaConveniadaImportService $service): int
    {
        $pdfPath = $this->argument('pdf');

        if (! $pdfPath) {
            $padrao = 'C:\\Projetos\\listagem-empresas\\data\\pdf\\empresas 06-11-26.pdf';
            if (file_exists($padrao)) {
                $pdfPath = $padrao;
                $this->info("Usando arquivo padrão localizado em: {$pdfPath}");
            } else {
                $pdfPath = $this->ask('Informe o caminho completo do arquivo PDF');
            }
        }

        if (! file_exists($pdfPath)) {
            $this->error("Arquivo não encontrado: {$pdfPath}");
            return Command::FAILURE;
        }

        $this->info("Iniciando extração do PDF: {$pdfPath}");
        $csvDestino = $this->option('csv');

        try {
            $this->output->write("1. Extraindo dados do PDF para CSV... ");
            $extracao = $service->extrairPdfParaCsv($pdfPath, $csvDestino);
            $this->info("Concluído! [{$extracao['total']} empresas extraídas]");
            $this->line("   -> Arquivo CSV gerado em: {$extracao['csv_path']}");

            $this->output->write("2. Persistindo registros no banco de dados (upsert)... ");
            $banco = $service->importarCsvParaBanco($extracao['csv_path']);
            $this->info("Concluído!");
            $this->line("   -> Total de registros lidos do CSV: {$banco['total_lidos']}");
            $this->line("   -> Total de empresas no banco agora: {$banco['total_no_banco']}");

            $this->newLine();
            $this->info("Importação finalizada com sucesso!");
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->newLine();
            $this->error("Erro durante a importação: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
