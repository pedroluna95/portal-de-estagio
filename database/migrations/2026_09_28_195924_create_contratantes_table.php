<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratantes', function (Blueprint $table) {
            $table->increments('contratante_ID');

            $table->unsignedInteger('user_ID')->unique();

            $table->foreign('user_ID')
                ->references('user_ID')
                ->on('usuarios')
                ->onDelete('cascade');

            $table->string('empresa', 150);
            $table->string('cnpj', 18)->unique();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratantes');
    }
};

