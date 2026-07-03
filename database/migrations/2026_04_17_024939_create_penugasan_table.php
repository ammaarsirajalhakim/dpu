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
        Schema::create('penugasan', function (Blueprint $table) {
            $table->id(); // Primary Key tabel penugasan tetap auto-increment

            // Relasi ke tabel tugas (kodetugas tetap string)
            $table->string('kodetugas', 10);
            $table->foreign('kodetugas')->references('kodetugas')->on('tugas')->onDelete('cascade');

            // 1. Mengubah tipe data id_admin menjadi string untuk menampung NIP
            $table->string('id_admin');
            $table->foreign('id_admin')->references('nip')->on('users')->onDelete('cascade');

            // 2. Mengubah tipe data id_penerima menjadi string untuk menampung NIP
            // Catatan: Jika Anda menggunakan migration 'remove_id_penerima', kolom ini mungkin tidak diperlukan lagi.
            $table->string('id_penerima')->nullable();
            $table->foreign('id_penerima')->references('nip')->on('users')->onDelete('cascade');

            $table->date('batas_waktu_lapor'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penugasan');
    }
};