<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('anggota_penugasans', function (Blueprint $table) {
            // Kolom untuk fitur "Ajukan Buka Laporan"
            $table->string('status_keterlambatan')->nullable()->after('id_user')->comment('mengajukan, disetujui, ditolak');
            $table->text('alasan_keterlambatan')->nullable()->after('status_keterlambatan');
            $table->dateTime('custom_deadline')->nullable()->after('alasan_keterlambatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anggota_penugasans', function (Blueprint $table) {
            $table->dropColumn(['status_keterlambatan', 'alasan_keterlambatan', 'custom_deadline']);
        });
    }
};