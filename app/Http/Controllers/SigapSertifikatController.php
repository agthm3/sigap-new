<?php

namespace App\Http\Controllers;

use App\Models\SertifikatPeserta;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SigapSertifikatController extends Controller
{
    public function index()
    {
        return view('SigapSertifikat.index');
    }
    public function verifikasi(Request $request)
    {
        $request->validate([
            'nomor' => 'required'
        ]);

        $sertifikat = SertifikatPeserta::with('kegiatan')
            ->where('nomor_sertifikat',$request->nomor)
            ->where('status','Aktif')
            ->first();

        return view('SigapSertifikat.index',[
            'sertifikat'=>$sertifikat,
            'nomor'=>$request->nomor,
            'searched'=>true
        ]);
    }
    public function view($id)
    {
        $sertifikat = SertifikatPeserta::with('kegiatan')
            ->findOrFail($id);

        return view('SigapSertifikat.view', compact('sertifikat'));
    }

}
