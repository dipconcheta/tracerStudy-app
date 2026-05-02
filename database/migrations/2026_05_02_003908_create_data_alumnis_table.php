<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_alumni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('User_id')->constrained('User')->onDelete('cascade');

            // ── Identitas Pribadi ──────────────────────────────────────────
            $table->string('nomor_induk_siswa')->unique();
            $table->string('nisn')->unique()->nullable();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan']);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('agama')->nullable();
            $table->enum('status_pernikahan', ['belum_menikah', 'menikah', 'cerai'])->default('belum_menikah');
            $table->string('kebangsaan')->default('Indonesia');

            // ── Kontak ─────────────────────────────────────────────────────
            $table->string('nomor_telepon', 20);
            $table->string('email_aktif');
            $table->text('alamat_sekarang');
            $table->string('kota_sekarang');
            $table->string('provinsi_sekarang');
            $table->string('kode_pos', 10)->nullable();

            // ── Riwayat Sekolah ────────────────────────────────────────────
            $table->string('jurusan_saat_sekolah');
            $table->year('tahun_masuk');
            $table->year('tahun_lulus');
            $table->enum('status_kelulusan', ['lulus', 'tidak_lulus', 'pindah'])->default('lulus');

            // ── Status Setelah Lulus ───────────────────────────────────────
            $table->enum('status_saat_ini', [
                'bekerja',
                'wirausaha',
                'kuliah',
                'kuliah_dan_bekerja',
                'belum_bekerja',
                'tidak_melanjutkan',
            ]);
            $table->integer('lama_tunggu_bulan')->nullable()->comment('Lama tunggu kerja/kuliah dalam bulan');

            // ── Data Pekerjaan (jika bekerja / wirausaha) ──────────────────
            $table->string('nama_perusahaan')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('bidang_pekerjaan')->nullable();
            $table->enum('jenis_pekerjaan', [
                'pns',
                'swasta',
                'bumn',
                'tni_polri',
                'wirausaha',
                'freelance',
                'lainnya',
            ])->nullable();
            $table->enum('kesesuaian_bidang', ['sesuai', 'tidak_sesuai', 'sebagian_sesuai'])->nullable();
            $table->enum('skala_gaji_bulanan', [
                'di_bawah_1jt',
                '1jt_2jt',
                '2jt_3jt',
                '3jt_5jt',
                'di_atas_5jt',
            ])->nullable();
            $table->string('kota_bekerja')->nullable();
            $table->string('provinsi_bekerja')->nullable();

            // ── Data Pendidikan Lanjut (jika kuliah) ──────────────────────
            $table->string('nama_perguruan_tinggi')->nullable();
            $table->string('program_studi')->nullable();
            $table->enum('jenjang_kuliah', ['d1', 'd2', 'd3', 'd4', 's1', 's2', 's3'])->nullable();
            $table->enum('jalur_masuk_pt', [
                'snbp',
                'snbt',
                'mandiri',
                'beasiswa',
                'lainnya',
            ])->nullable();
            $table->year('tahun_masuk_pt')->nullable();
            $table->string('kota_perguruan_tinggi')->nullable();

            // ── Penilaian Alumni ───────────────────────────────────────────
            $table->tinyInteger('nilai_kompetensi')->nullable()->comment('Skala 1-5');
            $table->tinyInteger('nilai_soft_skill')->nullable()->comment('Skala 1-5');
            $table->tinyInteger('nilai_fasilitas_sekolah')->nullable()->comment('Skala 1-5');
            $table->tinyInteger('nilai_kepuasan_sekolah')->nullable()->comment('Skala 1-5: 1=sangat tidak puas, 5=sangat puas');
            $table->text('saran_untuk_sekolah')->nullable();

            // ── Meta ───────────────────────────────────────────────────────
            $table->timestamp('diisi_pada')->nullable();
            $table->boolean('sudah_diverifikasi')->default(false);
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('User');
            $table->timestamp('diverifikasi_pada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_alumni');
    }
};
