<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sertifikasi_alumni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_alumni_id')->constrained('data_alumni')->onDelete('cascade');
            $table->string('nama_sertifikasi');
            $table->string('lembaga_penerbit');
            $table->year('tahun_terbit');
            $table->enum('kategori', [
                'kompetensi',
                'bahasa',
                'teknologi',
                'profesi',
                'lainnya',
            ])->default('lainnya');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sertifikasi_alumni');
    }
};
