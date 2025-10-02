<?php

namespace App\Repositories;

use App\Models\Riset;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RisetRepository
{
    public function paginateLatest(int $perPage = 10): LengthAwarePaginator
    {
        return Riset::query()->latest('created_at')->paginate($perPage);
    }

    public function create(array $payload): Riset
    {
        return Riset::create($payload);
    }

    public function findById(int $id): ?Riset
    {
        return Riset::find($id);
    }


    public function searchPublic(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $q     = $filters['q']          ?? null;
        $tags  = $filters['tags']       ?? null;   // string: "ai, lingkungan"
        $auth  = $filters['author']     ?? null;   // string nama
        $stk   = $filters['stakeholder']?? null;   // string nama
        $year  = $filters['year']       ?? null;
        $type  = $filters['type']       ?? null;   // internal/kolaborasi/eksternal
        $sort  = $filters['sort']       ?? 'latest';
        $onlyPublic = !empty($filters['only_public']);
        $hasDoi     = !empty($filters['has_doi']);

        $builder = Riset::query();

        // Filter dasar
        if ($q) {
            $builder->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('abstract', 'like', "%{$q}%");
            });
        }

        if ($tags) {
            // pecah koma → cari salah satu muncul di kolom json "tags"
            $arr = collect(explode(',', $tags))
                ->map(fn($v) => trim($v))
                ->filter()->values()->all();
            foreach ($arr as $tg) {
                $builder->whereJsonContains('tags', $tg);
            }
        }

        if ($auth) {
            // cari di JSON authors (by name)
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

        // Sort
        $builder->when($sort === 'latest', fn($q) => $q->latest('created_at'))
                ->when($sort === 'oldest', fn($q) => $q->orderBy('created_at', 'asc'))
                ->when($sort === 'az',     fn($q) => $q->orderBy('title', 'asc'))
                ->when($sort === 'za',     fn($q) => $q->orderBy('title', 'desc'));

        return $builder->paginate($perPage)->withQueryString();
    }
    public function findOrFail(int $id): Riset
    {
        return Riset::findOrFail($id);
    }

    public function update(int $id, array $payload): Riset
    {
        $r = $this->findOrFail($id);
        $r->update($payload);
        return $r;
    }
    
}
