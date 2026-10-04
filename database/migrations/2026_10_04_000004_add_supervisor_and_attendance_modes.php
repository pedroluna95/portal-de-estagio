<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estagios', function (Blueprint $table) {
            $table->unsignedInteger('supervisor_id')->nullable()->after('orientador_id');
            $table->foreign('supervisor_id')
                ->references('supervisor_ID')
                ->on('supervisores')
                ->nullOnDelete();
        });

        Schema::table('presencas', function (Blueprint $table) {
            $table->string('modo', 20)->default('oficial')->after('status');
            $table->string('parecer_supervisor', 500)->nullable()->after('parecer_orientador');
        });
    }

    public function down(): void
    {
        Schema::table('presencas', function (Blueprint $table) {
            $table->dropColumn(['modo', 'parecer_supervisor']);
        });

        Schema::table('estagios', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn('supervisor_id');
        });
    }
};