<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jornadas_estagio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estagio_id')
                ->unique()
                ->constrained('estagios')
                ->restrictOnDelete();
            $table->decimal('horas_diarias', 4, 2);
            $table->boolean('segunda')->default(false);
            $table->boolean('terca')->default(false);
            $table->boolean('quarta')->default(false);
            $table->boolean('quinta')->default(false);
            $table->boolean('sexta')->default(false);
            $table->boolean('sabado')->default(false);
            $table->boolean('domingo')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jornadas_estagio');
    }
};