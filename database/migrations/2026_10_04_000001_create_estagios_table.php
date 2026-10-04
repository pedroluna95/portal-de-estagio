<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estagios', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('aluno_id');
            $table->unsignedInteger('orientador_id');
            $table->string('local', 150);
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->string('status', 20)->default('ativo');
            $table->timestamps();

            $table->foreign('aluno_id')
                ->references('user_id')
                ->on('alunos')
                ->restrictOnDelete();

            $table->foreign('orientador_id')
                ->references('orientador_ID')
                ->on('orientadores')
                ->restrictOnDelete();

            $table->index(['aluno_id', 'status']);
            $table->index(['orientador_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estagios');
    }
};