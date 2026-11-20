<?php

namespace Tests\Feature;

use App\Models\Aluno;
use App\Models\Estagio;
use App\Models\JornadaEstagio;
use App\Models\Orientador;
use App\Models\Presenca;
use App\Models\Supervisor;
use App\Models\Usuario;
use App\Services\FichaFrequenciaService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FichaFrequenciaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.foreign_key_constraints' => true,
        ]);
        DB::purge('sqlite');

        $this->createSchema();
    }

    private function createSchema(): void
    {
        $files = [
            '2026_08_15_000001_create_atribuicoes_table.php',
            '2026_08_15_000002_create_empresas_conveniadas_table.php',
            '2026_08_15_000003_create_usuarios_table.php',
            '2026_09_28_000001_create_alunos_table.php',
            '2026_09_28_000002_create_orientadores_table.php',
            '2026_09_28_000003_create_supervisores_table.php',
            '2026_10_04_000001_create_estagios_table.php',
            '2026_10_04_000002_create_jornadas_estagio_table.php',
            '2026_10_04_000003_create_presencas_table.php',
        ];

        foreach ($files as $file) {
            $migration = require base_path('database/migrations/' . $file);
            $migration->up();
        }
    }

    private function criarCenarioEstagio(
        string $dataInicio = '2026-10-27',
        string $dataFim = '2026-11-27',
        float $horasDiarias = 4.0
    ): array {
        // Aluno
        $userAluno = Usuario::create([
            'Nome' => 'Carlos Silva Aluno',
            'Email' => 'carlos@estagio.cefet.br',
            'Senha' => bcrypt('senha123'),
            'atribuicao_id' => 2,
        ]);
        $aluno = Aluno::create([
            'user_id' => $userAluno->user_ID,
            'nome' => $userAluno->Nome,
            'matricula' => '20261001INF',
            'curso' => 'Engenharia de Computação',
        ]);

        // Orientador
        $userOrientador = Usuario::create([
            'Nome' => 'Prof. Roberto Orientador',
            'Email' => 'roberto@cefet.br',
            'Senha' => bcrypt('senha123'),
            'atribuicao_id' => 3,
        ]);
        $orientador = Orientador::create([
            'user_ID' => $userOrientador->user_ID,
            'siape' => '98765432',
            'area' => 'Sistemas de Informação',
        ]);

        // Supervisor
        $userSupervisor = Usuario::create([
            'Nome' => 'Dra. Marina Supervisora',
            'Email' => 'marina@techcorp.com',
            'Senha' => bcrypt('senha123'),
            'atribuicao_id' => 4,
        ]);
        $supervisor = Supervisor::create([
            'user_ID' => $userSupervisor->user_ID,
            'cargo' => 'Gerente de Engenharia',
            'empresa' => 'TechCorp Soluções',
        ]);

        // Estágio
        $estagio = Estagio::create([
            'aluno_id' => $aluno->user_id,
            'orientador_id' => $orientador->orientador_ID,
            'supervisor_id' => $supervisor->supervisor_ID,
            'local' => 'TechCorp Soluções',
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim,
            'status' => Estagio::STATUS_ATIVO,
        ]);

        // Jornada
        $jornada = JornadaEstagio::create([
            'estagio_id' => $estagio->id,
            'horas_diarias' => $horasDiarias,
            'segunda' => true,
            'terca' => true,
            'quarta' => true,
            'quinta' => true,
            'sexta' => true,
            'sabado' => false,
            'domingo' => false,
        ]);

        return compact('userAluno', 'aluno', 'userOrientador', 'orientador', 'userSupervisor', 'supervisor', 'estagio', 'jornada');
    }

    public function test_template_original_existe_e_pode_ser_carregado(): void
    {
        $service = new FichaFrequenciaService();
        $path = $service->getTemplatePath();

        $this->assertFileExists($path);
    }

    public function test_preenchimento_automatico_dos_dados_cadastrais(): void
    {
        $dados = $this->criarCenarioEstagio();
        $estagio = $dados['estagio'];

        $service = new FichaFrequenciaService();
        $spreadsheet = $service->preencherFicha($estagio);
        $sheet = $spreadsheet->getActiveSheet();

        // Dados do Aluno e Curso
        $this->assertEquals('Carlos Silva Aluno', $sheet->getCell('F3')->getValue());
        $this->assertEquals('Curso de: Engenharia de Computação', $sheet->getCell('Z3')->getValue());
        $this->assertEquals('20261001INF', $sheet->getCell('F4')->getValue());
        $this->assertEquals('Unidade: Maracanã', $sheet->getCell('Z4')->getValue());

        // Dados da Empresa, Vigência e Carga Horária
        $this->assertEquals('TechCorp Soluções', $sheet->getCell('F5')->getValue());
        $this->assertEquals('27/10/2026 a 27/11/2026', $sheet->getCell('AC5')->getValue());
        $this->assertEquals('4 h', $sheet->getCell('AQ5')->getValue());

        // Supervisor
        $this->assertEquals('Dra. Marina Supervisora', $sheet->getCell('C32')->getValue());
        $this->assertEquals('Gerente de Engenharia', $sheet->getCell('Q32')->getValue());

        // Professor Orientador e SIAPE
        $this->assertEquals('Nome: Prof. Roberto Orientador', $sheet->getCell('AH42')->getValue());
        $this->assertEquals('Siape:   98765432', $sheet->getCell('AH44')->getValue());
    }

    public function test_preenchimento_calendario_respeitando_legendas(): void
    {
        $dados = $this->criarCenarioEstagio('2026-10-27', '2026-11-27', 4.0);
        $estagio = $dados['estagio'];

        // Registrar uma falta oficial (recusada) no dia 05/11/2026
        Presenca::create([
            'estagio_id' => $estagio->id,
            'data' => '2026-11-05',
            'horas' => 4.0,
            'modo' => 'oficial',
            'status' => 'recusada',
        ]);

        $service = new FichaFrequenciaService();
        $spreadsheet = $service->preencherFicha($estagio);
        $sheet = $spreadsheet->getActiveSheet();

        // Linha 17 = Outubro
        // Dias 1 a 26 estão fora da vigência -> devem ser vazios/nulos
        $this->assertNull($sheet->getCell('E17')->getValue()); // Dia 1
        $this->assertNull($sheet->getCell('AI17')->getValue()); // Dia 26

        // Dia 27 (Terça-feira): vigência ativa -> 'C' (Compareceu)
        $this->assertEquals('C', $sheet->getCell('AJ17')->getValue());

        // Dia 28 (Quarta-feira): Dia do Servidor Público (Feriado escolar) -> 'X'
        $this->assertEquals('X', $sheet->getCell('AK17')->getValue());

        // Dia 29 (Quinta-feira): dia útil de vigência -> 'C'
        $this->assertEquals('C', $sheet->getCell('AL17')->getValue());

        // Dia 30 (Sexta-feira): dia útil de vigência -> 'C'
        $this->assertEquals('C', $sheet->getCell('AM17')->getValue());

        // Dia 31 (Sábado): Fim de semana -> 'X'
        $this->assertEquals('X', $sheet->getCell('AN17')->getValue());

        // Linha 18 = Novembro
        // Dia 1 (Domingo): Fim de semana -> 'X'
        $this->assertEquals('X', $sheet->getCell('E18')->getValue());

        // Dia 2 (Segunda-feira): Finados (Feriado Nacional) -> 'X'
        $this->assertEquals('X', $sheet->getCell('H18')->getValue());

        // Dia 3 e 4 (Terça e Quarta): dias úteis -> 'C'
        $this->assertEquals('C', $sheet->getCell('I18')->getValue());
        $this->assertEquals('C', $sheet->getCell('J18')->getValue());

        // Dia 5 (Quinta-feira): falta registrada (recusada) -> 'F'
        $this->assertEquals('F', $sheet->getCell('L18')->getValue());

        // Dia 15 (Domingo): Proclamação da República -> 'X'
        $this->assertEquals('X', $sheet->getCell('W18')->getValue());

        // Dia 20 (Sexta-feira): Consciência Negra (Feriado Nacional/RJ) -> 'X'
        $this->assertEquals('X', $sheet->getCell('AC18')->getValue());

        // Dias após 27/11 (ex: dia 28, 29, 30) -> fora da vigência -> vazios/nulos
        $this->assertNull($sheet->getCell('AK18')->getValue()); // Dia 28
        $this->assertNull($sheet->getCell('AL18')->getValue()); // Dia 29
        $this->assertNull($sheet->getCell('AM18')->getValue()); // Dia 30
    }

    public function test_calculo_das_formulas_de_horas_e_totais(): void
    {
        $dados = $this->criarCenarioEstagio('2026-10-27', '2026-11-27', 4.0);
        $estagio = $dados['estagio'];

        $service = new FichaFrequenciaService();
        $spreadsheet = $service->preencherFicha($estagio);
        $sheet = $spreadsheet->getActiveSheet();

        // Em Outubro (linha 17): 3 presenças (dias 27, 29, 30)
        $this->assertEquals(3, $sheet->getCell('AO17')->getCalculatedValue());
        $this->assertEquals(12, $sheet->getCell('AQ17')->getCalculatedValue()); // 3 * 4h = 12h

        // Em Novembro (linha 18): 18 presenças
        $this->assertEquals(18, $sheet->getCell('AO18')->getCalculatedValue());
        $this->assertEquals(72, $sheet->getCell('AQ18')->getCalculatedValue()); // 18 * 4h = 72h

        // Totais na linha 20
        $this->assertEquals(21, $sheet->getCell('AO20')->getCalculatedValue()); // 3 + 18 = 21 presenças
        $this->assertEquals(84, $sheet->getCell('AQ20')->getCalculatedValue()); // 12 + 72 = 84 horas
    }

    public function test_diferentes_periodos_de_vigencia_semestre_letivo(): void
    {
        // Período de 01/03/2026 a 30/06/2026 (4 meses com 6h diárias)
        $dados = $this->criarCenarioEstagio('2026-03-01', '2026-06-30', 6.0);
        $estagio = $dados['estagio'];

        $service = new FichaFrequenciaService();
        $spreadsheet = $service->preencherFicha($estagio);
        $sheet = $spreadsheet->getActiveSheet();

        $this->assertEquals('6 h', $sheet->getCell('AQ5')->getValue());

        // Março (linha 10): Ano 2026 e AP10 = 6
        $this->assertEquals(2026, $sheet->getCell('D10')->getValue());
        $this->assertEquals(6, $sheet->getCell('AP10')->getValue());

        // Abril (linha 11): Ano 2026 e AP11 = 6
        $this->assertEquals(2026, $sheet->getCell('D11')->getValue());
        $this->assertEquals(6, $sheet->getCell('AP11')->getValue());

        // Maio (linha 12): Ano 2026 e AP12 = 6
        $this->assertEquals(2026, $sheet->getCell('D12')->getValue());
        $this->assertEquals(6, $sheet->getCell('AP12')->getValue());

        // Junho (linha 13): Ano 2026 e AP13 = 6
        $this->assertEquals(2026, $sheet->getCell('D13')->getValue());
        $this->assertEquals(6, $sheet->getCell('AP13')->getValue());

        // Meses fora da vigência (Janeiro, Fevereiro, Julho a Dezembro) devem ter D nulo
        $this->assertNull($sheet->getCell('D8')->getValue()); // Janeiro
        $this->assertNull($sheet->getCell('D9')->getValue()); // Fevereiro
        $this->assertNull($sheet->getCell('D14')->getValue()); // Julho
        $this->assertNull($sheet->getCell('D19')->getValue()); // Dezembro

        // Verificar cálculo do total
        $totalPresencas = $sheet->getCell('AO20')->getCalculatedValue();
        $totalHoras = $sheet->getCell('AQ20')->getCalculatedValue();

        $this->assertGreaterThan(70, $totalPresencas);
        $this->assertEquals($totalPresencas * 6, $totalHoras);
    }

    public function test_download_requer_autenticacao(): void
    {
        $dados = $this->criarCenarioEstagio();
        $estagio = $dados['estagio'];

        $response = $this->get(route('estagios.ficha', $estagio));
        $response->assertRedirect('/login');
    }

    public function test_aluno_pode_baixar_ficha_do_proprio_estagio(): void
    {
        $dados = $this->criarCenarioEstagio();
        $estagio = $dados['estagio'];

        $response = $this->withSession(['usuario_id' => $dados['userAluno']->user_ID])
            ->get(route('estagios.ficha', $estagio));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_aluno_nao_pode_baixar_ficha_de_outro_aluno(): void
    {
        $dados = $this->criarCenarioEstagio();
        $estagio = $dados['estagio'];

        $outroAluno = Usuario::create([
            'Nome' => 'Outro Aluno',
            'Email' => 'outro@cefet.br',
            'Senha' => bcrypt('senha123'),
            'atribuicao_id' => 2,
        ]);
        Aluno::create([
            'user_id' => $outroAluno->user_ID,
            'nome' => $outroAluno->Nome,
            'matricula' => '20269999INF',
            'curso' => 'Engenharia de Controle e Automação',
        ]);

        $response = $this->withSession(['usuario_id' => $outroAluno->user_ID])
            ->get(route('estagios.ficha', $estagio));

        $response->assertNotFound();
    }

    public function test_orientador_e_supervisor_podem_baixar_ficha(): void
    {
        $dados = $this->criarCenarioEstagio();
        $estagio = $dados['estagio'];

        // Orientador
        $resOrientador = $this->withSession(['usuario_id' => $dados['userOrientador']->user_ID])
            ->get(route('estagios.ficha', $estagio));
        $resOrientador->assertOk();

        // Supervisor
        $resSupervisor = $this->withSession(['usuario_id' => $dados['userSupervisor']->user_ID])
            ->get(route('estagios.ficha', $estagio));
        $resSupervisor->assertOk();
    }

    public function test_rota_publica_ficha_frequencia(): void
    {
        // Sem autenticação -> baixa modelo em branco
        $response = $this->get(route('ficha.download'));
        $response->assertOk();

        // Com autenticação de aluno que possui estágio -> baixa preenchida
        $dados = $this->criarCenarioEstagio();
        $responseAuth = $this->withSession(['usuario_id' => $dados['userAluno']->user_ID])
            ->get(route('ficha.download'));
        $responseAuth->assertOk();
    }
}
