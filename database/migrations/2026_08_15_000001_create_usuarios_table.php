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

            $table->unsignedInteger('atribuicao_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('atribuicao_id')
                ->references('Atribuicao_ID')
                ->on('atribuicoes')
                ->onDelete('set null');
});

        // Administrador padrão
        DB::table('usuarios')->insert([
            'cpf' => '000.000.000-00',
            'Nome' => 'Administrador',
            'Email' => 'admin@admin.com',
            'Senha' => Hash::make('cefet123'),
            'atribuicao_id' => 1,
            'created_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};