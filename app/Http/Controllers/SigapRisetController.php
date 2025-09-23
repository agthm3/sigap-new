<?php

namespace App\Http\Controllers;

use App\Models\Riset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Repositories\RisetRepository;

class SigapRisetController extends Controller
{
    public function __construct(private RisetRepository $repo) {}

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
        // contoh simple related (berdasar tag sama & tahun mendekati)
        $related = Riset::query()
            ->where('id', '<>', $riset->id)
            ->when(is_array($riset->tags) && count($riset->tags)>0, function($q) use ($riset){
                // ambil max 1 tag pertama untuk pencocokan sederhana
                $tag = $riset->tags[0];
                $q->whereJsonContains('tags', $tag);
            })
            ->orderByRaw('ABS(year - ?)', [$riset->year ?? now()->year])
            ->limit(5)
            ->get();

        // URL file (jika public disk)
        $pdfUrl  = $riset->file_path ? Storage::disk('public')->url($riset->file_path) : null;
        $thumbUrl= $riset->thumbnail_path ? Storage::disk('public')->url($riset->thumbnail_path) : null;
        $isPublic = ($riset->access ?? 'Public') === 'Public';
        $canPreview = $isPublic && $pdfUrl; // nanti bisa ditambah cek file_exists kalau mau

        return view('SigapRiset.show', [
            'research' => $riset,
            'related'  => $related,
            'pdfUrl'   => $pdfUrl,
            'thumbUrl' => $thumbUrl,
            'canPreview' => $canPreview,
        ]);
    }
}
