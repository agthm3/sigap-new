<?php

namespace App\Repositories;

use App\Models\Riset;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RisetRepository
{
    /** =======================
     *  QUERY / LISTING
     *  ======================= */
    public function paginateLatest(int $perPage = 10): LengthAwarePaginator
    {
        return Riset::query()->latest('created_at')->paginate($perPage);
    }

    public function findById(int $id): ?Riset
    {
        return Riset::find($id);
    }

    public function findOrFail(int $id): Riset
    {
        return Riset::findOrFail($id);
    }

    public function searchPublic(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $q     = $filters['q']          ?? null;
        $tags  = $filters['tags']       ?? null;   // "ai, lingkungan"
        $auth  = $filters['author']     ?? null;
        $stk   = $filters['stakeholder']?? null;
        $year  = $filters['year']       ?? null;
        $type  = $filters['type']       ?? null;   // internal/kolaborasi/eksternal
        $sort  = $filters['sort']       ?? 'latest';
        $onlyPublic = !empty($filters['only_public']);
        $hasDoi     = !empty($filters['has_doi']);

        $builder = Riset::query();

        if ($q) {
            $builder->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('abstract', 'like', "%{$q}%");
            });
        }

        if ($tags) {
            $arr = collect(explode(',', $tags))
                ->map(fn($v) => trim($v))
                ->filter()->values()->all();
            foreach ($arr as $tg) {
                $builder->whereJsonContains('tags', $tg);
            }
        }

        if ($auth) {
            $builder->where(function ($w) use ($auth) {
                $w->whereRaw("JSON_SEARCH(JSON_EXTRACT(authors,'$[*].name'), 'one', ?) IS NOT NULL", [$auth])
                  ->orWhereRaw("JSON_SEARCH(JSON_EXTRACT(authors,'$[*].name'), 'one', ?) IS NOT NULL", ['%'.$auth.'%']);
            });
        }

        if ($stk) {
            $builder->whereJsonContains('stakeholders', $stk);
        }

        if ($year) {
            $builder->where('year', (int)$year);
        }

        if ($type) {
            $builder->where('type', $type);
        }

        if ($onlyPublic) {
            $builder->where('access', 'Public');
        }

        if ($hasDoi) {
            $builder->whereNotNull('doi')->where('doi','<>','');
        }

        $builder->when($sort === 'latest', fn($q) => $q->latest('created_at'))
                ->when($sort === 'oldest', fn($q) => $q->orderBy('created_at', 'asc'))
                ->when($sort === 'az',     fn($q) => $q->orderBy('title', 'asc'))
                ->when($sort === 'za',     fn($q) => $q->orderBy('title', 'desc'));

        return $builder->paginate($perPage)->withQueryString();
    }

    /** =======================
     *  CREATE / UPDATE
     *  ======================= */

    /**
     * Create riset.
     * - Jika ada file upload (name "pdf" atau "file"), simpan ke disk sesuai akses:
     *   Public -> public, Restricted -> riset_private
     * - Set file_path, file_name, file_size
     * - Selain itu, perilaku sama seperti sebelumnya
     */
    public function create(array $payload): Riset
    {
        // Ambil file upload dari request (modal-mu bisa pakai name="pdf" atau "file")
        $uploaded = request()->file('pdf') ?? request()->file('file');

        $accessRaw = $payload['access'] ?? 'Public';
        $isPublic  = $this->isPublic($accessRaw);
        $disk      = $isPublic ? 'public' : 'riset_private';

        if ($uploaded) {
            $year     = (int)($payload['year'] ?? date('Y'));
            $title    = $payload['title'] ?? 'riset';
            [$filePath, $fileName, $fileSizeHuman] = $this->storeUploadedFile($uploaded, $disk, $year, $title);

            $payload['file_path'] = $filePath;
            $payload['file_name'] = $fileName;
            $payload['file_size'] = $fileSizeHuman;
        }
        // jika tidak ada file: biarkan payload seperti semula (kalau DB NOT NULL, validasi di layer atas yang wajibkan file)

        // Normalisasi akses ke "Public"/"Restricted" (tanpa mengubah logika lainmu)
        $payload['access'] = $isPublic ? 'Public' : 'Restricted';

        return Riset::create($payload);
    }

    /**
     * Update riset.
     * - Jika ada file baru -> simpan ke disk sesuai akses baru, hapus file lama di disk lama
     * - Jika tidak ada file baru & akses berubah -> pindahkan file lama antar disk
     */
    public function update(int $id, array $payload): Riset
    {
        $r = $this->findOrFail($id);

        $uploaded   = request()->file('pdf') ?? request()->file('file');
        $newAccess  = $payload['access'] ?? ($r->access ?? 'Public');
        $newIsPublic= $this->isPublic($newAccess);
        $newDisk    = $newIsPublic ? 'public' : 'riset_private';

        if ($uploaded) {
            $year   = (int)($payload['year'] ?? $r->year ?? date('Y'));
            $title  = $payload['title'] ?? $r->title ?? 'riset';

            [$newPath, $newName, $newSizeHuman] = $this->storeUploadedFile($uploaded, $newDisk, $year, $title);

            // hapus file lama jika ada
            if (!empty($r->file_path)) {
                $oldDisk = $this->isPublic($r->access ?? 'Public') ? 'public' : 'riset_private';
                $this->safeDelete($r->file_path, $oldDisk);
            }

            $payload['file_path'] = $newPath;
            $payload['file_name'] = $newName;
            $payload['file_size'] = $newSizeHuman;
        } else {
            // Tidak ada file baru → jika akses berubah & file lama ada, pindahkan
            if (!empty($r->file_path)) {
                $oldDisk = $this->isPublic($r->access ?? 'Public') ? 'public' : 'riset_private';
                if ($oldDisk !== $newDisk) {
                    $this->moveBetweenDisks($r->file_path, $oldDisk, $newDisk);
                }
            }
        }

        // Normalisasi akses ke "Public"/"Restricted"
        $payload['access'] = $newIsPublic ? 'Public' : 'Restricted';

        $r->update($payload);
        return $r;
    }

    /** =======================
     *  HELPERS
     *  ======================= */

    private function isPublic($accessRaw): bool
    {
        return strtoupper(trim($accessRaw ?? 'Public')) === 'PUBLIC';
    }

    /**
     * Simpan file upload ke disk & path yang ditentukan.
     * @return array [path, filename, humanSize]
     */
    private function storeUploadedFile($file, string $disk, int $year, string $title): array
    {
        $ext      = $file->getClientOriginalExtension() ?: 'pdf';
        $baseName = Str::slug($title);
        $fileName = $baseName.'-'.Str::random(6).'.'.$ext;

        // path relatif terhadap root disk (tanpa "storage/")
        $path = $file->storeAs("riset/{$year}", $fileName, $disk);

        $sizeBytes = $file->getSize();
        $sizeHuman = $this->humanFileSize($sizeBytes);

        return [$path, $fileName, $sizeHuman];
    }

    private function moveBetweenDisks(string $path, string $fromDisk, string $toDisk): bool
    {
        $relPath = $this->normalizePath($path);

        if (!Storage::disk($fromDisk)->exists($relPath)) {
            return false;
        }

        $bytes = Storage::disk($fromDisk)->get($relPath);
        Storage::disk($toDisk)->put($relPath, $bytes);
        Storage::disk($fromDisk)->delete($relPath);

        return true;
    }

    private function safeDelete(string $path, string $disk): void
    {
        $relPath = $this->normalizePath($path);
        if (Storage::disk($disk)->exists($relPath)) {
            Storage::disk($disk)->delete($relPath);
        }
    }

    private function normalizePath(?string $path): string
    {
        $path = (string)$path;
        $path = preg_replace('#^storage/#', '', $path); // buang prefix jika ada
        return ltrim($path, '/');
    }

    private function humanFileSize(?int $bytes, int $dec = 1): ?string
    {
        if ($bytes === null) return null;
        $size = ['B','KB','MB','GB','TB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$dec}f", $bytes / pow(1024, $factor)).' '.$size[$factor];
    }
}
