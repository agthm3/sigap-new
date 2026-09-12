<?php

namespace App\Http\Controllers;

use App\Models\ImaInovasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SigapImaExportController extends Controller
{
    /**
     * EKSPOR EXCEL (CSV via Stream) - Fleksibel: Full Data atau Hanya Operator
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        $query = ImaInovasi::with(['user', 'evidences.indicator']);

        if (!$user->hasAnyRole(['admin', 'superadmin', 'verif_inovasi'])) {
            $query->where('user_id', $user->id);
        }

        // Filter ID terpilih jika ada (dari checkbox)
        if ($request->filled('selected_ids')) {
            $ids = explode(',', $request->selected_ids);
            $query->whereIn('id', $ids);
        } else {
            // Terapkan Filter yang sama dengan index
            if ($request->filled('kategori')) $query->where('kategori_ima', $request->kategori);
            if ($request->filled('status')) $query->where('asistensi_status', $request->status);
            if ($request->filled('q')) {
                $query->where(function ($w) use ($request) {
                    $w->where('judul', 'like', "%{$request->q}%")
                      ->orWhere('operator_nama', 'like', "%{$request->q}%")
                      ->orWhere('opd_unit', 'like', "%{$request->q}%");
                });
            }
        }

        $inovasis = $query->get();
        $exportType = $request->query('type', 'full'); // Default 'full'

        $filename = ($exportType === 'operator' ? 'Rekap_Operator_IMA_' : 'Rekap_Data_Lengkap_IMA_') . date('Y-m-d_H-i') . ".csv";
        
        $headers = [
            "Content-type"        => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($inovasis, $exportType) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // BOM UTF-8

            if ($exportType === 'operator') {
                fputcsv($file, ['No', 'Judul Inovasi', 'OPD/Unit', 'Nama PIC', 'Jabatan PIC', 'Nomor WA', 'Email PIC', 'Status Inovasi'], ';');
                $no = 1;
                foreach ($inovasis as $inv) {
                    fputcsv($file, [
                        $no++,
                        $inv->judul,
                        $inv->opd_unit,
                        $inv->operator_nama,
                        $inv->operator_jabatan,
                        "'" . $inv->operator_wa,
                        $inv->operator_email,
                        $inv->asistensi_status
                    ], ';');
                }
            } else {
                fputcsv($file, [
                    'No', 'Judul Inovasi', 'Kategori', 'OPD/Unit', 'Nama Inisiator',
                    'Urusan Pemerintah', 'Asta Cita', 'Tahapan Inovasi', 'Waktu Uji Coba', 'Waktu Penerapan',
                    'Status Profil', 'Total Skor Sementara', 'Indikator Terisi',
                    'Nama PIC', 'Nomor WA'
                ], ';');

                $no = 1;
                foreach ($inovasis as $inv) {
                    $skorTotal = 0;
                    $indTerisi = $inv->evidences->unique('ima_indicator_id')->count();
                    foreach ($inv->evidences as $ev) {
                        if ($ev->review_status !== 'Ditolak') {
                            $skorTotal += ($ev->parameter_weight ?? 0) * ($ev->indicator->pengali ?? 1);
                        }
                    }

                    fputcsv($file, [
                        $no++,
                        $inv->judul,
                        $inv->kategori_ima,
                        $inv->opd_unit,
                        $inv->inisiator_nama,
                        $inv->urusan_pemerintah,
                        $inv->asta_cipta,
                        $inv->tahap_inovasi,
                        $inv->waktu_uji_coba ? $inv->waktu_uji_coba->format('d/m/Y') : '-',
                        $inv->waktu_penerapan ? $inv->waktu_penerapan->format('d/m/Y') : '-',
                        $inv->asistensi_status,
                        $skorTotal,
                        $indTerisi . ' / 20',
                        $inv->operator_nama,
                        "'" . $inv->operator_wa
                    ], ';');
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * EKSPOR BUKU PROFIL & EVIDENCE PDF (Bisa 1 Orang, Beberapa Orang, atau Semua)
     */
    public function exportPdfDetail(Request $request, $id = null)
    {
        $user = Auth::user();
        $query = ImaInovasi::with(['user', 'evidences.indicator', 'evidences.files', 'verifikator']);

        // 1. Ekspor Per 1 Inovasi Spesifik (via parameter URL {id})
        if ($id) {
            $inovasiSingle = $query->findOrFail($id);
            if ($user->id !== $inovasiSingle->user_id && !$user->hasAnyRole(['admin', 'superadmin', 'verif_inovasi'])) {
                abort(403, 'Akses ditolak.');
            }
            $inovasiList = collect([$inovasiSingle]);
        } 
        // 2. Ekspor Berdasarkan Checkbox / Seleksi Beberapa Inovasi
        elseif ($request->filled('selected_ids')) {
            $ids = explode(',', $request->selected_ids);
            if (!$user->hasAnyRole(['admin', 'superadmin', 'verif_inovasi'])) {
                $query->where('user_id', $user->id);
            }
            $inovasiList = $query->whereIn('id', $ids)->get();
        } 
        // 3. Ekspor Seluruh Inovasi (sesuai filter aktif)
        else {
            if (!$user->hasAnyRole(['admin', 'superadmin', 'verif_inovasi'])) {
                $query->where('user_id', $user->id);
            }
            if ($request->filled('kategori')) $query->where('kategori_ima', $request->kategori);
            if ($request->filled('status')) $query->where('asistensi_status', $request->status);
            if ($request->filled('q')) {
                $query->where(function ($w) use ($request) {
                    $w->where('judul', 'like', "%{$request->q}%")
                      ->orWhere('operator_nama', 'like', "%{$request->q}%")
                      ->orWhere('opd_unit', 'like', "%{$request->q}%");
                });
            }
            $inovasiList = $query->get();
        }

        if ($inovasiList->isEmpty()) {
            return back()->with('error', 'Tidak ada data inovasi yang dipilih untuk dicetak ke PDF.');
        }

        return view('dashboard.ima.export-pdf', compact('inovasiList'));
    }
}