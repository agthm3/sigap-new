<?php

namespace App\Http\Controllers;

use App\Models\Riset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Repositories\RisetRepository;

class SigapRisetController extends Controller
{
    public function __construct(private RisetRepository $repo) {}
    private function ensurePrivateStorageForRestricted(\App\Models\Riset $riset): void
    {
        $access = strtoupper(trim($riset->access ?? 'PUBLIC'));
        if ($access === 'PUBLIC') return;

        $path = $riset->file_path;
        if (!$path) return;

        // Normalisasi: buang prefix "storage/" jika ada
        $path = ltrim(preg_replace('#^storage/#', '', $path), '/');

        // kalau sudah di private, selesai
        if (\Storage::disk('riset_private')->exists($path)) return;

        // kalau file masih di public, pindahkan
        if (\Storage::disk('public')->exists($path)) {
            $contents = \Storage::disk('public')->get($path);
            \Storage::disk('riset_private')->put($path, $contents);
            \Storage::disk('public')->delete($path);
            // simpan kembali path yg sudah dinormalisasi (tanpa "storage/")
            $riset->file_path = $path;
            $riset->save();
        }
    }
    public function index(Request $request)
    {
        $filters = $request->only([
            'q','tags','author','stakeholder','year','type','sort','only_public','has_doi'
        ]);

        $risets = $this->repo->searchPublic($filters, 8);

        return view('SigapRiset.index', compact('risets'));
    }
    

    public function show(Riset $riset)
    {
        $this->ensurePrivateStorageForRestricted($riset);
        $baseYear = is_numeric($riset->year) ? (int) $riset->year : (int) now()->year;

            $related = Riset::query()
                ->where('id', '<>', $riset->id)
                ->when(is_array($riset->tags) && count($riset->tags) > 0, function ($q) use ($riset) {
                    $tag = $riset->tags[0];
                    $q->whereJsonContains('tags', $tag);
                })
                // CAST ke SIGNED + COALESCE utk NULL
                ->orderByRaw('ABS(CAST(COALESCE(`year`, ?) AS SIGNED) - ?)', [$baseYear, $baseYear])
                ->limit(5)
                ->get();


        $isPublic   = ($riset->access ?? 'Public') === 'Public';
        $thumbUrl   = $riset->thumbnail_path ? Storage::disk('public')->url($riset->thumbnail_path) : null;

        // JANGAN expose URL PDF di view jika Restricted
        $pdfUrl     = null;
        $canPreview = false;

        if ($isPublic && $riset->file_path && Storage::disk('public')->exists($riset->file_path)) {
            $pdfUrl     = Storage::disk('public')->url($riset->file_path); // aman untuk public
            $canPreview = true;
        }

        return view('SigapRiset.show', [
            'research'    => $riset,
            'related'     => $related,
            'pdfUrl'      => $pdfUrl,
            'thumbUrl'    => $thumbUrl,
            'canPreview'  => $canPreview,
            'isPublic'    => $isPublic,
        ]);
    }

    public function download(Riset $riset)
    {
        $this->ensurePrivateStorageForRestricted($riset);
        $isPublic = ($riset->access ?? 'Public') === 'Public';

        if ($isPublic) {
            // Public: pastikan file di disk public, lalu redirect ke response file
            if (!$riset->file_path || !Storage::disk('public')->exists($riset->file_path)) {
                abort(404, 'Berkas tidak ditemukan.');
            }
            // Boleh langsung stream/response download supaya ter-log juga
            return Storage::disk('public')->download($riset->file_path, basename($riset->file_path));
        }

        // Restricted: TOLAK + beri pesan jelas
        // Di sini juga tempat ideal untuk tulis LOG percobaan akses.
        // contoh minimal:
        // \Log::warning('Restricted riset access attempt', ['riset_id'=>$riset->id, 'ip'=>request()->ip(), 'user_id'=>optional(auth()->user())->id]);

        return redirect()
            ->route('sigap-riset.show', $riset->id)
            ->with('restricted_msg', 'Dokumen atau riset ini memiliki akses terbatas. Silakan hubungi admin melalui email untuk mendapatkan akses penuh.');
    }
    // CREATE
    public function store(Request $request, RisetRepository $repo)
    {
        $payload = $request->all(); // validasi sesuai kebutuhanmu
        $file = $request->file('pdf') ?? $request->file('file'); // nama input bebas
        $riset = $repo->create($payload, $file);
        return redirect()->route('sigap-riset.show', $riset->id);
    }

    // UPDATE
    public function update(Request $request, Riset $riset, RisetRepository $repo)
    {
        $payload = $request->all();
        $file = $request->file('pdf') ?? $request->file('file');
        $repo->update($riset->id, $payload, $file);
        return redirect()->route('sigap-riset.show', $riset->id);
    }
}
