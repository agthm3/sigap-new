<?php

namespace App\Http\Controllers;

use App\Models\SertifikatKegiatan;
use App\Models\SertifikatPeserta;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SertifikatImport;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SertifikatController extends Controller
{
    public function index(Request $request)
    {
        $query = SertifikatKegiatan::withCount('sertifikat');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kegiatan', 'like', '%' . $search . '%')
                  ->orWhere('tanggal', 'like', '%' . $search . '%')
                  ->orWhere('tempat', 'like', '%' . $search . '%');
            });
        }

        $kegiatan = $query->latest()->paginate(10)->withQueryString();

        return view('dashboard.sertifikat.dashboard', compact('kegiatan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required',
            'jenis' => 'required',
            'tanggal' => 'required',
            'tempat' => 'required',
            'status' => 'required'
        ]);

        SertifikatKegiatan::create([
            'nama_kegiatan' => $request->nama_kegiatan,
            'jenis' => $request->jenis,
            'tempat' => $request->tempat,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'status' => $request->status
        ]);

        return redirect()
            ->route('sigap-sertifikat.dashboard')
            ->with('success','Kegiatan berhasil dibuat');
    }

    public function show($id)
    {
        $kegiatan = SertifikatKegiatan::findOrFail($id);

        $sertifikat = SertifikatPeserta::where('kegiatan_id',$id)
                        ->latest()
                        ->get();

        return view('dashboard.sertifikat.show',compact(
            'kegiatan',
            'sertifikat'
        ));
    }

    /**
     * Tampilkan kanvas Sertifikat Digital per peserta
     */
    public function viewSertifikat($id)
    {
        $sertifikat = SertifikatPeserta::with('kegiatan')->findOrFail($id);

        return view('SigapSertifikat.view', compact('sertifikat'));
    }

    /**
     * Export Daftar Penerima Sertifikat ke PDF
     */
    public function exportPdf($id)
    {
        $kegiatan = SertifikatKegiatan::with('sertifikat')->findOrFail($id);

        // Path logo Pemkot dan Brida ke Base64 (agar aman di render DomPDF)
        $logoPemkotPath = public_path('images/sertifikat/logo-pemkot.png');
        $logoBridaPath  = public_path('images/sertifikat/logo-brida.png');

        $logoPemkot = file_exists($logoPemkotPath) 
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPemkotPath)) 
            : null;

        $logoBrida = file_exists($logoBridaPath) 
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoBridaPath)) 
            : null;

        // URL Verifikasi & QR Code
        $verifikasiUrl = route('sertifikat.show', $kegiatan->id);
        $qrVerifikasi = base64_encode(QrCode::format('png')->size(120)->margin(0)->generate($verifikasiUrl));

        $pdf = Pdf::loadView('dashboard.sertifikat.pdf', compact(
            'kegiatan',
            'logoPemkot',
            'logoBrida',
            'verifikasiUrl',
            'qrVerifikasi'
        ))->setPaper('letter', 'portrait');

        $filename = 'Daftar_Sertifikat_' . \Str::slug($kegiatan->nama_kegiatan) . '.pdf';

        return $pdf->download($filename);
    }

    public function storeSertifikat(Request $request)
    {
        $request->validate([
            'kegiatan_id'=>'required',
            'nomor_sertifikat'=>'required|unique:sertifikat_pesertas',
            'nama_penerima'=>'required'
        ]);

        SertifikatPeserta::create($request->all());

        return back()->with('success','Sertifikat berhasil ditambahkan');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file'=>'required|mimes:xlsx,xls'
        ]);

        Excel::import(
            new SertifikatImport($request->kegiatan_id),
            $request->file('file')
        );

        return back()->with('success','Data sertifikat berhasil diimport');
    }

    public function downloadTemplate()
    {
        $file = $_SERVER['DOCUMENT_ROOT'].'/template/template-sertifikat.xlsx';

        return response()->download($file);
    }

    public function destroy($id)
    {
        $kegiatan = SertifikatKegiatan::findOrFail($id);

        SertifikatPeserta::where('kegiatan_id', $id)->delete();
        $kegiatan->delete();

        return redirect()
            ->route('sigap-sertifikat.dashboard')
            ->with('success', 'Kegiatan sertifikat beserta seluruh data terkait berhasil dihapus.');
    }
}