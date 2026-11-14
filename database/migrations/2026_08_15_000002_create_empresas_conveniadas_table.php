<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresas_conveniadas', function (Blueprint $table) {
            $table->id();
            $table->string('empresa');
            $table->string('cnpj', 30)->unique();
            $table->date('vig_inicial');
            $table->date('vig_final');

            // Dados de localização obtidos via BrasilAPI / Receita Federal
            $table->string('municipio', 120)->nullable();
            $table->char('uf', 2)->nullable();
            $table->string('cep', 9)->nullable();
            $table->string('logradouro', 255)->nullable();
            $table->string('numero', 40)->nullable();
            $table->string('complemento', 120)->nullable();
            $table->string('bairro', 120)->nullable();
            $table->string('natureza_juridica', 120)->nullable();
            $table->boolean('localizacao_verificada')->default(false);
            $table->timestamp('localizacao_verificada_em')->nullable();

            $table->timestamps();

            $table->index(['uf', 'municipio']);
            $table->index('localizacao_verificada');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas_conveniadas');
    }
};
