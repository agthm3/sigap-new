<?php

namespace App\Http\Controllers;

use App\Models\EvidenceReviewItem;
use Illuminate\Http\Request;
use App\Models\Inovasi;
use App\Models\InovasiReviewItem;
use App\Models\InovasiReviewTemplate;
use App\Repositories\EvidenceRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InovasiReviewController extends Controller
{
    public function form($id)
    {
        $inovasi = Inovasi::with(['referensiVideos', 'evidenceReviewItems.reviewer'])->findOrFail($id);

        // ambil template (master field)
        $templates = InovasiReviewTemplate::all();

        // ambil review yang sudah ada
        $existing = InovasiReviewItem::where('inovasi_id', $id)
            ->where('reviewer_id', Auth::id())
            ->get()
            ->keyBy('field');
        $reviewers = InovasiReviewItem::where('inovasi_id', $id)
            ->with('reviewer')
            ->get()
            ->groupBy('reviewer_id');
        $inovasi->file_urls = [
            'anggaran' => $inovasi->anggaran ? asset('storage/'.$inovasi->anggaran) : null,
            'profil_bisnis' => $inovasi->profil_bisnis ? asset('storage/'.$inovasi->profil_bisnis) : null,
            'haki' => $inovasi->haki ? asset('storage/'.$inovasi->haki) : null,
            'penghargaan' => $inovasi->penghargaan ? asset('storage/'.$inovasi->penghargaan) : null,
        ];

        // ── EVIDENCE untuk ditampilkan di halaman review ──
        $evidenceRepo   = app(EvidenceRepository::class);
        $evidenceItems  = $evidenceRepo->listForInovasi($id);   // array 20 item

        // Review evidence yang sudah ada (milik reviewer ini)
        $existingEvRev  = EvidenceReviewItem::where('inovasi_id', $id)
            ->where('reviewer_id', Auth::id())
            ->get()
            ->keyBy('no');   // key by no (1–20)

        return view('dashboard.inovasi.review', compact(
            'inovasi',
            'templates',
            'existing',
            'reviewers',
            'evidenceItems',
            'existingEvRev'
        ));
    }

    // public function store(Request $request, $id)
    // {
    //     // dd($request->all());
    //     $templates = InovasiReviewTemplate::all();

    //     $totalPoint = 0;

    //     foreach ($templates as $tpl) {

    //         $status = $request->input("status.{$tpl->field}");
    //         $comment = $request->input("comment.{$tpl->field}");

    //         // 🔥 skip kalau belum direview
    //         if (!$status) {
    //             continue;
    //         }

    //         $point = ($status === 'accept') ? $tpl->point : 0;

    //         $reviews = InovasiReviewItem::where('inovasi_id', $id)->get();

    //         if ($reviews->contains('status', 'tolak')) {
    //             $overallStatus = 'Ditolak';
    //         } elseif ($reviews->contains('status', 'revisi')) {
    //             $overallStatus = 'Revisi';
    //         } elseif ($reviews->isNotEmpty() && $reviews->every(fn ($r) => $r->status === 'accept')) {
    //             $overallStatus = 'Disetujui';
    //         } else {
    //             $overallStatus = 'Menunggu Verifikasi';
    //         }

    //         Inovasi::where('id', $id)->update([
    //             'asistensi_status' => $overallStatus,
    //             'asistensi_at'     => now(),
    //             'asistensi_by'     => Auth::id(),
    //         ]);

    //         InovasiReviewItem::updateOrCreate(
    //             [
    //                 'inovasi_id' => $id,
    //                 'reviewer_id' => Auth::id(),
    //                 'field' => $tpl->field,
    //             ],
    //             [
    //                 'status' => $status,
    //                 'comment' => $comment,
    //                 'point' => $point,
    //             ]
    //         );
    //     }

    //     return redirect()
    //         ->route('sigap-inovasi.show', $id)
    //         ->with('success', "Review berhasil disimpan. Total poin: $totalPoint");
    // }
    public function store(Request $request, $id)
    {
        $templates = InovasiReviewTemplate::all();

        DB::transaction(function () use ($request, $id, $templates) {
            foreach ($templates as $tpl) {
                $status  = $request->input("status.{$tpl->field}");
                $comment = $request->input("comment.{$tpl->field}");

                if (!$status) {
                    continue;
                }

                $point = ($status === 'accept') ? $tpl->point : 0;

                InovasiReviewItem::updateOrCreate(
                    [
                        'inovasi_id'   => $id,
                        'reviewer_id'  => Auth::id(),
                        'field'        => $tpl->field,
                    ],
                    [
                        'status'  => $status,
                        'comment' => $comment,
                        'point'   => $point,
                    ]
                );
            }

            $reviews = InovasiReviewItem::where('inovasi_id', $id)->get();

            if ($reviews->contains('status', 'tolak')) {
                $overallStatus = 'Ditolak';
            } elseif ($reviews->contains('status', 'revisi')) {
                $overallStatus = 'Revisi';
            } elseif ($reviews->isNotEmpty() && $reviews->every(fn ($r) => $r->status === 'accept')) {
                $overallStatus = 'Disetujui';
            } else {
                $overallStatus = 'Menunggu Verifikasi';
            }

            Inovasi::where('id', $id)->update([
                'asistensi_status' => $overallStatus,
                'asistensi_at'     => now(),
                'asistensi_by'     => Auth::id(),
            ]);
        });

        return redirect()
            ->route('sigap-inovasi.show', $id)
            ->with('success', 'Review berhasil disimpan.');
    }

    public function reviewResult($id)
    {
        $inovasi = Inovasi::findOrFail($id);

        // semua review
        $reviews = InovasiReviewItem::where('inovasi_id', $id)
            ->with('reviewer')
            ->get();

        // group by field
        $grouped = $reviews->groupBy('field');

        // ambil template (biar urut & label)
        $templates = InovasiReviewTemplate::all();

        // total poin
        $totalPoint = $reviews->sum('point');

        // daftar reviewer unik
        $reviewers = $reviews->groupBy('reviewer_id');

        return view('dashboard.inovasi.review-result', compact(
            'inovasi',
            'grouped',
            'templates',
            'totalPoint',
            'reviewers'
        ));
    }

    public function storeEvidenceReview(Request $request, $id)
    {
        // Filter manual: buang nilai kosong, hanya simpan yang valid
        $statuses = array_filter(
            $request->input('ev_status', []),
            fn($v) => in_array($v, ['accept', 'revisi', 'tolak'])
        );

        if (empty($statuses)) {
            return back()->with('error_evidence', 'Pilih minimal satu status review evidence.');
        }

        $comments = $request->input('ev_comment', []);

        DB::transaction(function () use ($id, $statuses, $comments) {
            foreach ($statuses as $no => $status) {
                $no = (int) $no;
                if ($no < 1 || $no > 20) continue;

                EvidenceReviewItem::updateOrCreate(
                    [
                        'inovasi_id'  => $id,
                        'reviewer_id' => Auth::id(),
                        'no'          => $no,
                    ],
                    [
                        'status'  => $status,
                        'comment' => $comments[$no] ?? null,
                    ]
                );
            }
        });

        return redirect()
            ->route('inovasi.review', $id)
            ->with('success_evidence', 'Review evidence berhasil disimpan.');
    }
}
