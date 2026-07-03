<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('laporans') && !Schema::hasColumn('laporans', 'user_id')) {
            Schema::table('laporans', function (Blueprint $table) {
                $table->string('user_id', 20)->nullable()->after('id_penugasan');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('laporans') && Schema::hasColumn('laporans', 'user_id')) {
            Schema::table('laporans', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }
    }
};
