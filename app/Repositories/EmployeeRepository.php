<?php

namespace App\Repositories;

use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EmployeeRepository
{
    public function paginateWithFilters(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        $q = Employee::query();

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
        if (!empty($filters['role']))   $q->where('role', $filters['role']);
        if (!empty($filters['status'])) $q->where('status', $filters['status']);

        switch ($filters['sort'] ?? 'latest') {
            case 'name': $q->orderBy('name'); break;
            case 'unit': $q->orderBy('unit'); break;
            default:     $q->latest(); break;
        }

        return $q->paginate($perPage)->withQueryString();
    }

    public function find(int $id): ?Employee
    {
        return Employee::find($id);
    }

    public function findByUsername(string $username): ?Employee
    {
        return Employee::where('username', $username)->first();
    }

    public function create(array $data): Employee
    {
        return Employee::create($data);
    }

    public function update(Employee $employee, array $data): Employee
    {
        $employee->update($data);
        return $employee;
    }

    public function delete(Employee $employee): void
    {
        $employee->delete();
    }
}
