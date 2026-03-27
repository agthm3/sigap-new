<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InkubatormaRecord extends Model
{
    use HasFactory;

    protected $table = 'inkubatorma_records';

    protected $fillable = [
        'inkubatorma_id',
        'actor_id',
        'actor_role',
        'record_type',
        'title',
        'content',
        'revision_note',
        'file_path',
        'file_name',
        'file_mime',
    ];

    /**
     * Relasi ke pengajuan utama.
     */
    public function inkubatorma()
    {
        return $this->belongsTo(Inkubatorma::class, 'inkubatorma_id');
    }

    /**
     * Relasi ke user pembuat record.
     */
    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * Label record type untuk tampilan UI.
     */
    public function getRecordTypeLabelAttribute(): string
    {
        return match ($this->record_type) {
            'sesi_konsultasi'   => 'Sesi Konsultasi',
            'upload_revisi'     => 'Upload Revisi',
            'review_revisi'     => 'Review Revisi',
            'konfirmasi_selesai'=> 'Konfirmasi Selesai',
            'catatan_umum'      => 'Catatan Umum',
            default             => 'Record',
        };
    }

    /**
     * Label actor role untuk tampilan UI.
     */
    public function getActorRoleLabelAttribute(): string
    {
        return match ($this->actor_role) {
            'admin'       => 'Admin',
            'verifikator' => 'Verifikator',
            'user'        => 'User',
            default       => 'Pengguna',
        };
    }

    /**
     * Cek apakah record punya file lampiran.
     */
    public function getHasFileAttribute(): bool
    {
        return !empty($this->file_path);
    }
}