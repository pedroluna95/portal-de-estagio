<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presencas', function (Blueprint $table) {
            $table->unsignedSmallInteger('minutos')->nullable()->after('data');
            $table->time('entrada')->nullable()->change();
            $table->time('saida')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('presencas', function (Blueprint $table) {
            $table->dropColumn('minutos');
        });
    }
};
