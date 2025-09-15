<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inovasi extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'judul','opd_unit','inisiator_daerah','inisiator_nama','koordinat',
        'klasifikasi','jenis_inovasi','bentuk_inovasi_daerah','asta_cipta','program_prioritas',
        'urusan_pemerintah','waktu_uji_coba','waktu_penerapan','tahap_inovasi',
        'rancang_bangun','tujuan','manfaat','hasil_inovasi','perkembangan_inovasi',
        'anggaran_file','profil_bisnis_file','haki_file','penghargaan_file', 'user_id',
        // asistensi
        'asistensi_status','asistensi_note','asistensi_by','asistensi_at',
    ];

    protected $casts = [
        'waktu_uji_coba'  => 'date',
        'waktu_penerapan' => 'date',
        'asistensi_at'    => 'datetime',
    ];

     public function getAstaCiptaLabelAttribute(): string
    {
        $map = config('inovasi.asta_cipta', []);
        return $map[$this->asta_cipta] ?? ($this->asta_cipta ?: '—');
    }

    public function getProgramPrioritasLabelAttribute(): string
    {
        $map = config('inovasi.program_prioritas', []);
        return $map[$this->program_prioritas] ?? ($this->program_prioritas ?: '—');
    }

    public function getUrusanPemerintahLabelAttribute(): string
    {
        $map = config('inovasi.urusan_pemerintah', []);
        return $map[$this->urusan_pemerintah] ?? ($this->urusan_pemerintah ?: '—');
    }
     public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope untuk filter kepemilikan
    public function scopeOwnedBy($query, \App\Models\User $user)
    {
        // Admin: tidak difilter
        return $user->hasRole('admin') ? $query : $query->where('user_id', $user->id);
    }

    public function verifikator() { return $this->belongsTo(User::class, 'asistensi_by'); }
}
