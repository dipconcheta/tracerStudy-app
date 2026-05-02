<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SertifikasiAlumni extends Model
{
    use HasFactory;

    protected $table = 'sertifikasi_alumni';

    protected $fillable = [
        'data_alumni_id',
        'nama_sertifikasi',
        'lembaga_penerbit',
        'tahun_terbit',
        'kategori',
    ];

    public function dataAlumni()
    {
        return $this->belongsTo(DataAlumni::class, 'data_alumni_id');
    }
}
