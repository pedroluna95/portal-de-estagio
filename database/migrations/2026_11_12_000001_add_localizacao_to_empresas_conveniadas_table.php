<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas_conveniadas', function (Blueprint $table) {
            $table->string('municipio', 120)->nullable()->after('vig_final');
            $table->char('uf', 2)->nullable()->after('municipio');
            $table->string('cep', 9)->nullable()->after('uf');
            $table->string('logradouro', 255)->nullable()->after('cep');
            $table->string('numero', 40)->nullable()->after('logradouro');
            $table->string('complemento', 120)->nullable()->after('numero');
            $table->string('bairro', 120)->nullable()->after('complemento');
            $table->string('natureza_juridica', 120)->nullable()->after('bairro');
            $table->boolean('localizacao_verificada')->default(false)->after('natureza_juridica');
            $table->timestamp('localizacao_verificada_em')->nullable()->after('localizacao_verificada');
        });
    }

    public function down(): void
    {
        Schema::table('empresas_conveniadas', function (Blueprint $table) {
            $table->dropColumn([
                'municipio',
                'uf',
                'cep',
                'logradouro',
                'numero',
                'complemento',
                'bairro',
                'natureza_juridica',
                'localizacao_verificada',
                'localizacao_verificada_em',
            ]);
        });
    }
};
