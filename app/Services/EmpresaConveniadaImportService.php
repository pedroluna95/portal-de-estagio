<?php

namespace App\Services;

use App\Models\EmpresasConveniadas;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use RuntimeException;

class EmpresaConveniadaImportService
{
    /**
     * Localiza o executável Python compatível no ambiente.
     */
    public function resolvePythonBinary(): string
    {
        $candidatos = array_filter([
            config('services.python.binary'),
            env('PYTHON_BINARY'),
            'python3',
            'python',
            'C:\\Projetos\\listagem-empresas\\.venv\\Scripts\\python.exe',
            base_path('.venv/bin/python'),
            base_path('.venv/Scripts/python.exe'),
        ]);

        foreach ($candidatos as $binario) {
            if ($binario && (file_exists($binario) || $this->testBinaryExecutable($binario))) {
                return $binario;
            }
        }

        throw new RuntimeException(
            'Interpretador Python não encontrado. Certifique-se de configurar PYTHON_BINARY no arquivo .env ou instalar o Python com PyMuPDF.'
        );
    }

    private function testBinaryExecutable(string $bin): bool
    {
        try {
            $result = Process::run("{$bin} --version");
            return $result->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Executa a extração do PDF gerando o arquivo .csv intermediário.
     */
    public function extrairPdfParaCsv(string $pdfPath, ?string $csvDestino = null): array
    {
        if (! file_exists($pdfPath)) {
            throw new RuntimeException("Arquivo PDF não encontrado em: {$pdfPath}");
        }

        $pythonBin = $this->resolvePythonBinary();
        $scriptPath = base_path('scripts/extrair_empresas_pdf.py');

        if (! file_exists($scriptPath)) {
            throw new RuntimeException("Script de extração não encontrado em: {$scriptPath}");
        }

        if (! $csvDestino) {
            $dir = storage_path('app/imports/csv');
            File::ensureDirectoryExists($dir);
            $csvDestino = $dir . DIRECTORY_SEPARATOR . 'empresas_conveniadas_' . date('Ymd_His') . '.csv';
        } else {
            File::ensureDirectoryExists(dirname($csvDestino));
        }

        $comando = escapeshellarg($pythonBin) . ' '
            . escapeshellarg($scriptPath) . ' --pdf '
            . escapeshellarg($pdfPath) . ' --csv '
            . escapeshellarg($csvDestino);

        $process = Process::timeout(120)->run($comando);

        if (! $process->successful()) {
            Log::error('Falha na extração do PDF de empresas', [
                'output' => $process->output(),
                'error' => $process->errorOutput(),
            ]);
            throw new RuntimeException('Falha ao processar PDF: ' . ($process->errorOutput() ?: $process->output()));
        }

        $saidaJson = json_decode(trim($process->output()), true);

        if (! $saidaJson || ($saidaJson['status'] ?? '') !== 'success') {
            throw new RuntimeException('Erro reportado pelo extrator: ' . ($saidaJson['message'] ?? 'Resposta inválida'));
        }

        return [
            'total' => $saidaJson['total'] ?? 0,
            'csv_path' => $csvDestino,
            'csv_filename' => basename($csvDestino),
        ];
    }

    /**
     * Lê o arquivo .csv intermediário e persiste os registros na tabela empresas_conveniadas.
     */
    public function importarCsvParaBanco(string $csvPath): array
    {
        if (! file_exists($csvPath)) {
            throw new RuntimeException("Arquivo CSV não encontrado em: {$csvPath}");
        }

        $handle = fopen($csvPath, 'r');
        if (! $handle) {
            throw new RuntimeException("Não foi possível abrir o arquivo CSV para leitura.");
        }

        // Lê cabeçalho
        $header = fgetcsv($handle, 1000, ',');
        if (! $header) {
            fclose($handle);
            throw new RuntimeException("Arquivo CSV está vazio ou ilegível.");
        }

        $totalLidos = 0;
        $totalAdicionados = 0;
        $totalIgnorados = 0;
        $batch = [];
        $batchSize = 250;

        // Carrega todos os CNPJs já existentes no banco para filtrar e adicionar somente os novos
        $cnpjsExistentes = EmpresasConveniadas::pluck('cnpj')->all();
        $mapaExistentes = [];
        foreach ($cnpjsExistentes as $c) {
            $limpo = preg_replace('/\D/', '', (string) $c);
            $mapaExistentes[$limpo] = true;
            $mapaExistentes[(string) $c] = true;
        }

        $agora = now();

        while (($row = fgetcsv($handle, 2000, ',')) !== false) {
            if (empty($row) || count($row) < 2) {
                continue;
            }

            $empresa = trim($row[0] ?? '');
            $cnpj = trim($row[1] ?? '');
            $vigInicial = ! empty($row[2]) ? trim($row[2]) : null;
            $vigFinal = ! empty($row[3]) ? trim($row[3]) : null;

            if (empty($cnpj) || empty($empresa)) {
                continue;
            }

            $totalLidos++;

            // Filtra: se a empresa já existir no banco (ou já foi processada neste lote), ignora e não sobrescreve
            $cnpjLimpo = preg_replace('/\D/', '', $cnpj);
            if (isset($mapaExistentes[$cnpjLimpo]) || isset($mapaExistentes[$cnpj])) {
                $totalIgnorados++;
                continue;
            }

            // Normaliza formato e validação de calendário estrita para o MySQL (ex: 29/02/2029 -> 28/02/2029)
            $vigInicial = $this->sanitizeDate($vigInicial);
            $vigFinal = $this->sanitizeDate($vigFinal);

            // Se uma data estiver ausente, infere com base na regra de 5 anos do CEFET (evita erro de coluna NOT NULL no MySQL)
            if ($vigInicial && ! $vigFinal) {
                try {
                    $dt = new \DateTime($vigInicial);
                    $dt->modify('+5 years -1 day');
                    $vigFinal = $this->sanitizeDate($dt->format('Y-m-d'));
                } catch (\Throwable $e) {
                    $vigFinal = $vigInicial;
                }
            } elseif ($vigFinal && ! $vigInicial) {
                try {
                    $dt = new \DateTime($vigFinal);
                    $dt->modify('-5 years +1 day');
                    $vigInicial = $this->sanitizeDate($dt->format('Y-m-d'));
                } catch (\Throwable $e) {
                    $vigInicial = $vigFinal;
                }
            } elseif (! $vigInicial && ! $vigFinal) {
                $vigInicial = now()->toDateString();
                $vigFinal = now()->addYears(5)->toDateString();
            }

            // Registra no mapa para evitar duplicidade dentro do próprio arquivo
            $mapaExistentes[$cnpjLimpo] = true;
            $mapaExistentes[$cnpj] = true;
            $totalAdicionados++;

            $batch[] = [
                'empresa' => mb_substr($empresa, 0, 255),
                'cnpj' => mb_substr($cnpj, 0, 30),
                'vig_inicial' => $vigInicial,
                'vig_final' => $vigFinal,
                'created_at' => $agora,
                'updated_at' => $agora,
            ];

            if (count($batch) >= $batchSize) {
                EmpresasConveniadas::insert($batch);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            EmpresasConveniadas::insert($batch);
        }

        fclose($handle);

        return [
            'total_lidos' => $totalLidos,
            'total_adicionados' => $totalAdicionados,
            'total_ignorados' => $totalIgnorados,
            'total_no_banco' => EmpresasConveniadas::count(),
        ];
    }

    /**
     * Fluxo completo a partir de arquivo PDF enviado via upload.
     */
    public function processarUploadPdf(UploadedFile $pdfFile): array
    {
        $dirUpload = storage_path('app/imports/pdf');
        File::ensureDirectoryExists($dirUpload);

        $nomePdf = 'empresas_' . date('Ymd_His') . '_' . uniqid() . '.pdf';
        $pdfPath = $dirUpload . DIRECTORY_SEPARATOR . $nomePdf;
        $pdfFile->move($dirUpload, $nomePdf);

        // 1. Extração do PDF para CSV
        $resultadoExtracao = $this->extrairPdfParaCsv($pdfPath);

        // 2. Importação do CSV para a migration/tabela
        $resultadoBanco = $this->importarCsvParaBanco($resultadoExtracao['csv_path']);

        return [
            'total_extraido' => $resultadoExtracao['total'],
            'total_salvo' => $resultadoBanco['total_lidos'],
            'total_adicionados' => $resultadoBanco['total_adicionados'],
            'total_ignorados' => $resultadoBanco['total_ignorados'],
            'total_banco' => $resultadoBanco['total_no_banco'],
            'csv_filename' => $resultadoExtracao['csv_filename'],
            'csv_path' => $resultadoExtracao['csv_path'],
        ];
    }

    /**
     * Valida e ajusta a data para um formato estritamente aceito pelo MySQL.
     * Corrige datas impossíveis do PDF (ex: 29/02 em anos não bissextos como 2029 -> 28/02/2029).
     */
    public function sanitizeDate(?string $dateStr): ?string
    {
        if (! $dateStr) {
            return null;
        }

        $dateStr = trim($dateStr);
        if (! preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $dateStr, $matches)) {
            return null;
        }

        $y = (int) $matches[1];
        $m = (int) $matches[2];
        $d = (int) $matches[3];

        if ($y < 1900 || $y > 2100 || $m < 1 || $m > 12 || $d < 1) {
            return null;
        }

        // Calcula o último dia válido deste mês/ano (ex: 28 para fev/2029)
        $maxDays = (int) date('t', mktime(0, 0, 0, $m, 1, $y));
        $d = min($d, $maxDays);

        return sprintf('%04d-%02d-%02d', $y, $m, $d);
    }
}
