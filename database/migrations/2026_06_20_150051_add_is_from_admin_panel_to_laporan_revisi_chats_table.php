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
        Schema::table('laporan_revisi_chats', function (Blueprint $table) {
            // Penanda apakah pesan dikirim dari panel admin (1) atau panel user (0)
            $table->boolean('is_from_admin_panel')->default(false)->after('pesan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_revisi_chats', function (Blueprint $table) {
            $table->dropColumn('is_from_admin_panel');
        });
    }
};