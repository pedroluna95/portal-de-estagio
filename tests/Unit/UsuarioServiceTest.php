<?php

namespace Tests\Unit;

use Tests\TestCase;

class UsuarioServiceTest extends TestCase
{
    
    public function test_formatCpf_valid()
    {
        $svc = \App\Services\UsuarioService::class;
        $result = $svc::formatCpf('12345678910');
        $this->assertEquals('123.456.789-10', $result);

        $result2 = $svc::formatCpf('123.456.789-10');
        $this->assertEquals('123.456.789-10', $result2);
    }

    public function test_formatCpf_invalid()
    {
        $svc = \App\Services\UsuarioService::class;
        $this->assertNull($svc::formatCpf(''));
        $this->assertNull($svc::formatCpf('12345'));
        $this->assertNull($svc::formatCpf('abc.def.ghi-jk'));
    }

    public function test_model_classes_exist_and_extend_usuario()
    {
        $models = ['Aluno','Orientador','Supervisor','Administrador'];
        foreach ($models as $m) {
            $class = "App\\Models\\$m";
            $this->assertTrue(class_exists($class), "Classe $class não existe");
            $this->assertTrue(is_subclass_of($class, \App\Models\Usuario::class), "$class não estende Usuario");
        }
    }

    public function test_aluno_accepts_the_columns_required_by_its_table()
    {
        $aluno = new \App\Models\Aluno;
        $aluno->fill([
            'user_id' => 42,
            'nome' => 'Pessoa Teste',
            'curso' => 'Informatica',
            'matricula' => '123456',
        ]);

        $this->assertSame('user_id', $aluno->getKeyName());
        $this->assertFalse($aluno->getIncrementing());
        $this->assertSame(42, $aluno->getAttribute('user_id'));
        $this->assertSame('Pessoa Teste', $aluno->getAttribute('nome'));
    }

    public function test_jornada_calculates_weekly_hours_from_selected_days()
    {
        $jornada = new \App\Models\JornadaEstagio;
        $jornada->fill([
            'horas_diarias' => 6,
            'segunda' => true,
            'terca' => true,
            'quarta' => true,
            'quinta' => true,
            'sexta' => true,
            'sabado' => false,
            'domingo' => false,
        ]);

        $this->assertSame(30.0, $jornada->cargaSemanal());
        $this->assertTrue($jornada->trabalhaNoDia(1));
        $this->assertFalse($jornada->trabalhaNoDia(7));
    }

    public function test_presenca_derives_duration_from_entry_and_exit()
    {
        $presenca = new \App\Models\Presenca;
        $presenca->setRawAttributes([
            'entrada' => '08:00:00',
            'saida' => '13:30:00',
        ]);

        $this->assertSame(330, $presenca->minutos);
        $this->assertSame(5.5, $presenca->horas);
    }
}
