<?php

namespace App\Repositories;

use App\Models\Inovasi;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InovasiRepository
{
    /**
     * Buat inovasi baru + simpan file lampiran (opsional).
     */
    public function create(
        array $data,
        ?UploadedFile $anggaran = null,
        ?UploadedFile $profilBisnis = null,
        ?UploadedFile $haki = null,
        ?UploadedFile $penghargaan = null
    ): Inovasi {
        return DB::transaction(function () use ($data, $anggaran, $profilBisnis, $haki, $penghargaan) {
            // default tahap & pemilik
            $data['tahap_inovasi'] = $data['tahap_inovasi'] ?? 'Inisiatif';
            $data['user_id'] = $data['user_id'] ?? Auth::id();

            // simpan file
            if ($anggaran instanceof UploadedFile) {
                $data['anggaran_file'] = $anggaran->store('inovasi/anggaran', 'public');
            }
            if ($profilBisnis instanceof UploadedFile) {
                $data['profil_bisnis_file'] = $profilBisnis->store('inovasi/profil_bisnis', 'public');
            }
            if ($haki instanceof UploadedFile) {
                $data['haki_file'] = $haki->store('inovasi/haki', 'public');
            }
            if ($penghargaan instanceof UploadedFile) {
                $data['penghargaan_file'] = $penghargaan->store('inovasi/penghargaan', 'public');
            }

            $inovasi = Inovasi::create($data);

            Log::channel('giga')->info('Inovasi created', [
                'id' => $inovasi->id,
                'judul' => $inovasi->judul,
                'user_id' => $inovasi->user_id
            ]);

            return $inovasi;
        });
    }

    /**
     * Paginate dengan pembatasan kepemilikan:
     * - Admin: semua
     * - Non-admin: hanya miliknya (user_id)
     */
    public function paginateForUser(User $user, array $filters = [], int $perPage = 10)
    {
        $q = Inovasi::query();

        // batasi kepemilikan untuk non-admin
        if (!$user->hasRole('admin')) {
            $q->where('user_id', $user->id);
        }

        if (!empty($filters['q'])) {
            $kw = trim($filters['q']);
            $q->where(function ($w) use ($kw) {
                $w->where('judul', 'like', "%{$kw}%")
                  ->orWhere('opd_unit', 'like', "%{$kw}%");
            });
        }

        if (!empty($filters['urusan']))    $q->where('urusan_pemerintah', $filters['urusan']);
        if (!empty($filters['inisiator'])) $q->where('inisiator_daerah', $filters['inisiator']);

        if (!empty($filters['tahap']) && !empty($filters['tahap_status'])) {
            // saat ini satu kolom tahap_inovasi
            $q->where('tahap_inovasi', $filters['tahap_status']);
        }

        if (($filters['sort'] ?? 'terbaru') === 'judul') {
            $q->orderBy('judul');
        } else {
            $q->latest('created_at');
        }

        Log::channel('giga')->info('Inovasi paginateForUser', [
            'user_id' => $user->id,
            'is_admin' => $user->hasRole('admin'),
            'filters' => $filters
        ]);

        return $q->paginate($perPage)->withQueryString();
    }

    /**
     * (Optional) Versi umum tanpa user; masih dipakai tempat lain jika perlu.
     */
    public function paginate(array $filters = [], int $perPage = 10)
    {
        $q = Inovasi::query();

        if (!empty($filters['q'])) {
            $kw = trim($filters['q']);
            $q->where(function ($w) use ($kw) {
                $w->where('judul', 'like', "%{$kw}%")
                  ->orWhere('opd_unit', 'like', "%{$kw}%");
            });
        }

        if (!empty($filters['urusan']))    $q->where('urusan_pemerintah', $filters['urusan']);
        if (!empty($filters['inisiator'])) $q->where('inisiator_daerah', $filters['inisiator']);

        if (!empty($filters['tahap']) && !empty($filters['tahap_status'])) {
            $q->where('tahap_inovasi', $filters['tahap_status']);
        }

        if (($filters['sort'] ?? 'terbaru') === 'judul') {
            $q->orderBy('judul');
        } else {
            $q->latest('created_at');
        }

        Log::channel('giga')->info('Inovasi search filters applied', $filters);

        return $q->paginate($perPage)->withQueryString();
    }

    /**
     * Hapus inovasi.
     */
    public function delete(int $id): void
    {
        $inovasi = Inovasi::findOrFail($id);
        $judul = $inovasi->judul;
        $inovasi->delete();

        Log::channel('giga')->info('Inovasi deleted', ['id' => $id, 'judul' => $judul]);
    }

    /**
     * Cari 1 inovasi.
     */
    public function find(int $id): Inovasi
    {
        return Inovasi::findOrFail($id);
    }

    /**
     * Update metadata + ganti file (hapus file lama jika diubah).
     */
    public function update(
        int $id,
        array $data,
        ?UploadedFile $anggaran = null,
        ?UploadedFile $profilBisnis = null,
        ?UploadedFile $haki = null,
        ?UploadedFile $penghargaan = null
    ): Inovasi {
        return DB::transaction(function () use ($id, $data, $anggaran, $profilBisnis, $haki, $penghargaan) {
            $inovasi = Inovasi::findOrFail($id);

            // Lindungi user_id agar tidak diubah dari form biasa
            unset($data['user_id']);

            if ($anggaran instanceof UploadedFile) {
                if (!empty($inovasi->anggaran_file)) {
                    Storage::disk('public')->delete($inovasi->anggaran_file);
                }
                $data['anggaran_file'] = $anggaran->store('inovasi/anggaran', 'public');
            }

            if ($profilBisnis instanceof UploadedFile) {
                if (!empty($inovasi->profil_bisnis_file)) {
                    Storage::disk('public')->delete($inovasi->profil_bisnis_file);
                }
                $data['profil_bisnis_file'] = $profilBisnis->store('inovasi/profil_bisnis', 'public');
            }

            if ($haki instanceof UploadedFile) {
                if (!empty($inovasi->haki_file)) {
                    Storage::disk('public')->delete($inovasi->haki_file);
                }
                $data['haki_file'] = $haki->store('inovasi/haki', 'public');
            }

            if ($penghargaan instanceof UploadedFile) {
                if (!empty($inovasi->penghargaan_file)) {
                    Storage::disk('public')->delete($inovasi->penghargaan_file);
                }
                $data['penghargaan_file'] = $penghargaan->store('inovasi/penghargaan', 'public');
            }

            $inovasi->update($data);

            Log::channel('giga')->info('Inovasi updated', [
                'id' => $inovasi->id,
                'judul' => $inovasi->judul
            ]);

            return $inovasi;
        });
    }
}
