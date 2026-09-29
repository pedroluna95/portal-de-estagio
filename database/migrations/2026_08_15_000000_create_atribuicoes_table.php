<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atribuicoes', function (Blueprint $table) {
            $table->increments('Atribuicao_ID')->primary();
            $table->string('Nome_atribuicao', 45);
        });

        DB::table('atribuicoes')->insert([
            ['Nome_atribuicao' => 'Administrador'],
            ['Nome_atribuicao' => 'Aluno'],
            ['Nome_atribuicao' => 'Orientador'],
            ['Nome_atribuicao' => 'Supervisor'],
        ]);

    }

    public function down(): void
    {
        Schema::dropIfExists('atribuicoes');
    }
};