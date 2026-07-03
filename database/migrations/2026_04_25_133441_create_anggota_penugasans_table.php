<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota_penugasans', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel penugasan (tetap menggunakan ID auto-increment dari tabel penugasan)
            $table->unsignedBigInteger('id_penugasan');
            $table->foreign('id_penugasan')->references('id')->on('penugasan')->onDelete('cascade');

            // 1. Mengubah tipe data id_user menjadi string untuk menampung NIP
            $table->string('id_user');
            // 2. Mereferensikan ke kolom nip di tabel users
            $table->foreign('id_user')->references('nip')->on('users')->onDelete('cascade');

            // id_jabatan beserta foreign key-nya telah dihapus sesuai struktur asli Anda

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_penugasans');
    }
};