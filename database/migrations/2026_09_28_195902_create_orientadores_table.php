<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orientadores', function (Blueprint $table) {
            $table->increments('orientador_ID');

            $table->unsignedInteger('user_ID')->unique();

            $table->foreign('user_ID')
                ->references('user_ID')
                ->on('usuarios')
                ->onDelete('cascade');

            $table->string('siape', 20)->unique();
            $table->string('area', 100)->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orientadores');
    }
};
