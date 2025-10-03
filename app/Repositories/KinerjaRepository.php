<?php

namespace App\Repositories;

use App\Models\Kinerja;
use App\Models\KinerjaMedia;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;        // <— pastikan ada
use App\Services\ImageCompressor;  

class KinerjaRepository
{
    public function __construct(private ImageCompressor $img) {} 
    public function paginateForIndex(array $filters, int $perPage = 24): LengthAwarePaginator
    {
        $q        = trim($filters['q'] ?? '');
        $category = trim($filters['category'] ?? '');
        $rhk      = trim($filters['rhk'] ?? '');
        $month    = trim($filters['month'] ?? ''); // YYYY-MM

        $query = Kinerja::query()->latest('activity_date')->latest('id');

        if ($q !== '') {
            $query->where(function($w) use ($q) {
                $w->where('title','like',"%{$q}%")
                  ->orWhere('description','like',"%{$q}%");
            });
        }
        if ($category !== '') {
            $query->where('category', $category);
        }
        if ($rhk !== '') {
            $query->where('rhk', $rhk);
        }
        if ($month !== '') {
            // filter per-bulan
            $query->whereBetween('activity_date', [
                "{$month}-01",
                date('Y-m-t', strtotime("{$month}-01")),
            ]);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function create(array $data, array $files, ?UploadedFile $thumb = null): Kinerja
    {
        if (empty($files)) {
            abort(422, 'Tidak ada file yang diunggah.');
        }

        return DB::transaction(function () use ($data, $files, $thumb) {
            // ==== 1) Simpan FILE PERTAMA dulu agar punya file_path NOT NULL ====
            /** @var UploadedFile $first */
            $first = $files[0];
            $savedFirst = $this->img->compressAndStore($first, 'kinerja/files', 1600, 80, 'auto');
            $firstIsImage = str_starts_with($savedFirst['mime'], 'image/');

            // ==== 2) Buat record Kinerja pakai info file pertama ====
            $m = Kinerja::create([
                'category'      => $data['category'],
                'rhk'           => Arr::get($data, 'rhk'),
                'title'         => $data['title'],
                'description'   => Arr::get($data, 'description'),
                'activity_date' => $data['date'],
                'file_path'     => $savedFirst['path'], // NOT NULL
                'file_mime'     => $savedFirst['mime'],
                'file_size'     => $savedFirst['size'],
                'thumb_path'    => $firstIsImage ? $savedFirst['path'] : null, // sementara
                'created_by'    => Auth::id(),
            ]);

            // ==== 3) Simpan media pertama ====
            $firstMedia = KinerjaMedia::create([
                'kinerja_id' => $m->id,
                'path'       => $savedFirst['path'],
                'mime'       => $savedFirst['mime'],
                'size'       => $savedFirst['size'],
                'is_image'   => $firstIsImage,
                'is_primary' => false, // tentukan primary setelah semua media diproses
            ]);

            $hasImage = $firstIsImage;
            $firstImageId = $firstIsImage ? $firstMedia->id : null;

            // ==== 4) Simpan sisa media ====
            foreach (array_slice($files, 1) as $file) {
                if (!$file instanceof UploadedFile) continue;
                $saved = $this->img->compressAndStore($file, 'kinerja/files', 1600, 80, 'auto');
                $isImage = str_starts_with($saved['mime'], 'image/');

                $media = KinerjaMedia::create([
                    'kinerja_id' => $m->id,
                    'path'       => $saved['path'],
                    'mime'       => $saved['mime'],
                    'size'       => $saved['size'],
                    'is_image'   => $isImage,
                    'is_primary' => false,
                ]);

                if ($isImage && !$hasImage) {
                    $hasImage = true;
                    $firstImageId = $media->id;
                }
            }

            // ==== 5) Tentukan primary + thumb ====
            if ($hasImage && $firstImageId) {
                $m->media()->update(['is_primary' => false]); // clear
                $m->media()->where('id', $firstImageId)->update(['is_primary' => true]);

                // set thumb_path = media image pertama
                $thumbPath = $m->media()->where('id', $firstImageId)->value('path');
                $m->thumb_path = $thumbPath;
            } elseif (!$hasImage && $thumb instanceof UploadedFile) {
                $savedThumb = $this->img->compressAndStore($thumb, 'kinerja/thumbs', 800, 80, 'auto');
                $m->thumb_path = $savedThumb['path'];
            }

            $m->save();

            return $m;
        });
    }

  
    public function findOrFail(int $id): Kinerja
    {
        return Kinerja::findOrFail($id);
    }

    public function fileUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }

    
    public function listForAnnual(int $year, array $filters = []): Collection
    {
        $q        = trim($filters['q'] ?? '');
        $category = trim($filters['category'] ?? ''); // kode
        $rhk      = trim($filters['rhk'] ?? '');      // kode

        $builder = Kinerja::query()
            ->whereYear('activity_date', $year)
            ->latest('activity_date')->latest('id');

        if ($q !== '') {
            $builder->where(function($w) use ($q) {
                $w->where('title','like',"%{$q}%")
                ->orWhere('description','like',"%{$q}%");
            });
        }
        if ($category !== '') {
            $builder->where('category', $category);
        }
        if ($rhk !== '') {
            $builder->where('rhk', $rhk);
        }

        return $builder->get();
    }

    public function delete(int $id): void
    {
        $m = Kinerja::findOrFail($id);

        // kumpulkan semua path file agar dibersihkan dari storage
        $paths = [];
        if ($m->file_path)  $paths[] = $m->file_path;
        if ($m->thumb_path) $paths[] = $m->thumb_path;

        if (method_exists($m, 'media')) {
            foreach ($m->media as $med) {
                if ($med->path) $paths[] = $med->path;
                $med->delete();
            }
        }

        $m->delete();

        // hapus file di disk public (unique agar tak dobel)
        foreach (array_unique($paths) as $p) {
            Storage::disk('public')->delete($p);
        }
    }
}
