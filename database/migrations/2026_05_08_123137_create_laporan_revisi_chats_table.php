<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_revisi_chats', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel laporans (tetap menggunakan ID integer sesuai laporans_table)
            $table->unsignedBigInteger('id_laporan');
            $table->foreign('id_laporan')->references('id')->on('laporans')->onDelete('cascade');

            // 1. Perubahan: Tipe data id_user diubah menjadi string agar sesuai dengan NIP
            $table->string('id_user');
            
            // 2. Perubahan: Referensi diubah dari 'id' ke 'nip' pada tabel users
            $table->foreign('id_user')->references('nip')->on('users')->onDelete('cascade');

            $table->text('pesan');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_revisi_chats');
    }
};