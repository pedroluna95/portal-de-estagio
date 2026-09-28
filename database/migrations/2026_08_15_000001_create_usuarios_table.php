<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->increments('user_ID');

            $table->string('cpf', 14)->nullable();
            $table->string('Nome', 60);
            $table->string('Email', 45)->unique();
            $table->string('Senha', 255);
            $table->string('atribuicao', 45)->nullable();
        });

        // Administrador padrão
        DB::table('usuarios')->insert([
            'cpf' => null,
            'Nome' => 'Administrador',
            'Email' => 'admin@admin.com',
            'Senha' => Hash::make('cefet123'),
            'atribuicao' => null,
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};