<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PegawaiProfile extends Model
{
    protected $fillable = [
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'status_perkawinan',
        'golongan_darah',
        'nip_baru',
        'nip_lama',
        'keterangan',
        'status_pegawai',
        'jabatan',
        'golongan',
        'tmt_pns',
        'atasan_langsung',
        'golongan_ruang',
        'tmt_golongan',
        'masa_kerja_tahun',
        'masa_kerja_bulan',
        'tmt_jabatan',
        'eselon',
        'jabatan_struktural',
        'jabatan_fungsional',
        'jabatan_teknis',
        'unor',
        'alamat_ktp',
        'alamat_domisili',
        'npwp',
        'bpjs_kesehatan',
        'bpjs_ketenagakerjaan',
        'bank_nama',
        'nomor_rekening',
        'nama_rekening',
        'nama_pasangan',
        'pekerjaan_pasangan',
        'jumlah_anak',
        'kontak_darurat',
        'pendidikan_terakhir',
        'jurusan',
        'tahun_lulus',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}