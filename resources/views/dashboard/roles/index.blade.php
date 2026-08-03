@extends('layouts.app')

@section('content')
<section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      Manajemen <span class="text-maroon">Role</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Kelola daftar hak akses dan role sistem (Spatie Permission).
    </p>
  </div>

  <a href="{{ route('roles.create') }}"
     class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 transition-colors">
    + Tambah Role
  </a>
</section>

<div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm mt-4">
  <div class="px-4 py-3 border-b bg-gray-50 flex justify-between items-center">
    <h2 class="font-semibold text-gray-900">Daftar Role</h2>
    <span class="text-xs text-gray-500">Total: {{ $roles->total() }} Role</span>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase text-gray-600">
        <tr>
          <th class="px-4 py-3 text-left">No</th>
          <th class="px-4 py-3 text-left">Nama Role</th>
          <th class="px-4 py-3 text-left">Jumlah User</th>
          <th class="px-4 py-3 text-left">Guard Name</th>
          <th class="px-4 py-3 text-left">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($roles as $index => $role)
          <tr>
            <td class="px-4 py-3 font-medium text-gray-900">
              {{ $roles->firstItem() + $index }}
            </td>
            <td class="px-4 py-3 font-semibold text-maroon">
              <span class="inline-flex px-2.5 py-1 rounded-full text-xs bg-maroon/10 border border-maroon/20 text-maroon">
                {{ $role->name }}
              </span>
            </td>
            <td class="px-4 py-3 text-gray-600">
              {{ $role->users_count }} Pengguna
            </td>
            <td class="px-4 py-3 text-gray-500 text-xs">
              {{ $role->guard_name }}
            </td>
            <td class="px-4 py-3">
              <div class="flex items-center gap-2">
                <a href="{{ route('roles.edit', $role->id) }}"
                   class="px-3 py-1.5 rounded border border-gray-300 text-xs hover:bg-gray-50 transition-colors">
                  Edit
                </a>

                @if(!in_array($role->name, ['admin', 'employee']))
                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="form-delete inline">
                  @csrf
                  @method('DELETE')
                  <button type="button"
                          class="btn-delete px-3 py-1.5 rounded border border-red-500 text-red-600 text-xs hover:bg-red-600 hover:text-white transition-colors"
                          data-role="{{ $role->name }}">
                    Hapus
                  </button>
                </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-4 py-6 text-center text-gray-500">
              Belum ada data role.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-4">
  {{ $roles->links() }}
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.btn-delete').forEach(function(btn) {
    btn.addEventListener('click', function () {
      const form = this.closest('form');
      const roleName = this.dataset.role;

      Swal.fire({
        title: 'Hapus Role?',
        html: `Role <b>${roleName}</b> akan dihapus dari sistem!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#7a2222',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  });
});
</script>
@endpush