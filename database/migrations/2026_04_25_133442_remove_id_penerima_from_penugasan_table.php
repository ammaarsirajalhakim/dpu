<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penugasan', function (Blueprint $table) {
            // Hapus constraint foreign key dulu, baru kolomnya
            $table->dropForeign(['id_penerima']);
            $table->dropColumn('id_penerima');
        });
    }

    public function down(): void
    {
        Schema::table('penugasan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_penerima')->nullable();
            $table->foreign('id_penerima')->references('id')->on('users')->onDelete('cascade');
        });
    }
};