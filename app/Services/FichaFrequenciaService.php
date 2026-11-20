<?php

namespace App\Services;

use App\Models\Estagio;
use App\Models\Presenca;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FichaFrequenciaService
{
    /**
     * Mapeamento de mês (1-12) para a respectiva linha da planilha.
     */
    private const MES_LINHAS = [
        1  => 8,   // Janeiro
        2  => 9,   // Fevereiro
        3  => 10,  // Março
        4  => 11,  // Abril
        5  => 12,  // Maio
        6  => 13,  // Junho
        7  => 14,  // Julho
        8  => 15,  // Agosto
        9  => 16,  // Setembro
        10 => 17,  // Outubro
        11 => 18,  // Novembro
        12 => 19,  // Dezembro
    ];

    /**
     * Mapeamento do dia do mês (1-31) para a coluna correspondente na planilha.
     * Células mescladas recebem o valor na primeira coluna do intervalo mesclado:
     * - Dia 1: E (E:G)
     * - Dia 4: J (J:K)
     * - Dia 7: N (N:O)
     * - Dia 17: Y (Y:Z)
     */
    private const DIA_COLUNAS = [
        1  => 'E',
        2  => 'H',
        3  => 'I',
        4  => 'J',
        5  => 'L',
        6  => 'M',
        7  => 'N',
        8  => 'P',
        9  => 'Q',
        10 => 'R',
        11 => 'S',
        12 => 'T',
        13 => 'U',
        14 => 'V',
        15 => 'W',
        16 => 'X',
        17 => 'Y',
        18 => 'AA',
        19 => 'AB',
        20 => 'AC',
        21 => 'AD',
        22 => 'AE',
        23 => 'AF',
        24 => 'AG',
        25 => 'AH',
        26 => 'AI',
        27 => 'AJ',
        28 => 'AK',
        29 => 'AL',
        30 => 'AM',
        31 => 'AN',
    ];

    /**
     * Retorna o caminho do modelo oficial da Ficha de Frequência.
     */
    public function getTemplatePath(): string
    {
        $path = public_path('documents/CEFET - Ficha Individual de Frequência.xlsx');

        if (! file_exists($path)) {
            $path = base_path('public/documents/CEFET - Ficha Individual de Frequência.xlsx');
        }

        if (! file_exists($path)) {
            throw new \RuntimeException("Modelo de ficha de frequência não encontrado em: {$path}");
        }

        return $path;
    }

    /**
     * Preenche e retorna a planilha PhpSpreadsheet com os dados do estágio.
     */
    public function preencherFicha(Estagio $estagio, ?int $anoFiltro = null): Spreadsheet
    {
        @ini_set('memory_limit', '512M');

        $estagio->loadMissing([
            'aluno.usuario',
            'orientador.usuario',
            'supervisor.usuario',
            'jornada',
            'presencas',
        ]);

        $templatePath = $this->getTemplatePath();
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Dados Cadastrais do Aluno e Estágio
        $alunoNome = trim($estagio->aluno?->nome ?? $estagio->aluno?->usuario?->Nome ?? '');
        $matricula = trim($estagio->aluno?->matricula ?? '');
        $curso = trim($estagio->aluno?->curso ?? '');
        $unidade = 'Maracanã';

        $sheet->setCellValue('F3', $alunoNome);
        $sheet->setCellValue('Z3', 'Curso de: ' . ($curso ?: 'Não informado'));

        $sheet->setCellValue('F4', $matricula);
        $sheet->setCellValue('Z4', 'Unidade: ' . $unidade);

        // 2. Dados da Empresa e Vigência
        $empresa = trim($estagio->supervisor?->empresa ?? $estagio->local ?? '');
        $vigencia = $estagio->data_inicio->format('d/m/Y') . ' a ' . $estagio->data_fim->format('d/m/Y');

        $horasDiariasNum = (float) ($estagio->jornada?->horas_diarias ?? 6);
        $horasDiariasFormatada = ($horasDiariasNum == (int) $horasDiariasNum)
            ? (int) $horasDiariasNum . ' h'
            : number_format($horasDiariasNum, 1, ',', '') . ' h';

        $sheet->setCellValue('F5', $empresa);
        $sheet->setCellValue('AC5', $vigencia);
        $sheet->setCellValue('AQ5', $horasDiariasFormatada);

        // 3. Supervisor da Empresa
        $supervisorNome = trim($estagio->supervisor?->usuario?->Nome ?? '');
        $supervisorCargo = trim($estagio->supervisor?->cargo ?? '');

        if ($supervisorNome !== '') {
            $sheet->setCellValue('C32', $supervisorNome);
        }
        if ($supervisorCargo !== '') {
            $sheet->setCellValue('Q32', $supervisorCargo);
        }

        // Data do preenchimento / emissão
        $dataEmissao = now()->format('d/m/Y');
        $sheet->setCellValue('D31', $dataEmissao);

        // 4. Professor Orientador
        $orientadorNome = trim($estagio->orientador?->usuario?->Nome ?? '');
        $orientadorSiape = trim($estagio->orientador?->siape ?? '');

        $sheet->setCellValue('AH42', 'Nome: ' . ($orientadorNome ?: '.......................................................................'));
        $sheet->setCellValue('AH44', 'Siape:   ' . ($orientadorSiape ?: '......................................................................'));

        // 5. Preenchimento do Calendário de Frequência
        $this->preencherCalendario($sheet, $estagio, $anoFiltro, $horasDiariasNum);

        return $spreadsheet;
    }

    /**
     * Preenche as linhas de meses e dias do calendário de frequência.
     */
    protected function preencherCalendario($sheet, Estagio $estagio, ?int $anoFiltro, float $horasDiariasNum): void
    {
        $inicio = $estagio->data_inicio->copy()->startOfDay();
        $fim = $estagio->data_fim->copy()->endOfDay();

        // Mapear presenças existentes por data (formato Y-m-d)
        $presencasPorData = $estagio->presencas
            ->groupBy(fn (Presenca $p) => Carbon::parse($p->data)->toDateString());

        // Determinar os meses que fazem parte da vigência do estágio
        // Se um ano específico foi solicitado, restringir a esse ano
        $cursor = $inicio->copy()->startOfMonth();
        $fimMes = $fim->copy()->endOfMonth();

        // Meses ativos: coleção de [ano => int, mes => int]
        $mesesAtivos = [];
        while ($cursor->lessThanOrEqualTo($fimMes)) {
            if ($anoFiltro === null || $cursor->year === $anoFiltro) {
                $mesesAtivos[$cursor->month] = [
                    'ano' => $cursor->year,
                    'mes' => $cursor->month,
                ];
            }
            $cursor->addMonth();
        }

        foreach (self::MES_LINHAS as $mes => $linha) {
            if (! isset($mesesAtivos[$mes])) {
                // Mês fora da vigência: não preenche
                continue;
            }

            $infoMes = $mesesAtivos[$mes];
            $ano = $infoMes['ano'];

            // Preenche o Ano (coluna D) e alinha ao centro
            $cellAno = $sheet->getCell("D{$linha}");
            $cellAno->setValue($ano);
            $cellAno->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Preenche a Carga Horária Diária (coluna AP) como valor numérico para que as fórmulas
            // de Carga Horária Mensal (=PRODUCT(AO..., AP...)) e Total Geral funcionem nativamente
            $sheet->setCellValue("AP{$linha}", $horasDiariasNum);

            // Dias no mês (ex: 28, 29, 30 ou 31)
            $totalDiasNoMes = Carbon::createFromDate($ano, $mes, 1)->daysInMonth;

            for ($dia = 1; $dia <= 31; $dia++) {
                $coluna = self::DIA_COLUNAS[$dia];
                $coord = "{$coluna}{$linha}";

                if ($dia > $totalDiasNoMes) {
                    // Dia inexistente no mês (ex: 31 de abril, 30 de fevereiro)
                    continue;
                }

                $dataAtual = Carbon::createFromDate($ano, $mes, $dia)->startOfDay();

                // Fora do período de vigência do estágio
                if ($dataAtual->lessThan($inicio) || $dataAtual->greaterThan($fim)) {
                    continue;
                }

                // Determinar código segundo a legenda oficial:
                // C = Compareceu | F = Faltou | X = Sábados, Domingos e Feriados | L = Licença | R = Recesso
                $codigo = $this->determinarCodigoDia($dataAtual, $estagio, $presencasPorData);

                if ($codigo !== null && $codigo !== '') {
                    $sheet->setCellValue($coord, $codigo);
                }
            }
        }
    }

    /**
     * Determina o código ('C', 'X', 'F', 'L', 'R') para um dia específico.
     */
    protected function determinarCodigoDia(Carbon $data, Estagio $estagio, $presencasPorData): ?string
    {
        // 1. Fins de semana (Sábado ou Domingo) -> 'X'
        if ($data->isWeekend()) {
            return 'X';
        }

        // 2. Feriados nacionais ou estaduais do RJ -> 'X'
        if ($this->isFeriado($data)) {
            return 'X';
        }

        // 3. Dias não previstos na jornada do estágio -> 'X'
        if ($estagio->jornada && ! $estagio->jornada->trabalhaNoDia((int) $data->dayOfWeekIso)) {
            return 'X';
        }

        // 4. Verificação de registros na tabela de presenças
        $chaveData = $data->toDateString();
        $presencasDoDia = $presencasPorData->get($chaveData);

        if ($presencasDoDia && $presencasDoDia->isNotEmpty()) {
            $oficial = $presencasDoDia->firstWhere('modo', Presenca::MODO_OFICIAL) ?? $presencasDoDia->first();

            if ($oficial->status === Presenca::STATUS_RECUSADA) {
                return 'F';
            }

            // Presença aprovada ou pendente de validação
            return 'C';
        }

        // Se não houver registro pontual no banco, mas for um dia útil previsto na vigência:
        // Por padrão no preenchimento de frequência de estágio, comparecimento previsto
        return 'C';
    }

    /**
     * Calcula a data do Domingo de Páscoa utilizando o algoritmo clássico de Meeus/Jones/Butcher.
     * Funciona em qualquer versão do PHP de forma pura, sem depender da extensão ext-calendar.
     */
    public static function calcularDataPascoa(int $ano): Carbon
    {
        $a = $ano % 19;
        $b = intdiv($ano, 100);
        $c = $ano % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $mes = intdiv($h + $l - 7 * $m + 114, 31);
        $dia = (($h + $l - 7 * $m + 114) % 31) + 1;

        return Carbon::createFromDate($ano, $mes, $dia)->startOfDay();
    }

    /**
     * Verifica se uma data é feriado nacional ou feriado do estado do Rio de Janeiro.
     */
    public function isFeriado(CarbonInterface $data): bool
    {
        $ano = (int) $data->year;
        $mesDia = $data->format('m-d');

        // Feriados fixos
        $feriadosFixos = [
            '01-01', // Confraternização Universal (Ano Novo)
            '04-21', // Tiradentes
            '04-23', // São Jorge (Feriado Estadual RJ)
            '05-01', // Dia do Trabalhador
            '09-07', // Independência do Brasil
            '10-12', // Nossa Senhora Aparecida
            '10-15', // Dia do Professor
            '10-28', // Dia do Servidor Público
            '11-02', // Finados
            '11-15', // Proclamação da República
            '11-20', // Dia Nacional de Zumbi e da Consciência Negra
            '12-25', // Natal
        ];

        if (in_array($mesDia, $feriadosFixos, true)) {
            return true;
        }

        // Feriados móveis calculados pela Páscoa cristã
        $pascoa = self::calcularDataPascoa($ano);

        $carnavalSegunda = (clone $pascoa)->subDays(48);
        $carnavalTerca = (clone $pascoa)->subDays(47);
        $quartaCinzas = (clone $pascoa)->subDays(46);
        $sextaSanta = (clone $pascoa)->subDays(2);
        $corpusChristi = (clone $pascoa)->addDays(60);

        $feriadosMoveis = [
            $carnavalSegunda->format('m-d'),
            $carnavalTerca->format('m-d'),
            $quartaCinzas->format('m-d'),
            $sextaSanta->format('m-d'),
            $corpusChristi->format('m-d'),
        ];

        return in_array($mesDia, $feriadosMoveis, true);
    }

    /**
     * Salva a planilha preenchida em um arquivo físico no servidor.
     */
    public function salvarArquivo(Estagio $estagio, ?string $destino = null, ?int $anoFiltro = null): string
    {
        $spreadsheet = $this->preencherFicha($estagio, $anoFiltro);

        if (! $destino) {
            $alunoNome = Str::slug($estagio->aluno?->nome ?? 'estagio');
            $destino = storage_path("app/fichas/Ficha_Frequencia_{$alunoNome}_{$estagio->id}.xlsx");
        }

        $diretorio = dirname($destino);
        if (! is_dir($diretorio)) {
            mkdir($diretorio, 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($destino);

        return $destino;
    }

    /**
     * Retorna uma resposta HTTP StreamedResponse para download direto do arquivo .xlsx no navegador.
     */
    public function downloadResponse(Estagio $estagio, ?int $anoFiltro = null): StreamedResponse
    {
        $spreadsheet = $this->preencherFicha($estagio, $anoFiltro);
        $alunoNome = Str::slug($estagio->aluno?->nome ?? 'estagiario');
        $nomeArquivo = sprintf('Ficha_Frequencia_%s_estagio_%d.xlsx', $alunoNome, $estagio->id);

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $nomeArquivo, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $nomeArquivo . '"',
            'Cache-Control' => 'max-age=0, must-revalidate',
            'Pragma' => 'public',
        ]);
    }
}
