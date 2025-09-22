<?php

namespace App\Repositories;

use App\Models\Kinerja;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;


class KinerjaRepository
{
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

    public function create(array $data, UploadedFile $file, ?UploadedFile $thumb = null): Kinerja
    {
        // Simpan file bukti
        $filePath = $file->store('kinerja/files', 'public');
        $mime     = $file->getMimeType();
        $size     = $file->getSize();

        // Tentukan thumbnail:
        // - Jika file bukti adalah gambar → pakai file itu sebagai thumb
        // - Jika bukan gambar → pakai upload thumb (kalau ada), selain itu biarkan null → blade fallback dummy
        $thumbPath = null;
        if (str_starts_with(strtolower((string)$mime), 'image/')) {
            $thumbPath = $filePath;
        } elseif ($thumb instanceof UploadedFile) {
            $thumbPath = $thumb->store('kinerja/thumbs', 'public');
        }

        $payload = [
            'category'      => $data['category'],
            'rhk'           => Arr::get($data, 'rhk'),
            'title'         => $data['title'],
            'description'   => Arr::get($data, 'description'),
            'activity_date' => $data['date'], // validated Y-m-d
            'file_path'     => $filePath,
            'file_mime'     => $mime,
            'file_size'     => $size,
            'thumb_path'    => $thumbPath,
            'created_by'    => Auth::id(),
        ];

        return Kinerja::create($payload);
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
}
