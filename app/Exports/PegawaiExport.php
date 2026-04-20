<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class PegawaiExport implements FromCollection
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = User::query()->with('roles');

        // FILTER ROLE (Spatie)
        if (!empty($this->filters['role'])) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', $this->filters['role']);
            });
        }

        // FILTER UNIT
        if (!empty($this->filters['unit'])) {
            $query->where('unit', 'like', '%' . $this->filters['unit'] . '%');
        }

        // FILTER STATUS
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query->get()->map(function ($user) {
            return [
                'Nama'   => $user->name,
                'Username' => $user->username,
                'NIP'    => $user->nip,
                'Unit'   => $user->unit,
                'Role'   => $user->getRoleNames()->implode(', '),
                'Email'  => $user->email,
                'Phone'  => $user->phone,
                'Status' => $user->status,
            ];
        });
    }
}