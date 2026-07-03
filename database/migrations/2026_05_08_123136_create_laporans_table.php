<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel penugasan
            $table->unsignedBigInteger('id_penugasan');
            $table->foreign('id_penugasan')->references('id')->on('penugasan')->onDelete('cascade');

            // Status alur kerja laporan
            $table->enum('status', ['pending', 'diajukan', 'revisi', 'disetujui'])->default('pending');
            
            // Keterangan teks saat anggota mengajukan (bisa kosong jika hanya kirim file)
            $table->text('teks_laporan')->nullable();
            
            // Akan diisi path file jika admin sudah memilih file final
            $table->string('file_final_path')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};