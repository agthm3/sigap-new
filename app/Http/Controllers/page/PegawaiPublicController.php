<?php

namespace App\Http\Controllers\page;

use App\Http\Controllers\Controller;
use App\Models\PersonalDocument;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class PegawaiPublicController extends Controller
{
    public function __construct(private UserRepository $users) {}

    // /pegawai/hasil?q=&unit=&type=&year=
    public function search(Request $request)
    {
        $filters = [
            'q'      => $request->input('q'),
            'unit'   => $request->input('unit'),
            'status' => 'active',
            'sort'   => $request->input('sort','name'),
        ];
        $perPage = 10;
        $results = $this->users->paginateWithFilters($filters, $perPage);

        return view('SigapPegawai.results', [
            'results' => $results,
            'filters' => $filters,
        ]);
    }

    // /pegawai/{user}
    public function show(User $user)
    {
        // ambil dokumen pribadi milik user ini (hanya status verified/pending juga bisa ditampilkan)
        $docs = PersonalDocument::query()
            ->where('user_id',$user->id)
            ->latest()
            ->get();

        return view('SigapPegawai.detail', [
            'pegawai' => $user,
            'docs'    => $docs,
        ]);
    }

    // POST /pegawai/docs/{doc}/verify
    public function verify(Request $request, PersonalDocument $doc)
    {
        // alasan wajib (bisa disesuaikan SOP)
        $data = $request->validate([
            'access_code' => ['required','string','min:4'],
            'reason'      => ['required','string','max:200'],
        ]);

        // pemilik & admin tidak perlu kode; kalau bukan → cocokkan kode
        $me = Auth::user();
        $isOwner = $doc->user_id === $me->id;
        $isAdmin = $me->hasRole('admin');

        $ok = $isOwner || $isAdmin;
        if (!$ok) {
            $saved = $doc->access_code_enc ? Crypt::decryptString($doc->access_code_enc) : null;
            $ok = $saved && hash_equals($saved, $data['access_code']);
        }

        // tulis log selalu (sukses/gagal)
        DB::table('personal_document_logs')->insert([
            'personal_document_id' => $doc->id,
            'acted_by'   => $me->id,
            'action'     => 'verify_code',
            'extra'      => json_encode(['reason'=>$data['reason']]),
            'ip'         => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 1000),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (!$ok) {
            return back()->withErrors(['access_code'=>'Kode akses salah.'])->withInput();
        }

        // set sesi akses untuk doc ini
        $flag = "doc_access_{$doc->id}";
        session([$flag => now()->addMinutes(30)->toIso8601String()]);

        return back()->with('access_granted', true);
    }

    // GET /pegawai/docs/{doc}/view
public function view(Request $request, PersonalDocument $doc)
{
    $this->ensureAuthorized($request, $doc, 'view');

    // 🚫 pastikan tidak ada injeksi debugbar ke response binary
    if (app()->bound('debugbar')) {
        app('debugbar')->disable();
    }

    $disk = Storage::disk('private');
    $path = $disk->path($doc->path);

    // mime deteksi sederhana
    $mime = $doc->mime ?: (File::mimeType($path) ?: 'application/pdf');

    // nama file
    $filename = $doc->title ?: basename($path);
    if (!Str::of($filename)->contains('.')) {
        $filename .= '.pdf';
    }

    // Header MINIMAL (biarkan Laravel handle yang lain)
    return response()->file($path, [
        'Content-Type'        => $mime,
        'Content-Disposition' => 'inline; filename="'.Str::of($filename)->replace('"','\"').'"',
    ]);
}

    // GET /pegawai/docs/{doc}/download
    public function download(Request $request, PersonalDocument $doc)
    {
        $this->ensureAuthorized($request, $doc, 'download');

        DB::table('personal_document_logs')->insert([
            'personal_document_id' => $doc->id,
            'acted_by'   => Auth::id(),
            'action'     => 'download',
            'ip'         => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 1000),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Storage::disk('private')->download($doc->path, $doc->title.'.pdf');
    }

    private function ensureAuthorized(Request $request, PersonalDocument $doc, string $action)
    {
        $me = Auth::user();

        if ($me->hasRole('admin') || $doc->user_id === $me->id) {
            // admin/owner OK
        } else {
            $flag = "doc_access_{$doc->id}";
            $untilStr = $request->session()->get($flag);

            if (!$untilStr) {
                abort(403, 'Verifikasi kode akses dibutuhkan.');
            }

            try {
                $until = \Carbon\Carbon::parse($untilStr);
            } catch (\Throwable $e) {
                // kalau formatnya aneh/korup, hapus sesi dan paksa verifikasi ulang
                $request->session()->forget($flag);
                abort(403, 'Sesi akses tidak valid. Silakan verifikasi ulang.');
            }

            if (now()->greaterThan($until)) {
                $request->session()->forget($flag);
                abort(403, 'Sesi akses kedaluwarsa.');
            }
        }

        // catat view/preview
        DB::table('personal_document_logs')->insert([
            'personal_document_id' => $doc->id,
            'acted_by'   => $me->id,
            'action'     => $action === 'view' ? 'preview' : $action,
            'ip'         => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 1000),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

}
