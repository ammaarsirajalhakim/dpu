<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_files', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('id_laporan');
            $table->foreign('id_laporan')->references('id')->on('laporans')->onDelete('cascade');

            // Tempat menyimpan nama asli dan letak direktori file
            $table->string('file_name');
            $table->string('file_path');

            // Kita memanfaatkan created_at bawaan timestamps sebagai penanda waktu upload
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_files');
    }
};