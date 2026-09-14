<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use App\Models\SuratMasuk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SuratMasukController extends Controller
{
    /**
     * Tampilan daftar agenda surat masuk
     */
    public function index(Request $request)
    {
        $tahunSekarang = $request->get('tahun', date('Y'));

        $query = SuratMasuk::with('penerima')
            ->where('tahun', $tahunSekarang)
            ->orderBy('nomor_agenda', 'desc');

        // Filter tanggal terima
        if ($request->filled('tanggal_terima')) {
            $query->whereDate('tanggal_terima', $request->tanggal_terima);
        }

        // Pencarian multi-kolom
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('asal_surat', 'like', "%{$q}%")
                    ->orWhere('nomor_surat_masuk', 'like', "%{$q}%")
                    ->orWhere('perihal', 'like', "%{$q}%")
                    ->orWhere('unit_pengolah', 'like', "%{$q}%");
            });
        }

        $surats = $query->paginate(20)->withQueryString();

        // Rekap ringkas kartu atas
        $totalSemua = SuratMasuk::where('tahun', $tahunSekarang)->count();
        $totalPenting = SuratMasuk::where('tahun', $tahunSekarang)
            ->whereIn('tingkat_surat', ['Penting', 'Segera', 'Penting / Segera', 'Rahasia'])
            ->count();

        return view('dashboard.surat.masuk.index', compact(
            'surats',
            'totalSemua',
            'totalPenting',
            'tahunSekarang'
        ));
    }

    /**
     * Form pencatatan surat masuk baru
     */
    public function create()
    {
        $tahun = date('Y');
        $nextAgenda = (SuratMasuk::where('tahun', $tahun)->max('nomor_agenda') ?? 0) + 1;

        // Ambil riwayat tanda tangan terakhir milik user yang sedang login
        $lastSignature = SuratMasuk::where('diterima_oleh', auth()->id())
            ->whereNotNull('ttd_penerima')
            ->latest()
            ->value('ttd_penerima');

        return view('dashboard.surat.masuk.create', compact('nextAgenda', 'lastSignature'));
    }

    /**
     * Simpan data surat masuk, TTD canvas, dan PDF
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_terima'    => 'required|date',
            'tanggal_surat'     => 'required|date',
            'asal_surat'        => 'required|string|max:255',
            'nomor_surat_masuk' => 'required|string|max:255',
            'tingkat_surat'     => 'required|string',
            'perihal'           => 'required|string',
            'unit_pengolah'     => 'required|string',
            'ttd_penerima'      => 'nullable|string', // Base64 Canvas dataURL
            'file_surat'        => 'nullable|mimes:pdf|max:10240',
        ]);

        return DB::transaction(function () use ($request) {
            $tahun = Carbon::parse($request->tanggal_terima)->year;

            // Lock baris terakhir agar nomor agenda urut tidak tumpang tindih
            $lastNomor = SuratMasuk::where('tahun', $tahun)->lockForUpdate()->max('nomor_agenda') ?? 0;
            $nomorAgenda = $lastNomor + 1;

            // Upload PDF jika dilampirkan
            $filePath = null;
            if ($request->hasFile('file_surat')) {
                $filePath = $request->file('file_surat')->store('surat_masuk', 'public');
            }

            $surat = SuratMasuk::create([
                'tahun'             => $tahun,
                'nomor_agenda'      => $nomorAgenda,
                'tanggal_terima'    => $request->tanggal_terima,
                'tanggal_surat'     => $request->tanggal_surat,
                'asal_surat'        => $request->asal_surat,
                'nomor_surat_masuk' => $request->nomor_surat_masuk,
                'tingkat_surat'     => $request->tingkat_surat,
                'perihal'           => $request->perihal,
                'unit_pengolah'     => $request->unit_pengolah,
                'diterima_oleh'     => auth()->id(),
                'ttd_penerima'      => $request->ttd_penerima,
                'file_surat'        => $filePath,
            ]);

            return redirect()->route('sigap-surat.masuk.index')
                ->with('success', "Surat masuk berhasil dicatat dengan No. Agenda: " . sprintf('%03d', $nomorAgenda));
        });
    }

    /**
     * Detail surat masuk
     */
    public function show($id)
    {
        $surat = SuratMasuk::with('penerima')->findOrFail($id);
        return view('dashboard.surat.masuk.show', compact('surat'));
    }

    /**
     * Form edit surat masuk
     */
    public function edit($id)
    {
        $surat = SuratMasuk::findOrFail($id);
        return view('dashboard.surat.masuk.edit', compact('surat'));
    }

    /**
     * Update data surat masuk
     */
    public function update(Request $request, $id)
    {
        $surat = SuratMasuk::findOrFail($id);

        $request->validate([
            'tanggal_terima'    => 'required|date',
            'tanggal_surat'     => 'required|date',
            'asal_surat'        => 'required|string|max:255',
            'nomor_surat_masuk' => 'required|string|max:255',
            'tingkat_surat'     => 'required|string',
            'perihal'           => 'required|string',
            'unit_pengolah'     => 'required|string',
            'ttd_penerima'      => 'nullable|string',
            'file_surat'        => 'nullable|mimes:pdf|max:10240',
        ]);

        $data = [
            'tanggal_terima'    => $request->tanggal_terima,
            'tanggal_surat'     => $request->tanggal_surat,
            'asal_surat'        => $request->asal_surat,
            'nomor_surat_masuk' => $request->nomor_surat_masuk,
            'tingkat_surat'     => $request->tingkat_surat,
            'perihal'           => $request->perihal,
            'unit_pengolah'     => $request->unit_pengolah,
        ];

        // Update TTD jika ada coretan baru
        if ($request->filled('ttd_penerima')) {
            $data['ttd_penerima'] = $request->ttd_penerima;
        }

        // Ganti file PDF jika ada upload baru
        if ($request->hasFile('file_surat')) {
            if ($surat->file_surat) {
                Storage::disk('public')->delete($surat->file_surat);
            }
            $data['file_surat'] = $request->file('file_surat')->store('surat_masuk', 'public');
        }

        $surat->update($data);

        return redirect()->route('sigap-surat.masuk.index')
            ->with('success', "Data surat masuk Agenda No. " . sprintf('%03d', $surat->nomor_agenda) . " berhasil diperbarui.");
    }

    /**
     * Cetak lembar disposisi ukuran 1/2 HVS A4
     */
    public function cetakDisposisi($id)
    {
        $surat = SuratMasuk::findOrFail($id);
        return view('dashboard.surat.masuk.cetak-disposisi', compact('surat'));
    }

    /**
     * Hapus data surat masuk beserta filenya
     */
    public function destroy($id)
    {
        $surat = SuratMasuk::findOrFail($id);

        if ($surat->file_surat) {
            Storage::disk('public')->delete($surat->file_surat);
        }

        $surat->delete();

        return redirect()->route('sigap-surat.masuk.index')
            ->with('success', 'Data surat masuk berhasil dihapus dari agenda.');
    }
    public function bukuAgenda(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));

        // Ambil seluruh arsip surat masuk pada tahun takwim tersebut
        $surats = SuratMasuk::with('penerima')
            ->where('tahun', $tahun)
            ->orderBy('nomor_agenda', 'asc')
            ->get();

        $totalSemua = $surats->count();

        return view('dashboard.surat.masuk.buku-agenda', compact('surats', 'tahun', 'totalSemua'));
    }
}