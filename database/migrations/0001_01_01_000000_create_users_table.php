<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('User', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('email')->unique();
            $table->string('kata_sandi');
            $table->enum('peran', ['admin', 'alumni'])->default('alumni');
            $table->boolean('aktif')->default(true);
            $table->timestamp('email_terverifikasi_pada')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('User');
    }
};
