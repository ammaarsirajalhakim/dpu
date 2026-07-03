<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_progress_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_penugasan')->constrained('penugasan')->cascadeOnDelete();
            $table->string('id_user', 20);
            $table->date('tanggal_laporan');
            $table->text('progres')->nullable();
            $table->text('kendala')->nullable();
            $table->text('rencana_lanjut')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->timestamps();

            $table->unique(['id_penugasan', 'id_user', 'tanggal_laporan'], 'daily_progress_unique');
            $table->index(['id_user', 'tanggal_laporan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_progress_reports');
    }
};
