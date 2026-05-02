<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class DataAlumni extends Model
{
    use HasFactory;

    protected $table = 'data_alumni';

    protected $fillable = [
        'User_id',
        'nomor_induk_siswa',
        'nisn',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'status_pernikahan',
        'kebangsaan',
        'nomor_telepon',
        'email_aktif',
        'alamat_sekarang',
        'kota_sekarang',
        'provinsi_sekarang',
        'kode_pos',
        'jurusan_saat_sekolah',
        'tahun_masuk',
        'tahun_lulus',
        'status_kelulusan',
        'status_saat_ini',
        'lama_tunggu_bulan',
        'nama_perusahaan',
        'jabatan',
        'bidang_pekerjaan',
        'jenis_pekerjaan',
        'kesesuaian_bidang',
        'skala_gaji_bulanan',
        'kota_bekerja',
        'provinsi_bekerja',
        'nama_perguruan_tinggi',
        'program_studi',
        'jenjang_kuliah',
        'jalur_masuk_pt',
        'tahun_masuk_pt',
        'kota_perguruan_tinggi',
        'nilai_kompetensi',
        'nilai_soft_skill',
        'nilai_fasilitas_sekolah',
        'nilai_kepuasan_sekolah',
        'saran_untuk_sekolah',
        'diisi_pada',
        'sudah_diverifikasi',
        'diverifikasi_oleh',
        'diverifikasi_pada',
    ];

    protected $casts = [
        'tanggal_lahir'       => 'date',
        'diisi_pada'          => 'datetime',
        'diverifikasi_pada'   => 'datetime',
        'sudah_diverifikasi'  => 'boolean',
    ];

    // ── Relasi ──────────────────────────────────────────────────────────────

    public function User()
    {
        return $this->belongsTo(User::class, 'User_id');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function sertifikasi()
    {
        return $this->hasMany(SertifikasiAlumni::class, 'data_alumni_id');
    }

    // ── Scopes ──────────────────────────────────────────────────────────────

    public function scopeSudahDiisi($query)
    {
        return $query->whereNotNull('diisi_pada');
    }

    public function scopeBelumDiverifikasi($query)
    {
        return $query->where('sudah_diverifikasi', false)->whereNotNull('diisi_pada');
    }

    public function scopeTahunLulus($query, int $tahun)
    {
        return $query->where('tahun_lulus', $tahun);
    }

    // ── Helper ───────────────────────────────────────────────────────────────

    public function labelStatusSaatIni(): string
    {
        return match($this->status_saat_ini) {
            'bekerja'             => 'Bekerja',
            'wirausaha'           => 'Wirausaha',
            'kuliah'              => 'Kuliah',
            'kuliah_dan_bekerja'  => 'Kuliah & Bekerja',
            'belum_bekerja'       => 'Belum Bekerja',
            'tidak_melanjutkan'   => 'Tidak Melanjutkan',
            default               => '-',
        };
    }
}
