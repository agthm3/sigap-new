<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFormatRequest;
use App\Http\Requests\UpdateFormatRequest;
use App\Models\FormatTemplate;
use App\Repositories\FormatTemplateRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Storage;

class SigapFormatController extends Controller
{
    public function __construct(private FormatTemplateRepository $repo)
    {
        //
    }

    public function index(Request $request)
    {
        $filters = $request->only(['q','category','file_type','privacy','lang','orientation','sort']);
        $templates = $this->repo->paginate($filters, perPage: 12);

        return view('dashboard.format.index', compact('templates', 'filters'));
    }

    public function store(StoreFormatRequest $request)
    {
        $t = $this->repo->create($request->validated(), $request->file('file'), $request->user()->id);

        return redirect()
            ->route('format.index')
            ->with('success', 'Template berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $template = $this->repo->findOrFail($id);
        $this->authorizeAdmin();

        return view('dashboard.format.edit', compact('template'));
    }

    public function update(UpdateFormatRequest $request, int $id)
    {
        $template = $this->repo->findOrFail($id);
        $t = $this->repo->update($template, $request->validated(), $request->file('file'));

        return redirect()
            ->route('format.index')
            ->with('success', 'Template berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $template = $this->repo->findOrFail($id);
        $this->authorizeAdmin();
        $this->repo->delete($template);

        return redirect()
            ->route('format.index')
            ->with('success', 'Template berhasil dihapus.');
    }

    /**
     * Download file (untuk dashboard).
     * - Admin: langsung download
     * - Non-admin: jika private, tolak di sini (untuk dashboard). (Untuk publik page, nanti kita munculkan modal kode.)
     */
    public function download(Request $request, int $id): StreamedResponse
    {
        $t = $this->repo->findOrFail($id);

        // Admin selalu boleh
        if ($request->user()->hasRole('admin')) {
            $this->repo->logAccess($t, $request->user()->id, 'download', true);
            return Storage::disk('public')->download($t->file_path, $t->original_name);
        }

        // Non-admin: hanya boleh jika public
        if ($t->privacy === 'public') {
            $this->repo->logAccess($t, $request->user()->id, 'download', true);
            return Storage::disk('public')->download($t->file_path, $t->original_name);
        }

        // private → untuk dashboard, kita kirim 403 (di halaman publik nanti pakai modal & verifikasi)
        $this->repo->logAccess($t, $request->user()->id, 'download', false);
        abort(403, 'Dokumen ini bersifat privasi. Gunakan halaman publik untuk verifikasi kode akses.');
    }

    public function unlockAndDownload(Request $request, int $id)
    {
        $t = $this->repo->findOrFail($id);

        // Jika admin → tidak perlu kode
        if ($request->user()->hasRole('admin')) {
            $this->repo->logAccess($t, $request->user()->id, 'download', true);
            return \Storage::disk('public')->download($t->file_path, $t->original_name);
        }

        // Validasi input
        $request->validate([
            'access_code' => ['required','string','min:3'],
        ]);

        // Hanya relevan kalau private
        if ($t->privacy !== 'private') {
            // publik → download langsung
            $this->repo->logAccess($t, $request->user()->id, 'download', true);
            return \Storage::disk('public')->download($t->file_path, $t->original_name);
        }

        // Cek kode
        $ok = $this->repo->verifyAccessCode($t, $request->input('access_code'));
        if (!$ok) {
            $this->repo->logAccess($t, $request->user()->id, 'download', false);
            // Kirim flash agar index bisa munculkan SweetAlert
            return redirect()
                ->route('format.index', ['bad' => $t->id])
                ->with('format_error_id', $t->id)
                ->with('format_error_msg', 'Kode akses salah. Silakan hubungi admin.');
        }

        // Benar → download
        $this->repo->logAccess($t, $request->user()->id, 'download', true);
        return \Storage::disk('public')->download($t->file_path, $t->original_name);
    }


    private function authorizeAdmin(): void
    {
        if (!auth()->user()?->hasRole('admin')) {
            abort(403);
        }
    }
}
