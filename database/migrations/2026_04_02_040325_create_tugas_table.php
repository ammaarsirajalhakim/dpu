<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tugas', function (Blueprint $table) {
            $table->string('kodetugas', 10)->primary(); 
            $table->string('nama_tugas');
            $table->text('deskripsi')->nullable();
            
            // TAMBAHAN: Kolom yang sebelumnya terlewat
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('lampiran')->nullable(); // nullable karena file tidak selalu wajib ada
            
            $table->string('id_admin'); 
            $table->foreign('id_admin')->references('nip')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};