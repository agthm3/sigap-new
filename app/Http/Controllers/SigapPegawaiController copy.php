<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Repositories\EmployeeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class SigapPegawaiController extends Controller
{
 public function __construct(private EmployeeRepository $repo) {}

    public function index(Request $request)
    {
        $filters   = $request->only(['q','unit','role','status','sort']);
        $perPage   = (int) $request->input('per_page', 25);
        $employees = $this->repo->paginateWithFilters($filters, $perPage);

        return view('dashboard.pegawai.index', compact('employees', 'filters'));
    }

    public function create()
    {
        $roles = Role::query()->where('guard_name','web')->pluck('name')->all();
        return view('dashboard.pegawai.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validRoleNames = Role::query()->where('guard_name','web')->pluck('name')->all();

        $validated = $request->validate([
            'name'         => ['required','string','max:255'],
            'username'     => ['required','string','max:100','unique:employees,username'],
            'nip'          => ['nullable','string','max:50'],
            'unit'         => ['required','string','max:100'],
            'role'         => ['required','in:pegawai,verifikator,admin'],
            'status'       => ['nullable','in:active,inactive'],
            'phone'        => ['nullable','string','max:50'],
            'email'        => ['nullable','email','max:255'],
            'make_account' => ['nullable','in:yes,no'],
            'password'     => [$request->input('make_account','yes')==='yes' ? 'required' : 'nullable','string','min:8'],
            'avatar'       => ['nullable','image','mimes:jpg,jpeg,png','max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }

        // NOTE: kalau mau buat akun login di tabel users, tambahkan di sini (opsional)

        $this->repo->create($validated);

        return redirect()->route('sigap-pegawai.index')->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('dashboard.pegawai.edit', compact('employee'));
    }
    public function update(Request $request, Employee $sigap_pegawai)
    {
        $employee = $sigap_pegawai;

        $validated = $request->validate([
            'name'     => ['required','string','max:255'],
            'username' => ['required','string','max:100',"unique:employees,username,{$employee->id}"],
            'nip'      => ['nullable','string','max:50'],
            'unit'     => ['required','string','max:100'],
            'role'     => ['required','in:pegawai,verifikator,admin'],
            'status'   => ['nullable','in:active,inactive'],
            'phone'    => ['nullable','string','max:50'],
            'email'    => ['nullable','email','max:255'],
            'avatar'   => ['nullable','image','mimes:jpg,jpeg,png','max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            if ($employee->avatar_path) Storage::disk('public')->delete($employee->avatar_path);
            $validated['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }

        $this->repo->update($employee, $validated);

        return back()->with('success', 'Perubahan tersimpan.');
    }

    public function destroy(Employee $sigap_pegawai)
    {
        $employee = $sigap_pegawai;

        if ($employee->avatar_path) Storage::disk('public')->delete($employee->avatar_path);
        $this->repo->delete($employee);

        return redirect()->route('sigap-pegawai.index')->with('success', 'Pegawai dihapus.');
    }
}
