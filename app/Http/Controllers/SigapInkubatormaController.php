<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Inkubatorma;
use Illuminate\Http\Request;
use App\Models\InkubatormaLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class SigapInkubatormaController extends Controller
{
    /**
     * LIST LAYANAN STATIS (tidak pakai database)
     * value = yang disimpan di DB (string)
     * label = yang ditampilkan di UI
     */
    private function layananOptions(): array
    {
        return [
            'penjaringan_dan_kurasi_ide'      => 'Penjaringan dan Kurasi ide Inovasi',
            'profil_inovasi'                 => 'Asistensi Penyusunan Profil Inovasi',
            'indikator_daerah'               => 'Pengisian Satuan Indikator Inovasi Daerah',
            'implementasi_dan_pilot_project' => 'Pendampingan Implementasi dan Pilot Project',
            'video_inovasi'                  => 'Pembuatan Video Inovasi',
            'kapasitas_sdm'                  => 'Penguatan Kapasitas SDM dan Inovator Riset',
            'konsultasi_tata_kelola'         => 'Konsultasi Inovasi dan Tata Kelola',
            'pembuatan_haki'                 => 'Pembuatan Hak Kekayaan Intelektual (HAKI) Inovasi',
            'hasil_inovasi_riset'            => 'Pengajuan Hasil Inovasi dalam Pelaksanaan Riset',
            'lainnya'                        => 'Lainnya',
        ];
    }

    /**
     * Landing page / index
     */
    public function index()
    {
        $layananOptions = $this->layananOptions();

        // LIST JADWAL (SEMUA STATUS)
        $jadwals = Inkubatorma::query()
        ->orderByRaw("
            CASE status
                WHEN 'Terjadwal' THEN 1
                WHEN 'Dijadwalkan Ulang' THEN 2
                WHEN 'Menunggu' THEN 3
                WHEN 'Akan Dijadwalkan' THEN 4
                WHEN 'Selesai' THEN 5
                WHEN 'Ditolak' THEN 6
                ELSE 7
            END
        ")
        ->orderByRaw("
            CASE 
                WHEN status IN ('Terjadwal','Dijadwalkan Ulang') 
                    THEN tanggal_final 
                ELSE tanggal_usulan 
            END
        ")
        ->orderByRaw("
            CASE 
                WHEN status IN ('Terjadwal','Dijadwalkan Ulang') 
                    THEN jam_final 
                ELSE jam_usulan 
            END
        ")
        ->get();

        // KHUSUS KALENDER
        $calendarJadwals = Inkubatorma::query()
            ->whereIn('status', [
                'Menunggu',
                'Akan Dijadwalkan',
                'Terjadwal',
                'Dijadwalkan Ulang'
            ])
            ->orderByRaw("
                CASE 
                    WHEN status IN ('Terjadwal','Dijadwalkan Ulang') 
                        THEN tanggal_final 
                    ELSE tanggal_usulan 
                END
            ")
            ->orderByRaw(" 
                CASE 
                    WHEN status IN ('Terjadwal','Dijadwalkan Ulang') 
                        THEN jam_final 
                    ELSE jam_usulan 
                END 
            ")
            ->get();

        // AMBIL PEGAWAI
        $employees = User::role('employee')
            ->where('status','active')
            ->select('id','name')
            ->orderBy('name')
            ->get();

        $formData = session('inkubatorma_form');

        return view('SigapInkubatorma.home.index', compact('layananOptions', 'jadwals', 'calendarJadwals', 'employees', 'formData'));
    }

    /**
     * Dashboard
     */
    public function dashboard(Request $request)
    {
        $inkubatormas = Inkubatorma::query()
            ->orderBy('created_at', 'desc')
            ->get();

        // dashboard blade butuh ini untuk label layanan (statis)
        $layananOptions = $this->layananOptions();

        $user = Auth::user();

        // ambil per page dari query (?per_page=10/25/50)
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }

        $q = Inkubatorma::query()->orderBy('created_at', 'desc');

        // ===== ROLE FILTER (Spatie) =====
        if ($user && $user->hasRole('admin') || $user->hasRole('verifikator_inkubatorma')) {
            // Admin dan Verifikator lihat semua
        }
        else {
            // User hanya lihat pengajuan yang dia buat (pakai kolom created_by yang ada di tabel)
            $q->where('created_by', $user->id);
        }

        // paginate + keep query string
        $inkubatormas = $q->paginate($perPage)->withQueryString();

        return view('dashboard.inkubatorma.dashboard', compact('inkubatormas', 'layananOptions'));
    }

    /**
     * Store (pengajuan baru dari landing/index)
     */
    public function store(Request $request)
    {
        $allowed = implode(',', array_keys($this->layananOptions()));

        $validated = $request->validate([
            'nama_pengaju' => ['required', 'string', 'max:255'],
            'no_hp'        => ['required', 'string', 'max:20'],
            'nama_opd'     => ['required', 'string', 'max:255'],

            'layanan' => ['required', 'array', 'max:2'],
            'layanan_lainnya' => ['nullable','string','max:255', 'required_if:layanan,lainnya'],


            'judul_konsultasi' => ['required', 'string', 'max:255'],
            'keluhan'          => ['required', 'string'],
            'poin_asistensi'   => ['required', 'string'],
            'tanggal'          => ['required', 'date'],
            'jam'              => ['required'], 
            'mode'             => ['required', 'in:online,offline'],

            'pegawai_id'       => ['nullable', 'exists:users,id'],

        ]);

        $layananLainnya = in_array('lainnya', $validated['layanan'])
        ? ($validated['layanan_lainnya'] ?? null)
        : null;

        // ambil nama target personil dari users (kalau dipilih)
        $targetPersonilNama = null;
        if (!empty($validated['pegawai_id'])) {
            $targetPersonilNama = User::where('id', $validated['pegawai_id'])->value('name');
        }

        // Kode unik (AMAN dari duplikat)
        $prefix = 'INK-' . now()->format('Ymd') . '-';

        // ambil kode terakhir untuk hari ini, lalu naikkan 1
        $lastKode = Inkubatorma::where('kode', 'like', $prefix . '%')
            ->orderBy('kode', 'desc')
            ->value('kode');

        $lastNumber = 0;
        if ($lastKode) {
            // ambil 4 digit terakhir
            $lastNumber = (int) substr($lastKode, -4);
        }

        $newNumber = $lastNumber + 1;
        $kode = $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);

        Inkubatorma::create([
            'kode' => $kode,

            // simpan string layanan ke layanan_id
            'layanan_id' => $validated['layanan'],
            'layanan_lainnya' => $layananLainnya,

            'judul_konsultasi' => $validated['judul_konsultasi'] ?? null,
            'nama_pengaju'     => $validated['nama_pengaju'],
            'hp_pengaju'       => $validated['no_hp'],
            'opd_unit'         => $validated['nama_opd'],
            'keluhan'          => $validated['keluhan'],
            'poin_asistensi'   => $validated['poin_asistensi'],
            'tanggal_usulan'   => $validated['tanggal'],
            'jam_usulan'       => $validated['jam'],
            'metode_usulan'    => $validated['mode'],
            'status'           => 'Menunggu',

            // ✅ agar USER bisa lihat list miliknya
            'created_by'       => Auth::id(),

            // ✅ agar VERIFIKATOR bisa lihat list yang ditujukan kepadanya
            // (pakai pegawai_id dari form index)
            'pic_employee_id'  => $validated['pegawai_id'] ?? null,

            'target_personil_usulan' => $targetPersonilNama,
        ]);

        if (!auth()->check()) {
            session()->put('inkubatorma_form', $validated);
            // supaya kembali ke halaman inkubatorma
            redirect()->setIntendedUrl(route('sigap-inkubatorma.index') . '#form');
            return redirect()->route('login');
        }

        session()->forget('inkubatorma_form');

        return redirect()
            ->route('sigap-inkubatorma.dashboard')
            ->with('success', 'Pengajuan berhasil dikirim');
    }

    /**
     * Detail
     */
    public function detail($id)
    {
        $inkubatorma = Inkubatorma::with([
            'picUser',
            'verifikatorUser',
            'logs',
        ])->findOrFail($id);

        $status = $inkubatorma->status ?? 'Menunggu';
        $layananOptions = $this->layananOptions();

        // ✅ TAMBAHAN: ambil semua verifikator inkubatorma (aktif)
        $verifikators = User::role('verifikator_inkubatorma')
        ->where('status', 'active')
        ->select('id', 'name', 'nomor_hp', 'profile_photo_path')
        ->orderBy('name')
        ->get();

        return view('dashboard.inkubatorma.detail', compact('inkubatorma', 'status', 'layananOptions', 'verifikators'));
    }

    private function canEditByStatus(?string $status): bool
    {
        $status = $status ?? 'Menunggu';
        return in_array($status, ['Menunggu', 'Akan Dijadwalkan'], true);
    }

    /**
     * Edit
     */
    public function edit($id)
    {
        $inkubatorma = Inkubatorma::findOrFail($id);

        if (!$this->canEditByStatus($inkubatorma->status)) {
            return redirect()
                ->route('sigap-inkubatorma.detail', $inkubatorma->id)
                ->with('error', 'Pengajuan tidak dapat diedit pada status saat ini.');
        }

        $layananOptions = $this->layananOptions();

        // AMBIL PEGAWAI
        $employees = User::role('employee')
            ->where('status','active')
            ->select('id','name')
            ->orderBy('name')
            ->get();

        return view('dashboard.inkubatorma.edit', compact('inkubatorma', 'layananOptions', 'employees'));
    }

    /**
     * Update dari halaman edit.blade.php
     */
    public function update(Request $request, $id)
    {
        $inkubatorma = Inkubatorma::findOrFail($id);

        if (!$this->canEditByStatus($inkubatorma->status)) {
            return redirect()
                ->route('sigap-inkubatorma.detail', $inkubatorma->id)
                ->with('error', 'Pengajuan tidak dapat diedit pada status saat ini.');
        }

        $allowed = implode(',', array_keys($this->layananOptions()));

        $validated = $request->validate([
            'layanan_id' => ['required', "in:$allowed"],
            'layanan_lainnya' => ['nullable','string','max:255', 'required_if:layanan_id,lainnya'],
            'judul_konsultasi' => ['required', 'string', 'max:255'],

            'nama_pengaju' => ['required', 'string', 'max:255'],
            'hp_pengaju'   => ['nullable', 'string', 'max:20'],
            'opd_unit'     => ['required', 'string', 'max:255'],

            'keluhan'        => ['nullable', 'string'],
            'poin_asistensi' => ['nullable', 'string'],

            'tanggal_usulan' => ['nullable', 'date'],
            'jam_usulan'     => ['nullable', 'date_format:H:i'],
            'metode_usulan'  => ['nullable', 'in:online,offline'],
            'target_personil_usulan' => ['nullable', 'string', 'max:255'],
        ]);

        $inkubatorma->layanan_id = $validated['layanan_id'];
        $inkubatorma->layanan_lainnya = ($validated['layanan_id'] === 'lainnya')
            ? ($validated['layanan_lainnya'] ?? null)
            : null;

        $inkubatorma->save();

        $inkubatorma->update($validated);

        return redirect()
            ->route('sigap-inkubatorma.detail', $inkubatorma->id)
            ->with('success', 'Data pengajuan berhasil diperbarui.');
    }

    /**
     * Halaman verifikasi
     */
    public function verifikasi($id)
    {
        $inkubatorma = Inkubatorma::with([
            'picUser',
            'verifikatorUser',
            'logs',
        ])->findOrFail($id);

        // untuk dropdown/search PIC: user employee aktif
        $employees = User::role('employee')
            ->where('status', 'active')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $status = $inkubatorma->status ?? 'Menunggu';
        $layananOptions = $this->layananOptions();

        return view('dashboard.inkubatorma.verifikasi', compact('inkubatorma', 'employees', 'status', 'layananOptions'));
    }

    /**
     * Simpan aksi verifikasi
     */
    public function verifikasiUpdate(Request $request, $id)
    {
        $inkubatorma = Inkubatorma::findOrFail($id);

        // ✅ NEW: kalau sudah ditutup (Selesai), tidak boleh verifikasi lagi
        if (($inkubatorma->status ?? '') === 'Selesai') {
            return redirect()
                ->route('sigap-inkubatorma.detail', $inkubatorma->id)
                ->with('error', 'Pengajuan sudah ditutup. Verifikasi tidak bisa diubah lagi.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:Menunggu,Akan Dijadwalkan,Terjadwal,Dijadwalkan Ulang,Ditolak,Selesai'],

            // ✅ pindah ke users
            'pic_employee_id' => ['nullable', 'exists:users,id'],

            'tanggal_final'   => ['nullable', 'date'],
            'jam_final'       => ['nullable', 'date_format:H:i'],
            'metode_final'    => ['nullable', 'in:online,offline'],
            'lokasi_link_final' => ['nullable', 'string', 'max:255'],

            'catatan_verifikator' => ['nullable', 'string'],
        ]);

        $statusLama = $inkubatorma->status ?? 'Menunggu';
        $statusBaru = $validated['status'];

        // Konfirmasi wajib jika menutup (Selesai)
        if ($statusBaru === 'Selesai') {
            $confirm = strtoupper(trim((string) $request->input('close_confirm', '')));
            if ($confirm !== 'TUTUP') {
                return back()
                    ->withErrors(['status' => 'Konfirmasi penutupan gagal. Ketik TUTUP untuk menutup konsultasi.'])
                    ->withInput();
            }
        }

        $butuhJadwal = in_array($statusBaru, ['Terjadwal', 'Dijadwalkan Ulang', 'Selesai'], true);
        if ($butuhJadwal) {
            if (empty($validated['tanggal_final']) || empty($validated['jam_final']) || empty($validated['metode_final'])) {
                return back()
                    ->withErrors(['tanggal_final' => 'Status ini membutuhkan Tanggal/Jam/Metode final.'])
                    ->withInput();
            }
        }

        $inkubatorma->status = $statusBaru;

        $inkubatorma->pic_employee_id   = $validated['pic_employee_id'] ?? null;
        $inkubatorma->tanggal_final     = $validated['tanggal_final'] ?? null;
        $inkubatorma->jam_final         = $validated['jam_final'] ?? null;
        $inkubatorma->metode_final      = $validated['metode_final'] ?? null;
        $inkubatorma->lokasi_link_final = $validated['lokasi_link_final'] ?? null;

        $inkubatorma->catatan_verifikator = $validated['catatan_verifikator'] ?? null;

        // ✅ set verifikator dari user login (kalau kamu pakai auth)
        // kalau belum pakai auth, biarkan null
        $inkubatorma->verifikator_employee_id = $validated['pic_employee_id'] ?? null;
        $inkubatorma->verifikasi_at = now();

        $inkubatorma->save();

        $aksi = $this->mapAksiDariStatus($statusLama, $statusBaru);

        InkubatormaLog::create([
            'inkubatorma_id' => $inkubatorma->id,
            'aksi'           => $aksi,
            'status_dari'    => $statusLama,
            'status_ke'      => $statusBaru,
            'catatan'        => $inkubatorma->catatan_verifikator,
            'created_by'     => null,
            'created_at'     => now(),
        ]);

        // Kalau ditutup -> redirect ke detail
        if ($statusBaru === 'Selesai') {
            return redirect()
                ->route('sigap-inkubatorma.detail', $inkubatorma->id)
                ->with('success', 'Konsultasi ditutup (Selesai).');
        }

        return redirect()
            ->route('sigap-inkubatorma.verifikasi', $inkubatorma->id)
            ->with('success', 'Verifikasi berhasil disimpan.');
    }

    private function mapAksiDariStatus(string $dari, string $ke): string
    {
        if ($ke === 'Akan Dijadwalkan') return 'APPROVE';
        if ($ke === 'Terjadwal') return 'SET_SCHEDULE';
        if ($ke === 'Dijadwalkan Ulang') return 'RESCHEDULE';
        if ($ke === 'Ditolak') return 'REJECT';
        if ($ke === 'Selesai') return 'CLOSE';

        return 'APPROVE';
    }

    public function destroy($id)
    {
        Inkubatorma::findOrFail($id)->delete();

        return redirect()
            ->route('sigap-inkubatorma.dashboard')
            ->with('success', 'Data berhasil dihapus');
    }
}