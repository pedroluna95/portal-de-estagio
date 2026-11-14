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
            $table->decimal('horas', 5, 2)->nullable();
            $table->time('entrada')->nullable();
            $table->time('saida')->nullable();
            $table->string('status', 20)->default('pendente');
            $table->string('modo', 20)->default('oficial');
            $table->string('observacao', 500)->nullable();
            $table->string('parecer_orientador', 500)->nullable();
            $table->string('parecer_supervisor', 500)->nullable();
            $table->timestamps();

            $table->unique(['estagio_id', 'data', 'modo']);
            $table->index(['estagio_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presencas');
    }
};
