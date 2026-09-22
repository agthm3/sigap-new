<?php

namespace App\Http\Controllers;

use App\Models\KgbGajiPokok;
use Illuminate\Http\Request;

class KgbMasterGajiController extends Controller
{
    public function index(Request $request)
    {
        $query = KgbGajiPokok::query();

        if ($request->filled('jenis_pegawai')) {
            $query->where('jenis_pegawai', $request->jenis_pegawai);
        }

        if ($request->filled('golongan')) {
            $query->where('golongan', $request->golongan);
        }

        $gajis = $query->orderBy('jenis_pegawai', 'asc')
            ->orderBy('golongan', 'asc')
            ->orderBy('masa_kerja', 'asc')
            ->paginate(15);

        $daftarGolongan = KgbGajiPokok::select('golongan')->distinct()->pluck('golongan');

        return view('dashboard.kgb.master_gaji.index', compact('gajis', 'daftarGolongan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_pegawai' => 'required|in:pns,pppk',
            'golongan' => 'required|string|max:20',
            'masa_kerja' => 'required|integer|min:0|max:40',
            'nominal' => 'required|numeric|min:0',
            'regulasi' => 'required|string|max:100',
        ]);

        KgbGajiPokok::updateOrCreate(
            [
                'jenis_pegawai' => $validated['jenis_pegawai'],
                'golongan' => $validated['golongan'],
                'masa_kerja' => $validated['masa_kerja'],
                'regulasi' => $validated['regulasi'],
            ],
            [
                'nominal' => $validated['nominal'],
            ]
        );

        return back()->with('success', 'Data acuan gaji pokok berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $gaji = KgbGajiPokok::findOrFail($id);

        $validated = $request->validate([
            'nominal' => 'required|numeric|min:0',
            'regulasi' => 'required|string|max:100',
        ]);

        $gaji->update($validated);

        return back()->with('success', 'Nominal gaji pokok berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $gaji = KgbGajiPokok::findOrFail($id);
        $gaji->delete();

        return back()->with('success', 'Data acuan gaji berhasil dihapus.');
    }
    public function cetakPdf(Request $request)
    {
        $query = KgbGajiPokok::query();

        if ($request->filled('jenis_pegawai')) {
            $query->where('jenis_pegawai', $request->jenis_pegawai);
        }

        if ($request->filled('golongan')) {
            $query->where('golongan', $request->golongan);
        }

        $gajis = $query->orderBy('jenis_pegawai', 'asc')
            ->orderBy('golongan', 'asc')
            ->orderBy('masa_kerja', 'asc')
            ->get();

        $filterJenis = $request->jenis_pegawai ? strtoupper($request->jenis_pegawai) : 'SEMUA (PNS & PPPK)';
        $filterGolongan = $request->golongan ? 'GOLONGAN ' . $request->golongan : 'SEMUA GOLONGAN';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('dashboard.kgb.master_gaji.pdf_acuan', compact('gajis', 'filterJenis', 'filterGolongan'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Tabel_Acuan_Gaji_Pokok_' . now()->format('Ymd_His') . '.pdf');
    }
}