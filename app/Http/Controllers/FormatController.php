<?php

namespace App\Http\Controllers;

use App\Models\FormatTemplate;
use App\Repositories\FormatTemplateRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FormatController extends Controller
{
    public function __construct(private FormatTemplateRepository $repo) {}

    /**
     * List publik: pakai repository paginate() + dekorasi thumbnail.
     */
    public function index(Request $request)
    {
        $filters   = $request->only(['q','category','file_type','privacy','lang','orientation','sort']);
        // default publik → hanya tampilkan public. (boleh override jika admin)
        // if (!auth()->check() || !auth()->user()->hasRole('admin')) {
        //     $filters['privacy'] = 'public';
        // }

        $templates = $this->repo->paginate($filters, 12);

        // Decorate: thumbnail_url (gambar asli jika image; kalau bukan → dummy)
        $imageTypes = ['PNG','JPG','JPEG','SVG','WEBP'];
        foreach ($templates as $t) {
            $type = strtoupper($t->file_type);
            if (in_array($type, $imageTypes)) {
                try {
                    $t->thumbnail_url = Storage::disk('public')->url($t->file_path);
                } catch (\Throwable $e) {
                    $t->thumbnail_url = "https://picsum.photos/seed/format{$t->id}/480/300";
                }
            } else {
                $t->thumbnail_url = "https://picsum.photos/seed/format{$t->id}/480/300";
            }
        }

        return view('SigapFormat.index', compact('templates','filters'));
    }

    /**
     * Unlock untuk non-admin (privasi): validasi kode, lalu:
     * - intent=show → set session unlocked dan redirect ke show
     * - intent=download → langsung stream download
     */
    public function unlock(Request $request, int $id)
    {
        $t = $this->repo->findOrFail($id);

        // Admin tak perlu kode
        if (auth()->check() && auth()->user()->hasRole('admin')) {
            return $this->afterUnlock($request, $t);
        }

        $request->validate([
            'access_code' => ['required','string','min:3'],
            'intent'      => ['required','in:show,download'],
        ]);

        // Publik tanpa privasi → langsung
        if ($t->privacy !== 'private') {
            return $this->afterUnlock($request, $t);
        }

        // Cek kode
        if (!$this->repo->verifyAccessCode($t, $request->input('access_code'))) {
            $this->repo->logAccess($t, auth()->id(), 'download', false);
            return back()
                ->with('format_error_msg', 'Kode akses salah. Silakan hubungi admin.')
                ->withInput();
        }

        // Simpan sesi unlock untuk akses detail/preview selanjutnya
        session()->put("format_unlocked.{$t->id}", true);

        return $this->afterUnlock($request, $t);
    }

    /**
     * Aksi setelah unlock sukses (atau tidak perlu unlock).
     */
    private function afterUnlock(Request $request, FormatTemplate $t)
    {
        $intent = $request->input('intent', 'show');

        if ($intent === 'download') {
            // Download publik
            $this->repo->logAccess($t, auth()->id(), 'download', true);
            return Storage::disk('public')->download($t->file_path, $t->original_name);
        }

        // intent show → redirect ke detail
        return redirect()->route('sigap-format.show', $t->id);
    }

    /**
     * Detail. Jika private & bukan admin → wajib session unlocked.
     */
    public function show(int $id)
    {
        $t = $this->repo->findOrFail($id);

        if ($t->privacy === 'private' && !(auth()->check() && auth()->user()->hasRole('admin'))) {
            if (!session()->get("format_unlocked.$id")) {
                return redirect()->route('sigap-format.index')
                    ->with('format_error_msg', 'Dokumen privasi. Masukkan kode akses untuk melihat detail.');
            }
        }

        $previewUrl = Storage::disk('public')->url($t->file_path);

        // convert tags agar aman di blade
        if (is_string($t->tags)) {
            $t->tags = array_values(array_filter(array_map('trim', explode(',', $t->tags))));
        }

        return view('SigapFormat.show', [
            'template'   => $t,
            'previewUrl' => $previewUrl,
        ]);
    }

    /**
     * Preview (khusus PDF/Office/Image). Sama aturan dengan show.
     */
    public function preview(int $id)
    {
        $t = $this->repo->findOrFail($id);

        if ($t->privacy === 'private' && !(auth()->check() && auth()->user()->hasRole('admin'))) {
            if (!session()->get("format_unlocked.$id")) {
                return redirect()->route('sigap-format.index')
                    ->with('format_error_msg', 'Dokumen privasi. Masukkan kode akses untuk melihat pratinjau.');
            }
        }

        $url = Storage::disk('public')->url($t->file_path);

        return view('SigapFormat.show', [
            'template'   => $t,
            'previewUrl' => $url,
        ]);
    }

    /**
     * Download langsung (tanpa modal) dari halaman detail.
     * - Public: boleh jika public, atau private tapi sudah unlocked (session).
     * - Admin: selalu boleh.
     */
    public function download(Request $request, int $id)
    {
        $t = $this->repo->findOrFail($id);

        if (auth()->check() && auth()->user()->hasRole('admin')) {
            $this->repo->logAccess($t, auth()->id(), 'download', true);
            return Storage::disk('public')->download($t->file_path, $t->original_name);
        }

        if ($t->privacy === 'private' && !session()->get("format_unlocked.$id")) {
            return back()->with('format_error_msg', 'Dokumen privasi. Masukkan kode akses terlebih dahulu.');
        }

        $this->repo->logAccess($t, auth()->id(), 'download', true);
        return Storage::disk('public')->download($t->file_path, $t->original_name);
    }
}
