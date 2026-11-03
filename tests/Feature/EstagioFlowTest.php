<?php

namespace Tests\Feature;

use App\Models\Estagio;
use App\Models\JornadaEstagio;
use App\Models\Presenca;
use App\Models\Usuario;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EstagioFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-10-04 12:00:00');

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.foreign_key_constraints' => true,
        ]);
        DB::purge('sqlite');

        $this->createExistingProjectSchema();
        foreach ([
            '2026_10_04_000001_create_estagios_table.php',
            '2026_10_04_000002_create_jornadas_estagio_table.php',
            '2026_10_04_000003_create_presencas_table.php',
            '2026_10_04_000004_add_supervisor_and_attendance_modes.php',
            '2026_11_02_000001_use_supervisor_schedule_for_attendance.php',
            '2026_11_02_000002_allow_personal_and_official_attendance_per_day.php',
        ] as $migrationFile) {
            $migration = require base_path('database/migrations/' . $migrationFile);
            $migration->up();
        }

        DB::table('atribuicoes')->insert([
            ['Atribuicao_ID' => 1, 'Nome_atribuicao' => 'Administrador'],
            ['Atribuicao_ID' => 2, 'Nome_atribuicao' => 'Aluno'],
            ['Atribuicao_ID' => 3, 'Nome_atribuicao' => 'Orientador'],
            ['Atribuicao_ID' => 4, 'Nome_atribuicao' => 'Supervisor'],
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_job_board_is_removed_and_company_directory_remains_available(): void
    {
        $alunoId = $this->createUser('aluno');

        $this->get('/vagas')->assertNotFound();
        $this->get('/vagas/1')->assertNotFound();
        $this->get('/inscricoes')->assertNotFound();
        $this->get('/cadastro/contratante')->assertNotFound();

        $this->withSession(['usuario_id' => $alunoId])
            ->get('/empresas')
            ->assertOk()
            ->assertSee('Empresas Conveniadas')
            ->assertDontSee('Vagas Disponíveis')
            ->assertDontSee('Ver Vagas Disponíveis');

        $this->withSession(['usuario_id' => $alunoId])
            ->get('/tutorial')
            ->assertOk()
            ->assertSee('Trilha Completa do Estágio')
            ->assertSee('7 etapas para você completar');
    }

    public function test_home_redirects_authenticated_users_to_their_dashboard(): void
    {
        $alunoId = $this->createUser('aluno');
        $orientadorId = $this->createUser('orientador');
        $supervisorId = $this->createUser('supervisor');

        $this->get('/home')
            ->assertOk()
            ->assertSee('Criar Conta')
            ->assertSee('Já tenho conta');

        $this->withSession(['usuario_id' => $alunoId])
            ->get('/')
            ->assertRedirect(route('dashboard'));

        $this->withSession(['usuario_id' => $alunoId])
            ->get('/aluno')
            ->assertRedirect(route('dashboard'));

        $this->get('/home')->assertRedirect(route('dashboard'));
        $this->get(route('dashboard'))->assertOk()->assertSee('Bem-vindo de volta');

        $this->withSession(['usuario_id' => $orientadorId])
            ->get('/home')
            ->assertRedirect(route('dashboard'));
        $this->get(route('dashboard'))->assertOk()->assertSee('Dashboard do orientador');

        $this->withSession(['usuario_id' => $supervisorId])
            ->get('/home')
            ->assertRedirect(route('dashboard'));
        $this->get(route('dashboard'))->assertOk()->assertSee('Painel do supervisor');
    }

    public function test_legacy_job_offerings_table_is_removed_by_migration(): void
    {
        Schema::create('vagas', function (Blueprint $table) {
            $table->integer('Vaga_ID')->primary();
        });

        $migration = require base_path('database/migrations/2026_10_30_000001_drop_vagas_table.php');
        $migration->up();

        $this->assertFalse(Schema::hasTable('vagas'));
    }

    public function test_orientador_initializes_stage_and_student_cannot_open_another_students_stage(): void
    {
        $orientadorId = $this->createUser('orientador');
        $supervisorId = $this->createUser('supervisor');
        $alunoId = $this->createUser('aluno');
        $outroAlunoId = $this->createUser('aluno');

        $this->withSession(['usuario_id' => $alunoId])
            ->get('/perfil')
            ->assertOk()
            ->assertSee('Informática');

        $response = $this->withSession(['usuario_id' => $orientadorId])
            ->post(route('estagios.store'), [
                'aluno_id' => $alunoId,
                'supervisor_id' => 1,
                'local' => 'Laboratório de Sistemas',
                'data_inicio' => today()->subDays(3)->toDateString(),
                'data_fim' => today()->addDays(30)->toDateString(),
            ]);

        $stage = Estagio::firstOrFail();
        $response->assertRedirect(route('estagios.show', $stage));
        $this->assertSame($alunoId, $stage->aluno_id);
        $this->assertSame(1, $stage->orientador_id);
        $this->assertSame(1, $stage->supervisor_id);
        $this->assertSame(Estagio::STATUS_ATIVO, $stage->status);

        $this->withSession(['usuario_id' => $orientadorId])
            ->get(route('estagios.index'))
            ->assertOk()
            ->assertSee('Laboratório de Sistemas');

        $this->withSession(['usuario_id' => $orientadorId])
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Laboratório de Sistemas');

        $this->withSession(['usuario_id' => $supervisorId])
            ->get(route('estagios.index'))
            ->assertOk()
            ->assertSee('Laboratório de Sistemas')
            ->assertSee('Estágios sob supervisão');

        $this->withSession(['usuario_id' => $supervisorId])
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Painel do supervisor')
            ->assertSee('Laboratório de Sistemas');

        $this->withSession(['usuario_id' => $alunoId])
            ->get(route('estagios.show', $stage))
            ->assertOk()
            ->assertSee('A jornada ainda não foi definida pelo supervisor.')
            ->assertDontSee('Definir jornada');

        $this->withSession(['usuario_id' => $orientadorId])
            ->post(route('estagios.store'), [
                'aluno_id' => $alunoId,
                'local' => 'Outro local',
                'data_inicio' => today()->toDateString(),
                'data_fim' => today()->addDays(30)->toDateString(),
            ])
            ->assertSessionHasErrors('aluno_id');
        $this->assertSame(1, Estagio::where('aluno_id', $alunoId)->count());

        $this->withSession(['usuario_id' => $outroAlunoId])
            ->get(route('estagios.show', $stage))
            ->assertNotFound();

        $this->withSession(['usuario_id' => $outroAlunoId])
            ->get(route('controle-horas'))
            ->assertOk()
            ->assertSee('Você não possui um estágio ativo.');
    }

    public function test_supervisor_configures_journey_and_student_marks_attendance_with_assigned_duration(): void
    {
        $alunoId = $this->createUser('aluno');
        $orientadorId = $this->createUser('orientador');
        $supervisorId = $this->createUser('supervisor');
        $stage = Estagio::create([
            'aluno_id' => $alunoId,
            'orientador_id' => 1,
            'supervisor_id' => 1,
            'local' => 'Laboratório de Sistemas',
            'data_inicio' => today()->subDays(5)->toDateString(),
            'data_fim' => today()->addDays(30)->toDateString(),
            'status' => Estagio::STATUS_ATIVO,
        ]);

        $this->withSession(['usuario_id' => $alunoId])
            ->post(route('estagios.jornada.store', $stage), [
                'horas_diarias' => 6,
                'segunda' => 1,
                'terca' => 1,
                'quarta' => 1,
                'quinta' => 1,
                'sexta' => 1,
            ])
            ->assertForbidden();

        $this->withSession(['usuario_id' => $supervisorId])
            ->get(route('estagios.show', $stage))
            ->assertOk()
            ->assertSee('Definir jornada');

        $this->withSession(['usuario_id' => $supervisorId])
            ->post(route('estagios.jornada.store', $stage), [
                'horas_diarias' => 6,
                'segunda' => 1,
                'terca' => 1,
                'quarta' => 1,
                'quinta' => 1,
                'sexta' => 1,
            ])
            ->assertRedirect(route('estagios.show', $stage));

        $this->assertSame(30.0, $stage->fresh()->jornada->cargaSemanal());

        $this->post(route('estagios.jornada.store', $stage), [
            'horas_diarias' => 4,
            'segunda' => 1,
        ])->assertSessionHasErrors('jornada');

        $attendanceResponse = $this->withSession(['usuario_id' => $alunoId])
            ->post(route('estagios.presencas.store', $stage), [
            'data' => today()->subDays(2)->toDateString(),
            'modo' => 'oficial',
            'horas' => 999,
        ]);
        $attendanceResponse->assertRedirect(route('estagios.show', $stage))
            ->assertSessionHasNoErrors();

        $presenca = Presenca::firstOrFail();
        $this->assertSame(Presenca::STATUS_PENDENTE, $presenca->status);
        $this->assertSame(360, $presenca->minutos);
        $this->assertNull($presenca->entrada);
        $this->assertNull($presenca->saida);
        $this->assertArrayNotHasKey('horas', $presenca->getAttributes());

        $this->withSession(['usuario_id' => $alunoId])
            ->post(route('estagios.presencas.store', $stage), [
                'modo' => 'oficial',
                'data' => today()->subDays(2)->toDateString(),
            ])->assertSessionHasErrors('data');

        $this->withSession(['usuario_id' => $alunoId])
            ->post(route('estagios.presencas.store', $stage), [
                'modo' => 'pessoal',
                'data' => today()->subDays(2)->toDateString(),
            ])->assertRedirect(route('estagios.show', $stage))
            ->assertSessionHasNoErrors();

        $presencaPessoalMesmoDia = Presenca::where('modo', Presenca::MODO_PESSOAL)->firstOrFail();
        $metricasComOficialPendente = app(\App\Services\EstagioService::class)
            ->metricas($stage->fresh(['jornada', 'presencas']));
        $this->assertSame(360, $metricasComOficialPendente['pessoais_contabilizadas_minutos']);
        $this->assertSame(360, $metricasComOficialPendente['horas_progresso_minutos']);
        $this->withSession(['usuario_id' => $alunoId])
            ->get(route('estagios.show', $stage))
            ->assertViewHas('calendarData', fn (array $calendarData) =>
                $calendarData['presencas']['2026-10-02'] === [
                    'modo' => Presenca::MODO_OFICIAL,
                    'status' => Presenca::STATUS_PENDENTE,
                ]
            );

        $this->withSession(['usuario_id' => $alunoId])
            ->post(route('estagios.presencas.store', $stage), [
                'modo' => 'pessoal',
                'data' => today()->subDays(2)->toDateString(),
            ])->assertSessionHasErrors('data');

        $this->post(route('estagios.presencas.store', $stage), [
            'modo' => 'oficial',
            'data' => today()->toDateString(),
        ])->assertSessionHasErrors('data');

        $this->withSession(['usuario_id' => $alunoId])
            ->get(route('estagios.show', $stage))
            ->assertOk()
            ->assertSee('6h')
            ->assertSee('Calendário de presenças')
            ->assertSee('Não oficial')
            ->assertSee('Oficial')
            ->assertSee('Aguardando validação')
            ->assertSee('style="width: 3%; min-width: 12px', false)
            ->assertSee('bg-blue-500" style="width: 100%"', false)
            ->assertDontSee('Hora de entrada')
            ->assertSee('2026-10-02');

        $this->withSession(['usuario_id' => $supervisorId])
            ->get(route('estagios.show', $stage))
            ->assertOk()
            ->assertSee('Aprovar')
            ->assertSessionHasNoErrors();

        $this->withSession(['usuario_id' => $orientadorId])
            ->post(route('presencas.aprovar', $presenca))
            ->assertForbidden();

        $this->withSession(['usuario_id' => $supervisorId])
            ->post(route('presencas.aprovar', $presenca))
            ->assertRedirect(route('estagios.show', $stage));

        $this->assertSame(Presenca::STATUS_APROVADA, $presenca->fresh()->status);
        $metricas = app(\App\Services\EstagioService::class)->metricas($stage->fresh(['jornada', 'presencas']));
        $this->assertSame(0, $metricas['pessoais_contabilizadas_minutos']);
        $this->assertSame(360, $metricas['horas_progresso_minutos']);
        $this->assertSame(1440, $metricas['previstas_semana_minutos']);
        $this->assertSame(360, $metricas['realizadas_semana_minutos']);
        $this->assertSame('-18h', $metricas['diferenca_semana_formatada']);

        $presencaPessoal = Presenca::create([
            'estagio_id' => $stage->id,
            'data' => today()->subDays(4)->toDateString(),
            'entrada' => '08:00',
            'saida' => '12:00',
            'modo' => 'pessoal',
            'status' => Presenca::STATUS_PESSOAL,
            'observacao' => 'Anotação privada do aluno.',
        ]);
        $metricasComPessoal = app(\App\Services\EstagioService::class)->metricas($stage->fresh(['jornada', 'presencas']));
        $this->assertSame($metricas['aprovadas_minutos'], $metricasComPessoal['aprovadas_minutos']);
        $this->assertSame(240, $metricasComPessoal['pessoais_contabilizadas_minutos']);
        $this->assertSame(600, $metricasComPessoal['horas_progresso_minutos']);
        $estagioComPresencasDesatualizadas = $stage->fresh(['jornada']);
        $estagioComPresencasDesatualizadas->setRelation('presencas', collect());
        $metricasLidasDoBanco = app(\App\Services\EstagioService::class)
            ->metricas($estagioComPresencasDesatualizadas);
        $this->assertSame(600, $metricasLidasDoBanco['horas_progresso_minutos']);

        $this->withSession(['usuario_id' => $supervisorId])
            ->get(route('estagios.show', $stage))
            ->assertOk()
            ->assertDontSee('Anotação privada do aluno.');

        $this->withSession(['usuario_id' => $orientadorId])
            ->get(route('estagios.show', $stage))
            ->assertOk()
            ->assertDontSee('Anotação privada do aluno.');

        $this->withSession(['usuario_id' => $alunoId])
            ->get(route('estagios.show', $stage))
            ->assertOk()
            ->assertSee('Anotação privada do aluno.')
            ->assertSee('Registros pessoais');

        $presencaRecusada = Presenca::create([
            'estagio_id' => $stage->id,
            'data' => today()->subDays(3)->toDateString(),
            'entrada' => '09:00',
            'saida' => '14:00',
            'status' => Presenca::STATUS_PENDENTE,
        ]);
        $presencaPessoalMesmoDiaRecusado = Presenca::create([
            'estagio_id' => $stage->id,
            'data' => today()->subDays(3)->toDateString(),
            'entrada' => '09:00',
            'saida' => '13:00',
            'modo' => Presenca::MODO_PESSOAL,
            'status' => Presenca::STATUS_PESSOAL,
        ]);
        $metricasComOficialPendente = app(\App\Services\EstagioService::class)
            ->metricas($stage->fresh(['jornada', 'presencas']));
        $this->assertSame(480, $metricasComOficialPendente['pessoais_contabilizadas_minutos']);
        $this->assertSame(840, $metricasComOficialPendente['horas_progresso_minutos']);

        $this->withSession(['usuario_id' => $supervisorId])
            ->post(route('presencas.recusar', $presencaRecusada), [
            'parecer_supervisor' => 'Atividade sem descrição suficiente.',
        ])->assertRedirect(route('estagios.show', $stage));

        $this->assertSame(Presenca::STATUS_RECUSADA, $presencaRecusada->fresh()->status);
        $this->assertSame('Atividade sem descrição suficiente.', $presencaRecusada->fresh()->parecer_supervisor);
        $metricasComOficialRecusado = app(\App\Services\EstagioService::class)
            ->metricas($stage->fresh(['jornada', 'presencas']));
        $this->assertSame(240, $metricasComOficialRecusado['pessoais_contabilizadas_minutos']);
        $this->assertSame(600, $metricasComOficialRecusado['horas_progresso_minutos']);
        $this->assertSame(Presenca::MODO_PESSOAL, $presencaPessoalMesmoDiaRecusado->fresh()->modo);
    }

    public function test_only_the_responsible_supervisor_can_review_attendance(): void
    {
        $alunoId = $this->createUser('aluno');
        $this->createUser('orientador');
        $supervisorResponsavel = $this->createUser('supervisor');
        $outroSupervisor = $this->createUser('supervisor');
        $stage = Estagio::create([
            'aluno_id' => $alunoId,
            'orientador_id' => 1,
            'supervisor_id' => 1,
            'local' => 'Laboratório de Sistemas',
            'data_inicio' => today()->subDays(3)->toDateString(),
            'data_fim' => today()->addDays(30)->toDateString(),
            'status' => Estagio::STATUS_ATIVO,
        ]);
        $presenca = Presenca::create([
            'estagio_id' => $stage->id,
            'data' => today()->subDays(1)->toDateString(),
            'entrada' => '08:00',
            'saida' => '14:00',
            'modo' => 'oficial',
            'status' => Presenca::STATUS_PENDENTE,
        ]);

        $this->withSession(['usuario_id' => $outroSupervisor])
            ->post(route('presencas.aprovar', $presenca))
            ->assertNotFound();

        $this->assertSame(Presenca::STATUS_PENDENTE, $presenca->fresh()->status);
        $this->assertNotSame($supervisorResponsavel, $outroSupervisor);
    }

    public function test_personal_attendance_works_without_supervisor_and_never_counts_as_official(): void
    {
        $alunoId = $this->createUser('aluno');
        $this->createUser('orientador');
        $stage = Estagio::create([
            'aluno_id' => $alunoId,
            'orientador_id' => 1,
            'local' => 'Laboratório de Sistemas',
            'data_inicio' => today()->subDays(5)->toDateString(),
            'data_fim' => today()->addDays(30)->toDateString(),
            'status' => Estagio::STATUS_ATIVO,
        ]);
        JornadaEstagio::create([
            'estagio_id' => $stage->id,
            'horas_diarias' => 6,
            'segunda' => true,
            'terca' => true,
            'quarta' => true,
            'quinta' => true,
            'sexta' => true,
        ]);

        $dadosPresenca = [
            'data' => today()->subDays(2)->toDateString(),
        ];

        $this->withSession(['usuario_id' => $alunoId])
            ->post(route('estagios.presencas.store', $stage), [...$dadosPresenca, 'modo' => 'oficial'])
            ->assertSessionHasErrors('modo');

        $this->post(route('estagios.presencas.store', $stage), [...$dadosPresenca, 'modo' => 'pessoal'])
            ->assertRedirect(route('estagios.show', $stage));

        $presencaPessoal = Presenca::firstOrFail();
        $this->assertSame(Presenca::MODO_PESSOAL, $presencaPessoal->modo);
        $this->assertSame(Presenca::STATUS_PESSOAL, $presencaPessoal->status);

        $metricas = app(\App\Services\EstagioService::class)->metricas($stage->fresh(['jornada', 'presencas']));
        $this->assertSame(0, $metricas['aprovadas_minutos']);
        $this->assertSame(0, $metricas['pendentes_minutos']);
    }

    public function test_attendance_accepts_start_date_and_rejects_dates_before_internship_period(): void
    {
        $alunoId = $this->createUser('aluno');
        $this->createUser('orientador');
        $stage = Estagio::create([
            'aluno_id' => $alunoId,
            'orientador_id' => 1,
            'local' => 'Laboratório de Sistemas',
            'data_inicio' => '2026-09-28',
            'data_fim' => '2026-10-05',
            'status' => Estagio::STATUS_ATIVO,
        ]);
        JornadaEstagio::create([
            'estagio_id' => $stage->id,
            'horas_diarias' => 6,
            'segunda' => true,
            'terca' => true,
            'quarta' => true,
            'quinta' => true,
            'sexta' => true,
        ]);

        $this->withSession(['usuario_id' => $alunoId])
            ->post(route('estagios.presencas.store', $stage), [
                'modo' => Presenca::MODO_PESSOAL,
                'data' => '2026-09-28',
            ])
            ->assertRedirect(route('estagios.show', $stage))
            ->assertSessionHasNoErrors();

        $this->post(route('estagios.presencas.store', $stage), [
            'modo' => Presenca::MODO_PESSOAL,
            'data' => '2026-09-27',
        ])->assertSessionHasErrors('data');

        $this->assertSame(1, Presenca::where('estagio_id', $stage->id)->count());
    }

    public function test_orientador_can_assign_legacy_stage_and_cannot_reassign_after_official_attendance(): void
    {
        $alunoId = $this->createUser('aluno');
        $orientadorId = $this->createUser('orientador');
        $this->createUser('supervisor');
        $this->createUser('supervisor');
        $stage = Estagio::create([
            'aluno_id' => $alunoId,
            'orientador_id' => 1,
            'local' => 'Empresa Teste',
            'data_inicio' => today()->subDays(5)->toDateString(),
            'data_fim' => today()->addDays(30)->toDateString(),
            'status' => Estagio::STATUS_ATIVO,
        ]);
        $presencaLegada = Presenca::create([
            'estagio_id' => $stage->id,
            'data' => today()->subDays(2)->toDateString(),
            'entrada' => '08:00',
            'saida' => '14:00',
            'modo' => Presenca::MODO_OFICIAL,
            'status' => Presenca::STATUS_PENDENTE,
        ]);

        $this->withSession(['usuario_id' => $orientadorId])
            ->post(route('estagios.supervisor.store', $stage), ['supervisor_id' => 1])
            ->assertRedirect(route('estagios.show', $stage));
        $this->assertSame(1, $stage->fresh()->supervisor_id);
        $this->assertSame(Presenca::STATUS_PENDENTE, $presencaLegada->fresh()->status);

        $this->withSession(['usuario_id' => $orientadorId])
            ->post(route('estagios.supervisor.store', $stage), ['supervisor_id' => 2])
            ->assertSessionHasErrors('supervisor_id');
        $this->assertSame(1, $stage->fresh()->supervisor_id);
    }

    public function test_admin_user_management_preserves_stage_history_and_supports_safe_edits(): void
    {
        $adminId = $this->createAdminUser();
        $alunoId = $this->createUser('aluno');
        $orientadorId = $this->createUser('orientador');
        $orientadorProfileId = (int) DB::table('orientadores')
            ->where('user_ID', $orientadorId)
            ->value('orientador_ID');
        $stage = Estagio::create([
            'aluno_id' => $alunoId,
            'orientador_id' => $orientadorProfileId,
            'local' => 'Estágio com histórico',
            'data_inicio' => today()->subDays(5)->toDateString(),
            'data_fim' => today()->addDays(30)->toDateString(),
            'status' => Estagio::STATUS_ATIVO,
        ]);

        $this->withSession(['usuario_id' => $adminId])
            ->get('/admin/usuarios')
            ->assertOk()
            ->assertSee('Gerenciar usuários')
            ->assertSee('busca-usuarios');

        $this->withSession(['usuario_id' => $adminId])
            ->get('/admin/usuarios/' . $orientadorId . '/edit')
            ->assertOk()
            ->assertSee('Carregando dados do usuário...')
            ->assertSee('Salvar alterações');

        $this->withSession(['usuario_id' => $adminId])
            ->getJson('/admin/api/usuarios?q=' . urlencode((string) DB::table('orientadores')
                ->where('user_ID', $orientadorId)
                ->value('siape')))
            ->assertOk()
            ->assertJsonFragment(['user_ID' => $orientadorId]);

        $this->withSession(['usuario_id' => $adminId])
            ->deleteJson('/admin/api/usuarios/' . $alunoId)
            ->assertStatus(409)
            ->assertJsonPath('message', 'Não é possível excluir este usuário porque ele está vinculado a estágios. Preserve a conta para manter o histórico.');
        $this->assertDatabaseHas('estagios', ['id' => $stage->id, 'aluno_id' => $alunoId]);
        $this->assertDatabaseHas('usuarios', ['user_ID' => $alunoId]);

        $this->withSession(['usuario_id' => $adminId])
            ->putJson('/admin/api/usuarios/' . $orientadorId, [
                'nome' => 'Orientador atualizado',
                'email' => 'orientador-atualizado@example.test',
                'tipo' => 'supervisor',
            ])
            ->assertStatus(409)
            ->assertJsonPath('message', 'Não é possível alterar o perfil deste usuário porque ele está vinculado a estágios. Preserve o perfil para manter o histórico.');
        $this->assertDatabaseHas('estagios', ['id' => $stage->id, 'orientador_id' => $orientadorProfileId]);

        $this->withSession(['usuario_id' => $adminId])
            ->putJson('/admin/api/usuarios/' . $orientadorId, [
                'nome' => 'Nome atualizado',
                'email' => 'nome-atualizado@example.test',
                'tipo' => 'orientador',
                'siape' => str_pad((string) (10000000 + $orientadorId), 8, '0', STR_PAD_LEFT),
            ])
            ->assertOk()
            ->assertJsonPath('Nome', 'Nome atualizado');

        $supervisorId = $this->createUser('supervisor');
        $this->withSession(['usuario_id' => $adminId])
            ->deleteJson('/admin/api/usuarios/' . $supervisorId)
            ->assertOk()
            ->assertJsonPath('message', 'Usuário excluído.');
        $this->assertDatabaseMissing('supervisores', ['user_ID' => $supervisorId]);
        $this->assertDatabaseMissing('usuarios', ['user_ID' => $supervisorId]);
    }

    public function test_admin_can_clear_portal_data_while_preserving_admin_login(): void
    {
        $adminId = $this->createAdminUser();
        $alunoId = $this->createUser('aluno');
        $orientadorId = $this->createUser('orientador');
        $orientadorProfileId = (int) DB::table('orientadores')
            ->where('user_ID', $orientadorId)
            ->value('orientador_ID');
        $stage = Estagio::create([
            'aluno_id' => $alunoId,
            'orientador_id' => $orientadorProfileId,
            'local' => 'Estágio para limpeza',
            'data_inicio' => today()->subDays(5)->toDateString(),
            'data_fim' => today()->addDays(30)->toDateString(),
            'status' => Estagio::STATUS_ATIVO,
        ]);
        DB::table('jornadas_estagio')->insert([
            'estagio_id' => $stage->id,
            'horas_diarias' => 6,
            'segunda' => true,
            'terca' => true,
            'quarta' => true,
            'quinta' => true,
            'sexta' => true,
            'sabado' => false,
            'domingo' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('presencas')->insert([
            'estagio_id' => $stage->id,
            'data' => today()->subDays(2)->toDateString(),
            'modo' => 'oficial',
            'status' => 'aprovada',
            'entrada' => '08:00',
            'saida' => '14:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('empresas_conveniadas')->insert([
            'empresa' => 'Empresa para limpeza',
            'cnpj' => '00000000000199',
            'vig_inicial' => today()->toDateString(),
            'vig_final' => today()->addYear()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->withSession(['usuario_id' => $alunoId])
            ->deleteJson('/admin/api/dados', ['confirmacao' => 'APAGAR TODOS OS DADOS'])
            ->assertForbidden();
        $this->assertDatabaseHas('estagios', ['id' => $stage->id]);

        $this->withSession(['usuario_id' => $adminId])
            ->deleteJson('/admin/api/dados', ['confirmacao' => 'APAGAR'])
            ->assertUnprocessable();
        $this->assertDatabaseHas('estagios', ['id' => $stage->id]);

        $this->withSession(['usuario_id' => $adminId])
            ->deleteJson('/admin/api/dados', ['confirmacao' => 'APAGAR TODOS OS DADOS'])
            ->assertOk()
            ->assertJsonPath('message', 'Os dados do portal foram apagados. O login do administrador foi preservado.')
            ->assertJsonPath('apagados.usuarios', 2)
            ->assertJsonPath('apagados.estagios', 1)
            ->assertJsonPath('apagados.presencas', 1);

        $this->assertSame(1, Usuario::count());
        $this->assertDatabaseHas('usuarios', [
            'user_ID' => $adminId,
            'Email' => 'admin@example.test',
        ]);
        foreach ([
            'alunos',
            'orientadores',
            'supervisores',
            'estagios',
            'jornadas_estagio',
            'presencas',
            'empresas_conveniadas',
        ] as $table) {
            $this->assertDatabaseCount($table, 0);
        }
    }

    private function createExistingProjectSchema(): void
    {
        Schema::connection('sqlite')->create('atribuicoes', function (Blueprint $table) {
            $table->increments('Atribuicao_ID');
            $table->string('Nome_atribuicao', 45);
        });

        Schema::connection('sqlite')->create('usuarios', function (Blueprint $table) {
            $table->increments('user_ID');
            $table->string('Nome', 60);
            $table->string('Email', 45)->unique();
            $table->string('Senha', 255);
            $table->string('cpf', 14)->nullable();
            $table->unsignedInteger('atribuicao_id')->nullable();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('empresas_conveniadas', function (Blueprint $table) {
            $table->id();
            $table->string('empresa');
            $table->string('cnpj')->unique();
            $table->date('vig_inicial');
            $table->date('vig_final');
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('alunos', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->primary();
            $table->string('nome', 60);
            $table->string('curso', 100);
            $table->string('matricula', 30)->unique();
            $table->unsignedInteger('orientador')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('user_ID')->on('usuarios');
        });

        Schema::connection('sqlite')->create('orientadores', function (Blueprint $table) {
            $table->increments('orientador_ID');
            $table->unsignedInteger('user_ID')->unique();
            $table->string('siape', 20)->unique();
            $table->string('area', 100)->nullable();
            $table->timestamps();
            $table->foreign('user_ID')->references('user_ID')->on('usuarios');
        });

        Schema::connection('sqlite')->create('supervisores', function (Blueprint $table) {
            $table->increments('supervisor_ID');
            $table->unsignedInteger('user_ID')->unique();
            $table->string('cargo', 100)->nullable();
            $table->string('empresa', 150)->nullable();
            $table->timestamps();
            $table->foreign('user_ID')->references('user_ID')->on('usuarios');
        });
    }

    private function createUser(string $tipo): int
    {
        $atribuicaoId = DB::table('atribuicoes')
            ->where('Nome_atribuicao', ucfirst($tipo))
            ->value('Atribuicao_ID');
        $email = $tipo . DB::table('usuarios')->count() . '@example.test';
        $userId = DB::table('usuarios')->insertGetId([
            'Nome' => ucfirst($tipo) . ' Teste',
            'Email' => $email,
            'Senha' => bcrypt('segredo'),
            'atribuicao_id' => $atribuicaoId,
        ], 'user_ID');

        if ($tipo === 'aluno') {
            DB::table('alunos')->insert([
                'user_id' => $userId,
                'nome' => 'Aluno ' . $userId,
                'curso' => 'Informatica',
                'matricula' => 'MAT' . $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } elseif ($tipo === 'supervisor') {
            DB::table('supervisores')->insert([
                'user_ID' => $userId,
                'empresa' => 'Empresa Teste',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('orientadores')->insert([
                'user_ID' => $userId,
                'siape' => str_pad((string) (10000000 + $userId), 8, '0', STR_PAD_LEFT),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return (int) $userId;
    }

    private function createAdminUser(): int
    {
        return (int) DB::table('usuarios')->insertGetId([
            'Nome' => 'Administrador de teste',
            'Email' => 'admin@example.test',
            'Senha' => bcrypt('segredo'),
            'atribuicao_id' => DB::table('atribuicoes')
                ->where('Nome_atribuicao', 'Administrador')
                ->value('Atribuicao_ID'),
        ], 'user_ID');
    }
}