<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supervisores', function (Blueprint $table) {
            $table->increments('supervisor_ID');

            $table->unsignedInteger('user_ID')->unique();

            $table->foreign('user_ID')
                ->references('user_ID')
                ->on('usuarios')
                ->onDelete('cascade');

            $table->string('cargo', 100)->nullable();
            $table->string('empresa', 150)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supervisores');
    }
};
