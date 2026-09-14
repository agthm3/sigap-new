<?php

namespace App\Repositories;

use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentRepository
{
    /**
     * Simpan dokumen baru ke disk sesuai nilai sensitivity ('private' atau 'public').
     */
    public function create(array $data, ?UploadedFile $file = null, ?UploadedFile $thumb = null): Document
    {
        return DB::transaction(function () use ($data, $file, $thumb) {
            $isPrivate = ($data['sensitivity'] ?? 'public') === 'private';
            $targetDisk = $isPrivate ? 'private' : 'public';

            // 1. Simpan berkas dokumen fisik
            if ($file) {
                $data['file_path'] = $file->store('documents', $targetDisk);
            } elseif (!empty($data['temp_file_path'])) {
                $tempPath = $data['temp_file_path'];
                if (Storage::disk('public')->exists($tempPath)) {
                    $newFilename = 'documents/' . Str::random(40) . '.' . pathinfo($tempPath, PATHINFO_EXTENSION);
                    
                    if ($targetDisk === 'public') {
                        Storage::disk('public')->move($tempPath, $newFilename);
                    } else {
                        // Salin ke vault private dan hapus dari temporary public
                        $content = Storage::disk('public')->get($tempPath);
                        Storage::disk('private')->put($newFilename, $content);
                        Storage::disk('public')->delete($tempPath);
                    }
                    $data['file_path'] = $newFilename;
                } else {
                    throw new \InvalidArgumentException('Berkas sementara tidak ditemukan atau sudah kedaluwarsa.');
                }
            } elseif (empty($data['file_path'])) {
                throw new \InvalidArgumentException('Berkas dokumen wajib dilampirkan.');
            }

            // 2. Simpan thumbnail (selalu di disk public untuk pratinjau kartu)
            if ($thumb) {
                $data['thumb_path'] = $thumb->store('thumbnails', 'public');
            } elseif (!empty($data['temp_thumb_path'])) {
                $tempThumb = $data['temp_thumb_path'];
                if (Storage::disk('public')->exists($tempThumb)) {
                    $newThumb = 'thumbnails/' . Str::random(40) . '.' . pathinfo($tempThumb, PATHINFO_EXTENSION);
                    Storage::disk('public')->move($tempThumb, $newThumb);
                    $data['thumb_path'] = $newThumb;
                }
            }

            // 3. Generate Alias Unik jika kosong
            if (empty($data['alias'])) {
                $base = strtoupper(Str::slug(($data['title'] ?? 'dokumen') . '-' . ($data['year'] ?? now()->year), '_'));
                $alias = $base;
                $i = 1;
                while (Document::where('alias', $alias)->exists()) {
                    $alias = $base . '_' . $i++;
                }
                $data['alias'] = $alias;
            }

            // 4. Standarisasi Tag (simpan sebagai array JSON)
            if (isset($data['tags'])) {
                if (is_string($data['tags'])) {
                    $tags = array_values(array_filter(array_map('trim', explode(',', $data['tags']))));
                    $data['tags'] = !empty($tags) ? $tags : null;
                } elseif (is_array($data['tags'])) {
                    $data['tags'] = array_values(array_filter(array_map('trim', $data['tags']))) ?: null;
                }
            } else {
                $data['tags'] = null;
            }

            unset($data['temp_file_path'], $data['temp_thumb_path']);

            return Document::create($data);
        });
    }

    /**
     * Update dokumen dan otomatis pindahkan berkas fisik jika status sensitivity berubah.
     */
    public function update(
        int $id,
        array $data,
        ?UploadedFile $file = null,
        ?UploadedFile $thumb = null
    ): Document {
        return DB::transaction(function () use ($id, $data, $file, $thumb) {
            $doc = Document::findOrFail($id);

            $oldSensitivity = $doc->sensitivity;
            $newSensitivity = $data['sensitivity'] ?? $oldSensitivity;
            $targetDisk = $newSensitivity === 'private' ? 'private' : 'public';

            // 1. Jika ada upload file baru saat update
            if ($file instanceof UploadedFile) {
                $this->deletePhysicalFile($doc->file_path);
                $data['file_path'] = $file->store('documents', $targetDisk);
            }
            // 2. Jika tidak ada file baru, tapi sensitivity berubah: PINDAHKAN FILE FISIK
            elseif ($oldSensitivity !== $newSensitivity && $doc->file_path) {
                $data['file_path'] = $this->migrateFileDisk($doc->file_path, $oldSensitivity, $newSensitivity);
            }

            // Handle thumbnail baru
            if ($thumb instanceof UploadedFile) {
                if ($doc->thumb_path && Storage::disk('public')->exists($doc->thumb_path)) {
                    Storage::disk('public')->delete($doc->thumb_path);
                }
                $data['thumb_path'] = $thumb->store('thumbnails', 'public');
            }

            if (empty($data['alias'])) {
                $base = strtoupper(Str::slug(($data['title'] ?? 'dokumen') . '-' . ($data['year'] ?? now()->year), '_'));
                $alias = $base;
                $i = 1;
                while (Document::where('alias', $alias)->where('id', '!=', $id)->exists()) {
                    $alias = $base . '_' . $i++;
                }
                $data['alias'] = $alias;
            }

            if (isset($data['tags'])) {
                if (is_string($data['tags'])) {
                    $tags = array_values(array_filter(array_map('trim', explode(',', $data['tags']))));
                    $data['tags'] = !empty($tags) ? $tags : null;
                } elseif (is_array($data['tags'])) {
                    $data['tags'] = array_values(array_filter(array_map('trim', $data['tags']))) ?: null;
                }
            }

            $doc->fill($data)->save();

            return $doc->refresh();
        });
    }

    /**
     * Pindahkan berkas antar disk (public <-> private) secara atomik.
     */
    private function migrateFileDisk(string $filePath, string $fromSensitivity, string $toSensitivity): string
    {
        $sourceDisk = $fromSensitivity === 'private' ? 'private' : 'public';
        $destDisk   = $toSensitivity === 'private' ? 'private' : 'public';

        if ($sourceDisk === $destDisk) {
            return $filePath;
        }

        // Cek lokasi file saat ini (dengan fallback jika anomali)
        $actualSourceDisk = Storage::disk($sourceDisk)->exists($filePath) ? $sourceDisk : ($sourceDisk === 'private' ? 'public' : 'private');

        if (Storage::disk($actualSourceDisk)->exists($filePath)) {
            $stream = Storage::disk($actualSourceDisk)->get($filePath);
            Storage::disk($destDisk)->put($filePath, $stream);
            Storage::disk($actualSourceDisk)->delete($filePath);
        }

        return $filePath;
    }

    public function delete(int $id, bool $force = false): void
    {
        $doc = Document::findOrFail($id);

        if ($force) {
            $this->deletePhysicalFile($doc->file_path);
            if ($doc->thumb_path && Storage::disk('public')->exists($doc->thumb_path)) {
                Storage::disk('public')->delete($doc->thumb_path);
            }
            $doc->forceDelete();
        } else {
            $doc->delete();
        }
    }

    private function deletePhysicalFile(?string $filePath): void
    {
        if (!$filePath) return;

        if (Storage::disk('private')->exists($filePath)) {
            Storage::disk('private')->delete($filePath);
        }
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }

    public function paginate(array $filters = [], int $perPage = 10, ?int $userId = null, ?string $mode = 'public')
    {
        $q = Document::query();

        if ($mode === 'saya') {
            $q->where('created_by', $userId);
            if (!empty($filters['root_only'])) {
                $q->whereNull('folder_id');
            }
        } else {
            $q->where('sensitivity', 'public');
            if (!empty($filters['root_only'])) {
                $q->whereNull('folder_id');
            }
        }

        // Pencarian Komprehensif Multi-Term
        if (!empty($filters['q'])) {
            $rawKw = trim($filters['q']);
            // Pisahkan kata kunci jika pengguna mengetik beberapa kata (misal: "SK 2026 BRIDA")
            $words = array_filter(explode(' ', $rawKw));

            $q->where(function ($query) use ($rawKw, $words) {
                // Pencarian tepat frasa penuh
                $query->where('title', 'like', "%{$rawKw}%")
                    ->orWhere('alias', 'like', "%{$rawKw}%")
                    ->orWhere('number', 'like', "%{$rawKw}%")
                    ->orWhere('stakeholder', 'like', "%{$rawKw}%")
                    ->orWhere('tags', 'like', "%{$rawKw}%")
                    ->orWhere('description', 'like', "%{$rawKw}%")
                    ->orWhere('physical_rack', 'like', "%{$rawKw}%")
                    ->orWhere('physical_row', 'like', "%{$rawKw}%");

                // Pencarian parsial per-kata (agar fleksibel jika susunan kata terbalik)
                if (count($words) > 1) {
                    $query->orWhere(function ($subQ) use ($words) {
                        foreach ($words as $w) {
                            $subQ->where(function ($wQ) use ($w) {
                                $wQ->where('title', 'like', "%{$w}%")
                                   ->orWhere('number', 'like', "%{$w}%")
                                   ->orWhere('stakeholder', 'like', "%{$w}%")
                                   ->orWhere('tags', 'like', "%{$w}%");
                            });
                        }
                    });
                }
            });
        }

        if (!empty($filters['category'])) {
            $q->where('category', $filters['category']);
        }

        if (!empty($filters['year'])) {
            $q->where('year', $filters['year']);
        }

        $q->latest('created_at');

        return $q->paginate($perPage)->withQueryString();
    }

    public function find(int $id): ?Document
    {
        return Document::findOrFail($id);
    }

    public function getExistingTags(): array
    {
        $allDocs = Document::whereNotNull('tags')->pluck('tags')->toArray();
        $unique = [];
        foreach ($allDocs as $item) {
            if (is_array($item)) {
                foreach ($item as $p) {
                    $clean = trim($p);
                    if ($clean !== '') $unique[] = $clean;
                }
            } elseif (is_string($item)) {
                $decoded = json_decode($item, true);
                $parts = is_array($decoded) ? $decoded : explode(',', $item);
                foreach ($parts as $p) {
                    $clean = trim($p);
                    if ($clean !== '') $unique[] = $clean;
                }
            }
        }
        return array_values(array_unique($unique));
    }
}