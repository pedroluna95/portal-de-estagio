<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presencas', function (Blueprint $table) {
            if (! Schema::hasColumn('presencas', 'horas')) {
                $table->decimal('horas', 5, 2)->nullable()->after('data');
            }

            if (! Schema::hasColumn('presencas', 'modo')) {
                $table->string('modo', 20)->default('oficial')->after('status');
            }

            if (! Schema::hasColumn('presencas', 'parecer_supervisor')) {
                $table->string('parecer_supervisor', 500)->nullable()->after('parecer_orientador');
            }
        });

        if (Schema::hasColumn('presencas', 'minutos')) {
            DB::statement('UPDATE presencas SET horas = minutos / 60 WHERE minutos IS NOT NULL');

            Schema::table('presencas', function (Blueprint $table) {
                $table->dropColumn('minutos');
            });
        }
    }

    public function down(): void
    {
        Schema::table('presencas', function (Blueprint $table) {
            if (Schema::hasColumn('presencas', 'parecer_supervisor')) {
                $table->dropColumn('parecer_supervisor');
            }

            if (Schema::hasColumn('presencas', 'modo')) {
                $table->dropColumn('modo');
            }

            if (Schema::hasColumn('presencas', 'horas')) {
                $table->dropColumn('horas');
            }

            if (! Schema::hasColumn('presencas', 'minutos')) {
                $table->unsignedSmallInteger('minutos')->nullable()->after('data');
            }
        });
    }
};
