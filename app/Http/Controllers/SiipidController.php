<?php

namespace App\Http\Controllers;

use App\Models\ImaInovasi;
use App\Models\Inovasi;
use App\Models\SiipidPrestasi;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SiipidController extends Controller
{
    /**
     * Dashboard Peserta / Inovator SIIPID
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Ambil usulan milik user yang login
        $prestasis = SiipidPrestasi::with(['innovable', 'reviewer'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        // Statistik Inovator
        $totalUsulan = SiipidPrestasi::where('user_id', $user->id)->count();
        $menungguReview = SiipidPrestasi::where('user_id', $user->id)->whereIn('status', ['diajukan', 'dalam_review'])->count();
        $perluPerbaikan = SiipidPrestasi::where('user_id', $user->id)->where('status', 'dikembalikan_perbaikan')->count();
        $disetujui = SiipidPrestasi::where('user_id', $user->id)->whereIn('status', ['direkomendasikan', 'ditetapkan_sk'])->count();

        // Cek apakah punya inovasi terdaftar
        $hasInovasi = Inovasi::where('user_id', $user->id)->exists() 
            || ImaInovasi::where('user_id', $user->id)->exists();

        return view('dashboard.siipid.index', compact(
            'prestasis',
            'totalUsulan',
            'menungguReview',
            'perluPerbaikan',
            'disetujui',
            'hasInovasi'
        ));
    }

    /**
     * Form Pendaftaran Prestasi Inovator (Dengan Eligibility Gate)
     */
    public function create()
    {
        $user = Auth::user();

        // Ambil inovasi dari SIGAP Inovasi (OPD) & SIGAP IMA (Masyarakat)
        $daftarInovasiOpd = Inovasi::where('user_id', $user->id)
            ->select('id', 'judul', 'opd_unit', 'tahap_inovasi')
            ->get();

        $daftarInovasiIma = ImaInovasi::where('user_id', $user->id)
            ->select('id', 'judul', 'opd_unit', 'tahap_inovasi')
            ->get();

        // ELIGIBILITY GATE: Jika belum pernah menginput inovasi di kedua modul, cegat dengan notice edukatif
        if ($daftarInovasiOpd->isEmpty() && $daftarInovasiIma->isEmpty()) {
            return view('dashboard.siipid.gate_notice');
        }

        return view('dashboard.siipid.create', compact('daftarInovasiOpd', 'daftarInovasiIma', 'user'));
    }

    /**
     * Simpan Usulan Prestasi Baru
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'innovable_selection' => 'required|string', // Format: "Inovasi:12" atau "ImaInovasi:5"
            'kategori_sasaran' => 'required|in:asn,perangkat_daerah,masyarakat,dprd,kelompok',
            'nama_inovator' => 'required|string|max:255',
            'nik' => 'nullable|string|max:30',
            'nip' => 'nullable|string|max:30',
            'jabatan' => 'nullable|string|max:255',
            'opd_instansi' => 'nullable|string|max:255',
            'no_wa' => 'required|string|max:25',
            'email' => 'required|email|max:100',
            'jenis_kepesertaan' => 'required|in:perorangan,kelompok',
            'anggota_tim' => 'nullable|array',
            'nama_prestasi' => 'required|string|max:255',
            'ajang_kompetisi' => 'required|string|max:255',
            'tahun_perolehan' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'tingkat_prestasi' => 'required|in:kota,provinsi,nasional,internasional,khusus',
            'kategori_khusus' => 'nullable|string|max:255',
            'peringkat_capaian' => 'nullable|string|max:100',
            'lembaga_pemberi' => 'required|string|max:255',
            'deskripsi_prestasi' => 'nullable|string',
            'bukti_prestasi_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5MB
            'bukti_penerapan_file' => 'nullable|file|mimes:pdf|max:10240',
            'surat_keaslian_file' => 'nullable|file|mimes:pdf|max:5120',
            'surat_kontribusi_tim_file' => 'nullable|file|mimes:pdf|max:5120',
            'usulan_bentuk_penghargaan' => 'nullable|string|max:255',
            'usulan_bentuk_insentif' => 'nullable|string|max:255',
            'action_type' => 'required|in:draft,submit',
        ]);

        // Parse innovable polymorphic
        [$type, $id] = explode(':', $request->innovable_selection);
        $modelClass = $type === 'Inovasi' ? Inovasi::class : ImaInovasi::class;

        // Pastikan inovasi ini benar-benar milik user (Security check)
        $targetInovasi = $modelClass::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        // Upload berkas
        $buktiPrestasiPath = $request->file('bukti_prestasi_file')->store('siipid/bukti_prestasi', 'public');
        $buktiPenerapanPath = $request->hasFile('bukti_penerapan_file') 
            ? $request->file('bukti_penerapan_file')->store('siipid/bukti_penerapan', 'public') : null;
        $suratKeaslianPath = $request->hasFile('surat_keaslian_file') 
            ? $request->file('surat_keaslian_file')->store('siipid/surat_keaslian', 'public') : null;
        $suratKontribusiPath = $request->hasFile('surat_kontribusi_tim_file') 
            ? $request->file('surat_kontribusi_tim_file')->store('siipid/surat_tim', 'public') : null;

        $status = $request->action_type === 'submit' ? 'diajukan' : 'draft';

        $prestasi = SiipidPrestasi::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'innovable_type' => $modelClass,
            'innovable_id' => $targetInovasi->id,
            'kategori_sasaran' => $request->kategori_sasaran,
            'nama_inovator' => $request->nama_inovator,
            'nik' => $request->nik,
            'nip' => $request->nip,
            'jabatan' => $request->jabatan,
            'opd_instansi' => $request->opd_instansi,
            'no_wa' => $request->no_wa,
            'email' => $request->email,
            'jenis_kepesertaan' => $request->jenis_kepesertaan,
            'anggota_tim' => $request->anggota_tim,
            'nama_prestasi' => $request->nama_prestasi,
            'ajang_kompetisi' => $request->ajang_kompetisi,
            'tahun_perolehan' => $request->tahun_perolehan,
            'tingkat_prestasi' => $request->tingkat_prestasi,
            'kategori_khusus' => $request->kategori_khusus,
            'peringkat_capaian' => $request->peringkat_capaian,
            'lembaga_pemberi' => $request->lembaga_pemberi,
            'deskripsi_prestasi' => $request->deskripsi_prestasi,
            'bukti_prestasi_file' => $buktiPrestasiPath,
            'bukti_penerapan_file' => $buktiPenerapanPath,
            'surat_keaslian_file' => $suratKeaslianPath,
            'surat_kontribusi_tim_file' => $suratKontribusiPath,
            'usulan_bentuk_penghargaan' => $request->usulan_bentuk_penghargaan,
            'usulan_bentuk_insentif' => $request->usulan_bentuk_insentif,
            'status' => $status,
        ]);

        ActivityLogger::log('create', 'SIGAP SIIPID', "Mendaftarkan usulan prestasi: {$prestasi->nama_prestasi}");

        $pesan = $status === 'diajukan' 
            ? 'Usulan prestasi berhasil diajukan dan sedang menunggu review tim verifikator.' 
            : 'Data prestasi berhasil disimpan sebagai draft.';

        return redirect()->route('sigap-siipid.index')->with('success', $pesan);
    }

    /**
     * Detail Usulan Prestasi Inovator
     */
    public function show($uuid)
    {
        $user = Auth::user();
        $prestasi = SiipidPrestasi::with(['innovable', 'reviewer', 'reviewLogs.reviewer'])
            ->where('uuid', $uuid)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return view('dashboard.siipid.show', compact('prestasi'));
    }

    /**
     * Form Edit (Aktif jika status Draft atau Dikembalikan Perbaikan)
     */
    public function edit($uuid)
    {
        $user = Auth::user();
        $prestasi = SiipidPrestasi::with(['innovable'])
            ->where('uuid', $uuid)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if (!$prestasi->is_editable) {
            return redirect()->route('sigap-siipid.show', $uuid)
                ->with('error', 'Usulan ini sedang dalam proses review dan tidak dapat diedit.');
        }

        $daftarInovasiOpd = Inovasi::where('user_id', $user->id)->get();
        $daftarInovasiIma = ImaInovasi::where('user_id', $user->id)->get();

        return view('dashboard.siipid.edit', compact('prestasi', 'daftarInovasiOpd', 'daftarInovasiIma'));
    }

    /**
     * Simpan Perubahan Usulan (Update & Resubmit)
     */
    public function update(Request $request, $uuid)
    {
        $user = Auth::user();
        $prestasi = SiipidPrestasi::where('uuid', $uuid)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if (!$prestasi->is_editable) {
            return redirect()->route('sigap-siipid.show', $uuid)
                ->with('error', 'Usulan ini tidak dapat diubah.');
        }

        $request->validate([
            'kategori_sasaran' => 'required|in:asn,perangkat_daerah,masyarakat,dprd,kelompok',
            'nama_inovator' => 'required|string|max:255',
            'nik' => 'nullable|string|max:30',
            'nip' => 'nullable|string|max:30',
            'jabatan' => 'nullable|string|max:255',
            'opd_instansi' => 'nullable|string|max:255',
            'no_wa' => 'required|string|max:25',
            'email' => 'required|email|max:100',
            'jenis_kepesertaan' => 'required|in:perorangan,kelompok',
            'anggota_tim' => 'nullable|array',
            'nama_prestasi' => 'required|string|max:255',
            'ajang_kompetisi' => 'required|string|max:255',
            'tahun_perolehan' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'tingkat_prestasi' => 'required|in:kota,provinsi,nasional,internasional,khusus',
            'kategori_khusus' => 'nullable|string|max:255',
            'peringkat_capaian' => 'nullable|string|max:100',
            'lembaga_pemberi' => 'required|string|max:255',
            'deskripsi_prestasi' => 'nullable|string',
            'bukti_prestasi_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'bukti_penerapan_file' => 'nullable|file|mimes:pdf|max:10240',
            'surat_keaslian_file' => 'nullable|file|mimes:pdf|max:5120',
            'surat_kontribusi_tim_file' => 'nullable|file|mimes:pdf|max:5120',
            'usulan_bentuk_penghargaan' => 'nullable|string|max:255',
            'usulan_bentuk_insentif' => 'nullable|string|max:255',
            'action_type' => 'required|in:draft,submit',
        ]);

        // Upload berkas baru jika ada penggantian
        if ($request->hasFile('bukti_prestasi_file')) {
            if ($prestasi->bukti_prestasi_file) Storage::disk('public')->delete($prestasi->bukti_prestasi_file);
            $prestasi->bukti_prestasi_file = $request->file('bukti_prestasi_file')->store('siipid/bukti_prestasi', 'public');
        }

        if ($request->hasFile('bukti_penerapan_file')) {
            if ($prestasi->bukti_penerapan_file) Storage::disk('public')->delete($prestasi->bukti_penerapan_file);
            $prestasi->bukti_penerapan_file = $request->file('bukti_penerapan_file')->store('siipid/bukti_penerapan', 'public');
        }

        if ($request->hasFile('surat_keaslian_file')) {
            if ($prestasi->surat_keaslian_file) Storage::disk('public')->delete($prestasi->surat_keaslian_file);
            $prestasi->surat_keaslian_file = $request->file('surat_keaslian_file')->store('siipid/surat_keaslian', 'public');
        }

        if ($request->hasFile('surat_kontribusi_tim_file')) {
            if ($prestasi->surat_kontribusi_tim_file) Storage::disk('public')->delete($prestasi->surat_kontribusi_tim_file);
            $prestasi->surat_kontribusi_tim_file = $request->file('surat_kontribusi_tim_file')->store('siipid/surat_tim', 'public');
        }

        // Update data text
        $prestasi->kategori_sasaran = $request->kategori_sasaran;
        $prestasi->nama_inovator = $request->nama_inovator;
        $prestasi->nik = $request->nik;
        $prestasi->nip = $request->nip;
        $prestasi->jabatan = $request->jabatan;
        $prestasi->opd_instansi = $request->opd_instansi;
        $prestasi->no_wa = $request->no_wa;
        $prestasi->email = $request->email;
        $prestasi->jenis_kepesertaan = $request->jenis_kepesertaan;
        $prestasi->anggota_tim = $request->anggota_tim;
        $prestasi->nama_prestasi = $request->nama_prestasi;
        $prestasi->ajang_kompetisi = $request->ajang_kompetisi;
        $prestasi->tahun_perolehan = $request->tahun_perolehan;
        $prestasi->tingkat_prestasi = $request->tingkat_prestasi;
        $prestasi->kategori_khusus = $request->kategori_khusus;
        $prestasi->peringkat_capaian = $request->peringkat_capaian;
        $prestasi->lembaga_pemberi = $request->lembaga_pemberi;
        $prestasi->deskripsi_prestasi = $request->deskripsi_prestasi;
        $prestasi->usulan_bentuk_penghargaan = $request->usulan_bentuk_penghargaan;
        $prestasi->usulan_bentuk_insentif = $request->usulan_bentuk_insentif;

        // Jika disubmit ulang, ubah status kembali menjadi 'diajukan'
        if ($request->action_type === 'submit') {
            $prestasi->status = 'diajukan';
        }

        $prestasi->save();

        ActivityLogger::log('update', 'SIGAP SIIPID', "Memperbarui usulan prestasi: {$prestasi->nama_prestasi}");

        return redirect()->route('sigap-siipid.show', $prestasi->uuid)
            ->with('success', 'Perubahan usulan prestasi berhasil disimpan.');
    }

    /**
     * Hapus Usulan Prestasi (Hanya saat status draft)
     */
    public function destroy($uuid)
    {
        $user = Auth::user();
        $prestasi = SiipidPrestasi::where('uuid', $uuid)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($prestasi->status !== 'draft') {
            return back()->with('error', 'Hanya usulan berstatus draft yang dapat dihapus.');
        }

        $prestasi->delete();
        ActivityLogger::log('delete', 'SIGAP SIIPID', "Menghapus draft prestasi: {$prestasi->nama_prestasi}");

        return redirect()->route('sigap-siipid.index')->with('success', 'Draft prestasi berhasil dihapus.');
    }
}
