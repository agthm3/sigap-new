<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class KgbRiwayat extends Model
{
    use HasFactory;

    protected $table = 'kgb_riwayat';

    protected $fillable = [
        'user_id',
        'jenis_pegawai',
        'pangkat_golongan',
        'jabatan',
        'nomor_sk_lama',
        'tanggal_sk_lama',
        'tmt_lama',
        'mkg_tahun_lama',
        'mkg_bulan_lama',
        'gaji_pokok_lama',
        'pejabat_penetap',
        'tmt_baru',
        'mkg_tahun_baru',
        'mkg_bulan_baru',
        'gaji_pokok_baru',
        'status',
        'nomor_surat_usulan',
        'tanggal_surat_usulan',
        'file_sk_lama',
        'catatan',
    ];

    protected $casts = [
        'tanggal_sk_lama' => 'date',
        'tmt_lama' => 'date',
        'tmt_baru' => 'date',
        'tanggal_surat_usulan' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hitung selisih hari menuju TMT Baru
     */
    public function getSisaHariAttribute(): int
    {
        if (!$this->tmt_baru) {
            return 0;
        }

        return (int) now()->startOfDay()->diffInDays($this->tmt_baru->startOfDay(), false);
    }

    /**
     * Badge status visual & countdown
     */
    public function getBadgeStatusAttribute(): array
    {
        if ($this->status === 'selesai') {
            return [
                'label' => 'SELESAI',
                'color' => 'bg-emerald-50 border-emerald-200 text-emerald-700',
                'keterangan' => 'SK Baru Terbit'
            ];
        }

        if ($this->status === 'proses') {
            return [
                'label' => 'PROSES USULAN',
                'color' => 'bg-blue-50 border-blue-200 text-blue-700',
                'keterangan' => 'Dalam Proses Verifikasi'
            ];
        }

        $sisa = $this->sisa_hari;

        if ($sisa < 0) {
            return [
                'label' => 'TERLEWAT',
                'color' => 'bg-rose-50 border-rose-300 text-rose-700 font-bold',
                'keterangan' => abs($sisa) . ' hari lewat TMT'
            ];
        } elseif ($sisa <= 30) {
            return [
                'label' => 'JATUH TEMPO',
                'color' => 'bg-red-50 border-red-200 text-red-700 font-semibold',
                'keterangan' => $sisa . ' hari lagi'
            ];
        } elseif ($sisa <= 60) {
            return [
                'label' => 'SIAP USUL',
                'color' => 'bg-amber-50 border-amber-200 text-amber-700 font-semibold',
                'keterangan' => $sisa . ' hari lagi'
            ];
        } else {
            return [
                'label' => 'MENUNGGU',
                'color' => 'bg-gray-50 border-gray-200 text-gray-600',
                'keterangan' => $sisa . ' hari lagi'
            ];
        }
    }
}