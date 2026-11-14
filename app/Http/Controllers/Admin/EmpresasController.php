<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmpresasConveniadas;
use App\Services\CnpjEnriquecimentoService;
use App\Services\EmpresaConveniadaImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmpresasController extends Controller
{
    public function __construct(
        private readonly EmpresaConveniadaImportService $importService,
        private readonly CnpjEnriquecimentoService $enriquecimentoService,
    ) {}

    /**
     * Tela administrativa de listagem e importação de empresas conveniadas.
     */
    public function index(Request $request)
    {
        $busca  = $request->query('busca');
        $status = $request->query('status');
        $uf     = $request->query('uf');

        $query = EmpresasConveniadas::query();

        if (! empty($busca)) {
            $query->busca($busca);
        }

        if ($status === 'vigente') {
            $query->vigentes();
        } elseif ($status === 'expirado') {
            $query->expirados();
        }

        if (! empty($uf)) {
            $query->porUf($uf);
        }

        $empresas = $query->orderBy('empresa', 'asc')->paginate(15)->withQueryString();

        $stats = [
            'total'           => EmpresasConveniadas::count(),
            'vigentes'        => EmpresasConveniadas::vigentes()->count(),
            'expiradas'       => EmpresasConveniadas::expirados()->count(),
            'com_localizacao' => EmpresasConveniadas::comLocalizacao()->count(),
            'sem_localizacao' => EmpresasConveniadas::semLocalizacao()->count(),
        ];

        // UFs disponíveis para filtro (apenas as que já têm dados)
        $ufsDisponiveis = EmpresasConveniadas::whereNotNull('uf')
            ->distinct()
            ->orderBy('uf')
            ->pluck('uf');

        // Localiza o CSV mais recente gerado, se houver
        $csvDir    = storage_path('app/imports/csv');
        $ultimoCsv = null;
        if (File::isDirectory($csvDir)) {
            $arquivos = File::files($csvDir);
            if (! empty($arquivos)) {
                usort($arquivos, fn ($a, $b) => $b->getMTime() <=> $a->getMTime());
                $ultimoCsv = $arquivos[0]->getFilename();
            }
        }

        return view('admin.empresas.index', [
            'empresas'       => $empresas,
            'stats'          => $stats,
            'ultimoCsv'      => $ultimoCsv,
            'busca'          => $busca,
            'status'         => $status,
            'uf'             => $uf,
            'ufsDisponiveis' => $ufsDisponiveis,
        ]);
    }

    /**
     * Recebe o upload do PDF, executa extração, gera CSV e salva no banco.
     */
    public function importar(Request $request)
    {
        $request->validate([
            'arquivo_pdf' => [
                'required',
                'file',
                'mimes:pdf',
                'max:30720', // até 30MB
            ],
        ], [
            'arquivo_pdf.required' => 'Por favor, selecione um arquivo PDF oficial de convênios.',
            'arquivo_pdf.file'     => 'O arquivo enviado é inválido.',
            'arquivo_pdf.mimes'    => 'O arquivo deve estar estritamente no formato PDF (.pdf).',
            'arquivo_pdf.max'      => 'O arquivo não pode ultrapassar 30 MB.',
        ]);

        try {
            $resultado = $this->importService->processarUploadPdf($request->file('arquivo_pdf'));

            $mensagem = "Importação realizada com sucesso! {$resultado['total_adicionados']} novas empresas foram adicionadas ao banco de dados.";
            if ($resultado['total_ignorados'] > 0) {
                $mensagem .= " {$resultado['total_ignorados']} empresas que já existiam foram mantidas sem alteração.";
            }

            return redirect()->route('admin.empresas')->with([
                'sucesso'         => $mensagem,
                'csv_gerado'      => $resultado['csv_filename'],
                'total_importado' => $resultado['total_adicionados'],
                'total_ignorados' => $resultado['total_ignorados'],
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro ao importar PDF de empresas', [
                'mensagem' => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);

            return redirect()->route('admin.empresas')->withErrors([
                'arquivo_pdf' => 'Falha durante o processamento do PDF: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Enriquece um lote de empresas com localização via BrasilAPI (AJAX).
     * Retorna JSON com o progresso do lote processado.
     */
    public function enriquecerLote(Request $request)
    {
        $limite = (int) $request->input('limite', 30);
        $limite = max(1, min($limite, 100)); // entre 1 e 100

        try {
            $resultado = $this->enriquecimentoService->enriquecerLote($limite);

            return response()->json([
                'sucesso'     => true,
                'processadas' => $resultado['processadas'],
                'sucessos'    => $resultado['sucessos'],
                'falhas'      => $resultado['falhas'],
                'pendentes'   => $resultado['pendentes'],
                'erros'       => $resultado['erros'],
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro ao enriquecer lote de empresas via CNPJ', [
                'erro' => $e->getMessage(),
            ]);

            return response()->json([
                'sucesso'  => false,
                'mensagem' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Salva a base atual de empresas e localizações no arquivo JSON do Seeder do projeto.
     */
    public function salvarSeeder()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('empresas:salvar-seeder');

            $total = EmpresasConveniadas::count();
            $localizadas = EmpresasConveniadas::comLocalizacao()->count();

            return redirect()->route('admin.empresas')->with([
                'sucesso' => "Base embarcada (Seeder) atualizada com sucesso! {$total} empresas e {$localizadas} localizações foram salvas no repositório do projeto.",
            ]);
        } catch (\Throwable $e) {
            return redirect()->route('admin.empresas')->withErrors([
                'erro' => 'Falha ao atualizar base do seeder: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Download do arquivo CSV gerado na importação.
     */
    public function downloadCsv(string $arquivo)
    {
        $nomeSeguro = basename($arquivo);
        $caminho    = storage_path('app/imports/csv' . DIRECTORY_SEPARATOR . $nomeSeguro);

        if (! file_exists($caminho)) {
            abort(404, 'Arquivo CSV não encontrado.');
        }

        return response()->download($caminho, $nomeSeguro, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Exporta todos os registros atualmente salvos no banco como um arquivo CSV novo.
     */
    public function exportarBancoCsv(): StreamedResponse
    {
        $filename = 'empresas_conveniadas_export_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            // BOM UTF-8 para Excel abrir sem quebrar acentuação
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['empresa', 'cnpj', 'vig_inicial', 'vig_final', 'status', 'municipio', 'uf', 'cep', 'bairro', 'logradouro', 'numero']);

            EmpresasConveniadas::orderBy('empresa')->chunk(200, function ($empresas) use ($handle) {
                foreach ($empresas as $emp) {
                    fputcsv($handle, [
                        $emp->empresa,
                        $emp->cnpj,
                        $emp->vig_inicial ? $emp->vig_inicial->format('Y-m-d') : '',
                        $emp->vig_final ? $emp->vig_final->format('Y-m-d') : '',
                        $emp->isVigente() ? 'Vigente' : 'Expirado',
                        $emp->municipio ?? '',
                        $emp->uf ?? '',
                        $emp->cep ?? '',
                        $emp->bairro ?? '',
                        $emp->logradouro ?? '',
                        $emp->numero ?? '',
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Apaga todas as empresas conveniadas cadastradas.
     */
    public function limpar(Request $request)
    {
        $request->validate([
            'confirmacao' => ['required', 'string'],
        ], [
            'confirmacao.required' => 'Digite a frase de confirmação para autorizar a exclusão.',
        ]);

        if (trim(strtoupper($request->input('confirmacao'))) !== 'APAGAR TODAS AS EMPRESAS') {
            return redirect()->route('admin.empresas')->withErrors([
                'confirmacao' => 'A frase digitada não confere. Para apagar, digite exatamente: APAGAR TODAS AS EMPRESAS',
            ]);
        }

        $totalRemovido = EmpresasConveniadas::count();
        EmpresasConveniadas::query()->delete();

        return redirect()->route('admin.empresas')->with([
            'sucesso' => "Todas as empresas conveniadas foram apagadas com sucesso ({$totalRemovido} registros excluídos).",
        ]);
    }
}
