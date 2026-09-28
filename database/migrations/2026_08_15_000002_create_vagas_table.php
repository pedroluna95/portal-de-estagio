<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vagas', function (Blueprint $table) {
            $table->integer('Vaga_ID')->primary();
            $table->string('Nome_vaga', 60);
            $table->string('Desc_vaga', 45);
            $table->string('Area_de_atuacao', 45);
            $table->string('Requisitos', 150);
            $table->string('Empresa', 45);
            $table->string('Atuacao', 45);
 
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('vagas');
    }
};


