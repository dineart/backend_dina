<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('KEUANGAN_MAHASISWA', function (Blueprint $table) {
            $table->string('ID_KEUANGAN_MHS', 20)->primary();
            $table->string('ID_KATEGORI', 20);
            $table->string('ID_MAHASISWA', 36)->nullable(); // UUID dari kelompok 3
            $table->string('SEMESTER', 15)->nullable();
            $table->string('BEASISWA', 20)->nullable();
            $table->string('STATUS_AKTIF', 20)->nullable();

            $table->foreign('ID_KATEGORI')
                  ->references('ID_KATEGORI')
                  ->on('KATEGORI_UKT')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('KEUANGAN_MAHASISWA');
    }
};