<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('presencas', 'minutos') && ! Schema::hasColumn('presencas', 'horas')) {
            DB::statement('UPDATE presencas SET horas = minutos / 60 WHERE minutos IS NOT NULL');
        }

        Schema::table('presencas', function (Blueprint $table) {
            if (! Schema::hasColumn('presencas', 'horas')) {
                $table->decimal('horas', 5, 2)->nullable()->after('data');
            }

            if (Schema::hasColumn('presencas', 'minutos')) {
                $table->dropColumn('minutos');
            }

            $table->time('entrada')->nullable()->change();
            $table->time('saida')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('presencas', function (Blueprint $table) {
            if (! Schema::hasColumn('presencas', 'minutos')) {
                $table->unsignedSmallInteger('minutos')->nullable()->after('data');
            }

            if (Schema::hasColumn('presencas', 'horas')) {
                $table->dropColumn('horas');
            }
        });
    }
};
