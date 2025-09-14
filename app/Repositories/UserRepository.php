<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository
{
    public function paginateWithFilters(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        $q = User::query()->with('roles'); // eager-load roles

        if (!empty($filters['q'])) {
            $s = $filters['q'];
            $q->where(function($w) use ($s) {
                $w->where('name', 'like', "%{$s}%")
                  ->orWhere('username', 'like', "%{$s}%")
                  ->orWhere('nip', 'like', "%{$s}%")
                  ->orWhere('unit', 'like', "%{$s}%");
            });
        }

        if (!empty($filters['unit']))   $q->where('unit', $filters['unit']);
        if (!empty($filters['status'])) $q->where('status', $filters['status']);

        // ⬇️ filter role via Spatie
        if (!empty($filters['role'])) {
            $q->whereHas('roles', fn($r) => $r->where('name', $filters['role']));
        }

        switch ($filters['sort'] ?? 'latest') {
            case 'name': $q->orderBy('name'); break;
            case 'unit': $q->orderBy('unit'); break;
            default:     $q->latest(); break;
        }

        return $q->paginate($perPage)->withQueryString();
    }

    public function create(array $data, array $roleNames = []): User
    {
        $user = User::create($data);
        if ($roleNames) $user->syncRoles($roleNames);
        return $user;
    }

    public function update(User $user, array $data, array $roleNames = []): User
    {
        $user->update($data);
        $user->syncRoles($roleNames);
        return $user;
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}