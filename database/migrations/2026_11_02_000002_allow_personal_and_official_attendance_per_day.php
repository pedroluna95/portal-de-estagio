<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presencas', function (Blueprint $table) {
            $table->dropUnique(['estagio_id', 'data']);
            $table->unique(['estagio_id', 'data', 'modo']);
        });
    }

    public function down(): void
    {
        Schema::table('presencas', function (Blueprint $table) {
            $table->dropUnique(['estagio_id', 'data', 'modo']);
            $table->unique(['estagio_id', 'data']);
        });
    }
};
