<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alunos', function (Blueprint $table) {
            $table->increments('aluno_ID');

            $table->unsignedInteger('user_ID')->unique();

            $table->foreign('user_ID')
                ->references('user_ID')
                ->on('usuarios')
                ->onDelete('cascade');

            $table->string('matricula', 30)->unique();
            $table->string('curso', 100)->nullable();
            $table->string('periodo', 20)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alunos');
    }
};
