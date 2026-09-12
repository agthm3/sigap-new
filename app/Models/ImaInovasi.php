<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ImaInovasi extends Model
{
    use HasFactory;

    protected $table = 'ima_inovasis';

    protected $fillable = [
        'user_id',
        'kategori_ima',
        'judul',
        'opd_unit',
        'inisiator_daerah',
        'inisiator_nama',
        'koordinat',
        
        // Kontak Operator (PIC)
        'operator_nama',
        'operator_jabatan',
        'operator_wa',
        'operator_email',
        
        // Metadata Kebijakan & Klasifikasi
        'klasifikasi',
        'jenis_inovasi',
        'bentuk_inovasi_daerah',
        'urusan_pemerintah',
        'asta_cipta',
        'program_prioritas',
        'misi_walikota',
        'tahap_inovasi',
        'waktu_uji_coba',
        'waktu_penerapan',
        'perkembangan_inovasi',
        
        // Uraian Deskripsi
        'rancang_bangun',
        'tujuan',
        'manfaat',
        'hasil_inovasi',
        
        // Berkas Lampiran
        'sampul_file',
        'anggaran_file',
        'profil_bisnis_file',
        'haki_file',
        'penghargaan_file',
        
        // Asistensi & Review
        'asistensi_status',
        'asistensi_note',
        'asistensi_by',
        'asistensi_at',
    ];

    protected $casts = [
        'waktu_uji_coba'  => 'date',
        'waktu_penerapan' => 'date',
        'asistensi_at'    => 'datetime',
    ];

    // Relasi ke Pembuat / Pengusul
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Verifikator
    public function verifikator()
    {
        return $this->belongsTo(User::class, 'asistensi_by');
    }

    // Relasi ke 20 Indikator Evidence
    public function evidences()
    {
        return $this->hasMany(ImaEvidence::class, 'ima_inovasi_id');
    }
}