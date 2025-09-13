<?php

namespace App\Http\Controllers;

use App\Repositories\EvidenceRepository;
use Illuminate\Http\Request;

class EvidenceController extends Controller
{
    public function __construct(private EvidenceRepository $repo) {}

       public function index(int $inovasiId)
    {
        return response()->json([
            'items' => $this->repo->listForInovasi($inovasiId),
            'total_weight' => $this->repo->totalWeight($inovasiId),
        ]);
    }

    public function store(Request $r, int $inovasiId)
    {
        $itemsJson = $r->input('data');
        $items = json_decode($itemsJson ?? '[]', true) ?: [];

        $files = [];
        for ($i=1; $i<=20; $i++) {
            if ($r->hasFile("file_{$i}")) $files[$i] = $r->file("file_{$i}");
        }

        $this->repo->upsertBulk($inovasiId, $items, $files);

        // ⬇️ kembalikan data terbaru agar Blade bisa re-render tanpa GET
        return response()->json([
            'ok'           => true,
            'total_weight' => $this->repo->totalWeight($inovasiId),
            'items'        => $this->repo->listForInovasi($inovasiId),
        ]);
    }

    public function destroyFile(int $inovasiId, int $no)
    {
        $this->repo->removeFile($inovasiId, $no);
        return response()->json(['ok'=>true]);
    }
}
