<?php

namespace App\Http\Controllers;

use App\Models\SertifikatKegiatan;
use App\Models\SertifikatPeserta;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SertifikatImport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

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

    // 1. Mengaktifkan view kanvas bawaan
    public function viewSertifikat($id)
    {
        $sertifikat = SertifikatPeserta::with('kegiatan')->findOrFail($id);

        return view('SigapSertifikat.view', compact('sertifikat'));
    }

    // 2. Export list sertifikat ke PDF
    public function exportPdf($id)
    {
        $kegiatan = SertifikatKegiatan::with('sertifikat')->findOrFail($id);

        $pdf = Pdf::loadView('dashboard.sertifikat.pdf', compact('kegiatan'))
                  ->setPaper('letter', 'portrait');

        return $pdf->download('Daftar_Sertifikat_' . Str::slug($kegiatan->nama_kegiatan) . '.pdf');
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

    /**
     * Update data sertifikat peserta
     */
    public function updateSertifikat(Request $request, $id)
    {
        $peserta = SertifikatPeserta::findOrFail($id);

        $request->validate([
            'nomor_sertifikat' => 'required|unique:sertifikat_pesertas,nomor_sertifikat,' . $peserta->id,
            'nama_penerima'    => 'required',
            'status'           => 'nullable|in:Aktif,Nonaktif'
        ]);

        $peserta->update([
            'nomor_sertifikat' => $request->nomor_sertifikat,
            'nama_penerima'    => $request->nama_penerima,
            'instansi'         => $request->instansi,
            'keterangan'       => $request->keterangan,
            'status'           => $request->status ?? $peserta->status,
        ]);

        return back()->with('success', 'Data sertifikat peserta berhasil diperbarui.');
    }

    /**
     * Hapus satu data sertifikat peserta
     */
    public function destroySertifikat($id)
    {
        $peserta = SertifikatPeserta::findOrFail($id);
        $peserta->delete();

        return back()->with('success', 'Data sertifikat berhasil dihapus.');
    }
}