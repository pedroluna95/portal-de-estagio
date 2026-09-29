<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('alunos');

        Schema::create('alunos', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->primary();
            $table->string('nome', 60);
            $table->string('curso', 100);
            $table->string('matricula', 30)->unique();
            $table->unsignedInteger('orientador')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('user_ID')
                ->on('usuarios')
                ->onDelete('cascade');

            $table->foreign('orientador')
                ->references('user_ID')
                ->on('usuarios')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alunos');
    }
};
