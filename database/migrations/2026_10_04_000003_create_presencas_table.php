<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presencas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estagio_id')
                ->constrained('estagios')
                ->restrictOnDelete();
            $table->date('data');
            $table->time('entrada');
            $table->time('saida');
            $table->string('status', 20)->default('pendente');
            $table->string('observacao', 500)->nullable();
            $table->string('parecer_orientador', 500)->nullable();
            $table->timestamps();

            $table->unique(['estagio_id', 'data']);
            $table->index(['estagio_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presencas');
    }
};