<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('daily_progress_reports')) {
            Schema::table('daily_progress_reports', function (Blueprint $table) {
                if (!Schema::hasColumn('daily_progress_reports', 'file_path')) {
                    $table->string('file_path')->nullable()->after('rencana_lanjut');
                }

                if (!Schema::hasColumn('daily_progress_reports', 'file_name')) {
                    $table->string('file_name')->nullable()->after('file_path');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('daily_progress_reports')) {
            Schema::table('daily_progress_reports', function (Blueprint $table) {
                if (Schema::hasColumn('daily_progress_reports', 'file_name')) {
                    $table->dropColumn('file_name');
                }

                if (Schema::hasColumn('daily_progress_reports', 'file_path')) {
                    $table->dropColumn('file_path');
                }
            });
        }
    }
};
