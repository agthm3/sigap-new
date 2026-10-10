<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SiipidPrestasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'siipid_prestasis';

    protected $fillable = [
        'uuid',
        'user_id',
        'innovable_type',
        'innovable_id',
        'kategori_sasaran',
        'nama_inovator',
        'nik',
        'nip',
        'jabatan',
        'opd_instansi',
        'no_wa',
        'email',
        'jenis_kepesertaan',
        'anggota_tim',
        'nama_prestasi',
        'ajang_kompetisi',
        'tahun_perolehan',
        'tingkat_prestasi',
        'kategori_khusus',
        'peringkat_capaian',
        'lembaga_pemberi',
        'deskripsi_prestasi',
        'bukti_prestasi_file',
        'bukti_penerapan_file',
        'surat_keaslian_file',
        'surat_kontribusi_tim_file',
        'usulan_bentuk_penghargaan',
        'usulan_bentuk_insentif',
        'status',
        'catatan_review_terakhir',
        'reviewer_id',
        'reviewed_at',
        'nomor_sk_walikota',
        'tanggal_sk_walikota',
        'file_sk_walikota',
        'is_published_public',
    ];

    protected $casts = [
        'anggota_tim' => 'array',
        'tahun_perolehan' => 'integer',
        'tanggal_sk_walikota' => 'date',
        'reviewed_at' => 'datetime',
        'is_published_public' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function innovable()
    {
        return $this->morphTo();
    }

    public function reviewLogs()
    {
        return $this->hasMany(SiipidReviewLog::class, 'prestasi_id')->latest();
    }

    // Status Helper
    public function getIsEditableAttribute(): bool
    {
        return in_array($this->status, ['draft', 'dikembalikan_perbaikan']);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'draft' => ['label' => 'Draft', 'class' => 'bg-gray-100 text-gray-700 border-gray-200'],
            'diajukan' => ['label' => 'Diajukan', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
            'dalam_review' => ['label' => 'Dalam Review', 'class' => 'bg-indigo-50 text-indigo-700 border-indigo-200'],
            'dikembalikan_perbaikan' => ['label' => 'Perlu Perbaikan', 'class' => 'bg-amber-50 text-amber-800 border-amber-300'],
            'ditolak' => ['label' => 'Ditolak', 'class' => 'bg-red-50 text-red-700 border-red-200'],
            'direkomendasikan' => ['label' => 'Direkomendasikan', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
            'ditetapkan_sk' => ['label' => 'Ditetapkan SK', 'class' => 'bg-maroon text-white border-maroon'],
            default => ['label' => ucfirst($this->status), 'class' => 'bg-gray-50 text-gray-600 border-gray-200']
        };
    }

    public function getTingkatLabelAttribute(): string
    {
        return match ($this->tingkat_prestasi) {
            'kota' => 'Tingkat Kota Makassar',
            'provinsi' => 'Tingkat Provinsi Sulsel',
            'nasional' => 'Tingkat Nasional',
            'internasional' => 'Tingkat Internasional',
            'khusus' => 'Kategori Khusus Kota',
            default => ucfirst($this->tingkat_prestasi)
        };
    }

    public function scopeOwnedBy($query, User $user)
    {
        if ($user->hasRole(['admin', 'superadmin', 'verif_siipid'])) {
            return $query;
        }
        return $query->where('user_id', $user->id);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published_public', true)
                     ->whereIn('status', ['direkomendasikan', 'ditetapkan_sk']);
    }
}
