<?php

namespace App\Http\Controllers;

use App\Models\KgbRiwayat;
use App\Models\KgbGajiPokok;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class KgbController extends Controller
{
    /**
     * Dashboard & Monitoring Seluruh Pegawai (Admin / Verifikator)
     */
    public function index(Request $request)
    {
        $query = KgbRiwayat::with('user');

        // Jika pegawai biasa (employee) & bukan admin/verifikator, arahkan ke KGB Saya
        if (auth()->user()->hasRole('employee') && !auth()->user()->hasAnyRole(['admin', 'superadmin', 'verif_kgb'])) {
            return redirect()->route('sigap-kgb.saya');
        }

        // Filter Jenis Pegawai
        if ($request->filled('jenis_pegawai')) {
            $query->where('jenis_pegawai', $request->jenis_pegawai);
        }

        // Filter Masa Waktu (Segera jatuh tempo <= 60 hari)
        if ($request->filter === 'segera') {
            $query->where('tmt_baru', '<=', Carbon::now()->addDays(60))
                  ->whereNotIn('status', ['selesai']);
        }

        $riwayats = $query->orderBy('tmt_baru', 'asc')->paginate(10);

        // Statistik Counter
        $allRecords = KgbRiwayat::all();
        $totalPegawai = $allRecords->unique('user_id')->count();
        $jatuhTempo = $allRecords->filter(function ($item) {
            return $item->status !== 'selesai' && $item->sisa_hari <= 60;
        })->count();
        $selesaiTahunIni = $allRecords->where('status', 'selesai')
            ->filter(fn($item) => Carbon::parse($item->tmt_baru)->year === now()->year)
            ->count();

        return view('dashboard.kgb.index', compact('riwayats', 'totalPegawai', 'jatuhTempo', 'selesaiTahunIni'));
    }

    /**
     * Portal Mandiri Pegawai: KGB Saya
     */
    public function saya()
    {
        $user = auth()->user();

        // Ambil riwayat KGB terbaru milik user yang sedang login
        $kgbAktif = KgbRiwayat::where('user_id', $user->id)
            ->orderBy('tmt_baru', 'desc')
            ->first();

        // Ambil semua arsip riwayat KGB sebelumnya
        $riwayats = KgbRiwayat::where('user_id', $user->id)
            ->orderBy('tmt_baru', 'desc')
            ->get();

        return view('dashboard.kgb.saya', compact('user', 'kgbAktif', 'riwayats'));
    }

    /**
     * Form Tambah SK KGB Pegawai
     */
public function create()
    {
        // Ambil user dengan role employee yang aktif
        $users = User::role('employee')
            ->with(['profile', 'kgbTerbaru'])
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        return view('dashboard.kgb.create', compact('users'));
    }

    /**
     * Endpoint API JSON untuk Auto-Fill Cerdas
     */
    public function getPegawaiData($id)
    {
        $user = User::with(['profile', 'kgbTerbaru'])->findOrFail($id);
        $profile = $user->profile;
        $kgbTerakhir = $user->kgbTerbaru;

        // 1. Deteksi Jenis Pegawai (PNS / PPPK)
        $rawStatus = strtoupper($profile->status_pegawai ?? '');
        $jenisPegawai = str_contains($rawStatus, 'PPPK') ? 'pppk' : 'pns';

        // 2. Pangkat / Golongan & Jabatan
        $pangkatGolongan = $profile->golongan_ruang ?: ($profile->golongan ?: '');
        $jabatan = $profile->jabatan ?: ($profile->jabatan_fungsional ?: ($profile->jabatan_teknis ?: ''));

        // 3. Masa Kerja & SK Acuan
        if ($kgbTerakhir) {
            // Jika sudah ada riwayat KGB sebelumnya, jadikan SK baru sebelumnya sebagai acuan SK lama saat ini
            $nomorSkLama = $kgbTerakhir->nomor_sk_lama;
            $tanggalSkLama = $kgbTerakhir->tanggal_sk_lama ? $kgbTerakhir->tanggal_sk_lama->format('Y-m-d') : '';
            $tmtLama = $kgbTerakhir->tmt_baru ? $kgbTerakhir->tmt_baru->format('Y-m-d') : '';
            $mkgTahun = $kgbTerakhir->mkg_tahun_baru;
            $mkgBulan = $kgbTerakhir->mkg_bulan_baru;
            $gajiPokokLama = $kgbTerakhir->gaji_pokok_baru;
        } else {
            // Jika baru pertama kali diinput, tarik estimasi awal dari profil
            $nomorSkLama = '';
            $tanggalSkLama = '';
            $tmtLama = $profile->tmt_golongan ? \Carbon\Carbon::parse($profile->tmt_golongan)->format('Y-m-d') : '';
            $mkgTahun = (int) ($profile->masa_kerja_tahun ?? 0);
            $mkgBulan = (int) ($profile->masa_kerja_bulan ?? 0);
            
            // Estimasi gaji lama dari master acuan
            $gajiPokokLama = KgbGajiPokok::cariNominal($jenisPegawai, $pangkatGolongan, $mkgTahun);
        }

        return response()->json([
            'nama' => $user->name,
            'nip' => $user->nip,
            'unit' => $user->unit,
            'jenis_pegawai' => $jenisPegawai,
            'pangkat_golongan' => $pangkatGolongan,
            'jabatan' => $jabatan,
            'mkg_tahun' => $mkgTahun,
            'mkg_bulan' => $mkgBulan,
            'nomor_sk_lama' => $nomorSkLama,
            'tanggal_sk_lama' => $tanggalSkLama,
            'tmt_lama' => $tmtLama,
            'gaji_pokok_lama' => $gajiPokokLama,
        ]);
    }

    /**
     * Simpan Data SK KGB & Otomatis Hitung +2 Tahun & Lookup Gaji
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'jenis_pegawai' => 'required|in:pns,pppk',
            'pangkat_golongan' => 'required|string|max:50',
            'jabatan' => 'nullable|string|max:191',
            'nomor_sk_lama' => 'required|string|max:191',
            'tanggal_sk_lama' => 'required|date',
            'tmt_lama' => 'required|date',
            'mkg_tahun_lama' => 'required|integer|min:0|max:40',
            'mkg_bulan_lama' => 'required|integer|min:0|max:11',
            'gaji_pokok_lama' => 'required|numeric|min:0',
            'pejabat_penetap' => 'nullable|string|max:191',
            'file_sk_lama' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072',
        ]);

        // 1. Kalkulasi Otomatis TMT Baru & MKG Baru (+2 Tahun)
        $tmtLama = Carbon::parse($validated['tmt_lama']);
        $tmtBaru = $tmtLama->copy()->addYears(2);
        $mkgTahunBaru = $validated['mkg_tahun_lama'] + 2;
        $mkgBulanBaru = $validated['mkg_bulan_lama'];

        // 2. Lookup Nominal Gaji Pokok Baru di Master Acuan
        $gajiPokokBaru = KgbGajiPokok::cariNominal(
            $validated['jenis_pegawai'],
            $validated['pangkat_golongan'],
            $mkgTahunBaru
        );

        if ($gajiPokokBaru <= 0) {
            $gajiPokokBaru = $validated['gaji_pokok_lama'];
        }

        // Upload berkas jika ada
        $filePath = null;
        if ($request->hasFile('file_sk_lama')) {
            $filePath = $request->file('file_sk_lama')->store('kgb/sk_lama', 'public');
        }

        KgbRiwayat::create([
            'user_id' => $validated['user_id'],
            'jenis_pegawai' => $validated['jenis_pegawai'],
            'pangkat_golongan' => $validated['pangkat_golongan'],
            'jabatan' => $validated['jabatan'],
            'nomor_sk_lama' => $validated['nomor_sk_lama'],
            'tanggal_sk_lama' => $validated['tanggal_sk_lama'],
            'tmt_lama' => $validated['tmt_lama'],
            'mkg_tahun_lama' => $validated['mkg_tahun_lama'],
            'mkg_bulan_lama' => $validated['mkg_bulan_lama'],
            'gaji_pokok_lama' => $validated['gaji_pokok_lama'],
            'pejabat_penetap' => $validated['pejabat_penetap'],
            'tmt_baru' => $tmtBaru->format('Y-m-d'),
            'mkg_tahun_baru' => $mkgTahunBaru,
            'mkg_bulan_baru' => $mkgBulanBaru,
            'gaji_pokok_baru' => $gajiPokokBaru,
            'status' => 'menunggu',
            'file_sk_lama' => $filePath,
        ]);

        return redirect()->route('sigap-kgb.index')->with('success', 'Data SK KGB berhasil disimpan. Jadwal KGB berikutnya otomatis terhitung.');
    }

    /**
     * Detail Riwayat KGB Pegawai
     */
    public function show($id)
    {
        $riwayat = KgbRiwayat::with('user')->findOrFail($id);
        return view('dashboard.kgb.show', compact('riwayat'));
    }

    /**
     * Form Edit Riwayat KGB
     */
    public function edit($id)
    {
        $riwayat = KgbRiwayat::with('user')->findOrFail($id);
        $users = User::role('employee')->orderBy('name', 'asc')->get();
        return view('dashboard.kgb.edit', compact('riwayat', 'users'));
    }

    /**
     * Update Data Riwayat KGB
     */
    public function update(Request $request, $id)
    {
        $riwayat = KgbRiwayat::findOrFail($id);

        $validated = $request->validate([
            'pangkat_golongan' => 'required|string|max:50',
            'jabatan' => 'nullable|string|max:191',
            'nomor_sk_lama' => 'required|string|max:191',
            'tanggal_sk_lama' => 'required|date',
            'tmt_lama' => 'required|date',
            'mkg_tahun_lama' => 'required|integer|min:0|max:40',
            'mkg_bulan_lama' => 'required|integer|min:0|max:11',
            'gaji_pokok_lama' => 'required|numeric|min:0',
            'pejabat_penetap' => 'nullable|string|max:191',
            'file_sk_lama' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072',
        ]);

        $tmtLama = Carbon::parse($validated['tmt_lama']);
        $tmtBaru = $tmtLama->copy()->addYears(2);
        $mkgTahunBaru = $validated['mkg_tahun_lama'] + 2;

        $gajiPokokBaru = KgbGajiPokok::cariNominal(
            $riwayat->jenis_pegawai,
            $validated['pangkat_golongan'],
            $mkgTahunBaru
        );

        if ($gajiPokokBaru <= 0) {
            $gajiPokokBaru = $validated['gaji_pokok_lama'];
        }

        if ($request->hasFile('file_sk_lama')) {
            if ($riwayat->file_sk_lama) {
                Storage::disk('public')->delete($riwayat->file_sk_lama);
            }
            $riwayat->file_sk_lama = $request->file('file_sk_lama')->store('kgb/sk_lama', 'public');
        }

        $riwayat->update(array_merge($validated, [
            'tmt_baru' => $tmtBaru->format('Y-m-d'),
            'mkg_tahun_baru' => $mkgTahunBaru,
            'mkg_bulan_baru' => $validated['mkg_bulan_lama'],
            'gaji_pokok_baru' => $gajiPokokBaru,
        ]));

        return redirect()->route('sigap-kgb.index')->with('success', 'Data KGB berhasil diperbarui.');
    }

    /**
     * Update Status Pengusulan
     */
    public function updateStatus(Request $request, $id)
    {
        $riwayat = KgbRiwayat::findOrFail($id);
        $riwayat->update(['status' => $request->status]);

        return back()->with('success', 'Status KGB pegawai berhasil diperbarui.');
    }

    /**
     * Export Dokumen Pengajuan KGB ke Format PDF
     */
    public function exportPdf($id)
    {
        $riwayat = KgbRiwayat::with('user')->findOrFail($id);

        $pdf = Pdf::loadView('dashboard.kgb.pdf_usulan', compact('riwayat'))
            ->setPaper('a4', 'portrait');

        $fileName = 'Usulan_KGB_' . str_replace(' ', '_', $riwayat->user->name) . '.pdf';
        return $pdf->stream($fileName);
    }

    /**
     * Hapus Data Riwayat KGB
     */
    public function destroy($id)
    {
        $riwayat = KgbRiwayat::findOrFail($id);
        if ($riwayat->file_sk_lama) {
            Storage::disk('public')->delete($riwayat->file_sk_lama);
        }
        $riwayat->delete();

        return redirect()->route('sigap-kgb.index')->with('success', 'Data riwayat KGB berhasil dihapus.');
    }
}