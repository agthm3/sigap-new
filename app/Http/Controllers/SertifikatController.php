<?php

namespace App\Http\Controllers;

use App\Models\SertifikatKegiatan;
use App\Models\SertifikatPeserta;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SertifikatImport;

class SertifikatController extends Controller
{
    public function index(Request $request)
    {
        // Mulai query builder dengan menghitung jumlah sertifikat terkait
        $query = SertifikatKegiatan::withCount('sertifikat');

        // Cek jika ada input 'search' dari user
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kegiatan', 'like', '%' . $search . '%')
                  ->orWhere('tanggal', 'like', '%' . $search . '%')
                  ->orWhere('tempat', 'like', '%' . $search . '%');
            });
        }

        // Ambil data dengan pagination
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
        // $file = public_path('template/template-sertifikat.xlsx');
        $file = $_SERVER['DOCUMENT_ROOT'].'/template/template-sertifikat.xlsx';

        return response()->download($file);
    }
}
