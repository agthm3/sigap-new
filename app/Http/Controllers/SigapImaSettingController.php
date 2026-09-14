<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImaDropdown;
use App\Models\ImaIndicator;
use App\Models\ImaSchedule;
use App\Models\ImaSetting;
use Illuminate\Support\Facades\Storage;

class SigapImaSettingController extends Controller
{
    public function index()
    {
        $dropdowns = ImaDropdown::orderBy('kategori')->orderBy('id')->get()->groupBy('kategori');
        
        if (ImaIndicator::count() === 0) {
            for ($i = 1; $i <= 20; $i++) {
                ImaIndicator::create(['no_urut' => $i, 'nama_indikator' => 'Indikator ' . $i]);
            }
        }
        
        $indicators = ImaIndicator::orderBy('no_urut')->get();
        $schedules  = ImaSchedule::orderBy('urutan')->orderBy('tanggal_mulai')->get();
        $isLocked   = ImaSetting::get('is_submission_locked', '0') === '1';
        $lockNotice = ImaSetting::get('lock_notice_message', 'Batas waktu pendaftaran dan pengisian evidence telah ditutup oleh verifikator.');

        $kategoriList = [
            'klasifikasi' => 'Klasifikasi Inovasi',
            'jenis_inovasi' => 'Jenis Inovasi',
            'bentuk_inovasi_daerah' => 'Bentuk Inovasi Daerah',
            'asta_cipta' => 'Asta Cita',
            'program_prioritas' => 'Program Prioritas Walikota',
            'urusan_pemerintah' => 'Urusan Pemerintah',
            'misi_walikota' => 'Misi Walikota',
        ];

        return view('dashboard.ima.settings', compact(
            'dropdowns', 
            'indicators', 
            'kategoriList', 
            'schedules', 
            'isLocked', 
            'lockNotice'
        ));
    }

    public function storeDropdown(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string',
            'label' => 'required|string',
        ]);

        $labels = explode("\n", $request->label);
        foreach ($labels as $label) {
            $cleanLabel = trim($label);
            if (!empty($cleanLabel)) {
                ImaDropdown::create([
                    'kategori' => $request->kategori,
                    'label' => $cleanLabel,
                    'is_active' => true
                ]);
            }
        }

        return back()->with('success', 'Opsi dropdown berhasil ditambahkan.');
    }

    // ==========================================
    // HANDLER MASTER PILAR SDGs
    // ==========================================
    public function storeSdg(Request $request)
    {
        $request->validate([
            'label'     => 'required|string|max:500', // DUKUNG HINGGA 500 KARAKTER
            'kode'      => 'nullable|string|max:50',
            'warna'     => 'required|string|max:50',
            'icon'      => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'deskripsi' => 'nullable|string',
        ]);

        $iconPath = null;
        if ($request->hasFile('icon')) {
            $iconPath = $request->file('icon')->store('ima/sdgs', 'public');
        }

        ImaDropdown::create([
            'kategori'  => 'sdgs',
            'kode'      => $request->kode ?: 'SDG',
            'icon_path' => $iconPath,
            'warna'     => $request->warna ?: '#E5243B',
            'label'     => mb_substr(trim($request->label), 0, 500),
            'deskripsi' => $request->deskripsi,
            'targets'   => '[]',
            'is_active' => true
        ]);

        return back()->with('success', 'Pilar SDGs berhasil ditambahkan.');
    }

    // ==========================================
    // HANDLER SIMPAN TARGET & INDIKATOR BERTINGKAT
    // ==========================================
    public function storeSdgTargets(Request $request, ImaDropdown $dropdown)
    {
        $targetsInput = $request->input('targets_json');
        
        $decoded = json_decode($targetsInput, true);
        if (!is_array($decoded)) {
            return back()->with('error', 'Format data Target dan Indikator tidak valid.');
        }

        // Bersihkan dan batasi tiap elemen hingga 500 karakter
        $cleanTargets = [];
        foreach ($decoded as $t) {
            $kodeTarget = trim($t['kode_target'] ?? '');
            $deskripsiTarget = trim($t['deskripsi_target'] ?? '');

            if (empty($kodeTarget) && empty($deskripsiTarget)) {
                continue;
            }

            $cleanIndikators = [];
            if (!empty($t['indikators']) && is_array($t['indikators'])) {
                foreach ($t['indikators'] as $ind) {
                    $namaIndikator = trim($ind['nama_indikator'] ?? '');
                    if (!empty($namaIndikator)) {
                        $cleanIndikators[] = [
                            'kode_indikator' => mb_substr(trim($ind['kode_indikator'] ?? ''), 0, 50),
                            'nama_indikator' => mb_substr($namaIndikator, 0, 500) // DUKUNG HINGGA 500 KARAKTER
                        ];
                    }
                }
            }

            $cleanTargets[] = [
                'kode_target'      => mb_substr($kodeTarget, 0, 50),
                'deskripsi_target' => mb_substr($deskripsiTarget, 0, 500), // DUKUNG HINGGA 500 KARAKTER
                'indikators'       => $cleanIndikators
            ];
        }

        $dropdown->update([
            'targets' => json_encode($cleanTargets)
        ]);

        return back()->with('success', 'Target dan Indikator untuk pilar ' . $dropdown->label . ' berhasil diperbarui.');
    }

    public function destroyDropdown(ImaDropdown $dropdown)
    {
        if (!empty($dropdown->icon_path) && Storage::disk('public')->exists($dropdown->icon_path)) {
            Storage::disk('public')->delete($dropdown->icon_path);
        }

        $dropdown->delete();
        return back()->with('success', 'Data berhasil dihapus.');
    }

    public function storeIndicator(Request $request)
    {
        foreach ($request->input('indikator', []) as $id => $data) {
            $indicator = ImaIndicator::findOrFail($id);
            $indicator->nama_indikator = $data['nama'];
            $indicator->deskripsi_panduan = $data['deskripsi'] ?? null;
            $indicator->video_url = $data['video_url'] ?? null;
            $indicator->parameter_options = $data['expected_files'] ?? []; 
            $indicator->pengali = (int) ($data['pengali'] ?? 1);

            $params = [];
            if (isset($data['params']) && is_array($data['params'])) {
                foreach ($data['params'] as $p) {
                    if (!empty($p['label'])) {
                        $params[] = [
                            'label' => $p['label'],
                            'poin' => (int) ($p['poin'] ?? 0)
                        ];
                    }
                }
            }
            $indicator->pilihan_parameter = $params;

            if ($request->hasFile("indikator.{$id}.file")) {
                if ($indicator->file_panduan_path) {
                    Storage::disk('public')->delete($indicator->file_panduan_path);
                }
                $indicator->file_panduan_path = $request->file("indikator.{$id}.file")->store('ima/pedoman', 'public');
            }

            $indicator->save();
        }

        return back()->with('success', 'Pengaturan 20 Indikator dan Skema Penilaian berhasil disimpan.');
    }

    public function storeSchedule(Request $request)
    {
        $request->validate([
            'fase_nama'       => 'required|string|max:255',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'urutan'          => 'required|integer',
            'deskripsi'       => 'nullable|string',
        ]);

        ImaSchedule::create($request->only([
            'fase_nama', 'tanggal_mulai', 'tanggal_selesai', 'urutan', 'deskripsi'
        ]));

        return back()->with('success', 'Jadwal tahapan lomba berhasil ditambahkan.');
    }

    public function destroySchedule(ImaSchedule $schedule)
    {
        $schedule->delete();
        return back()->with('success', 'Jadwal tahapan berhasil dihapus.');
    }

    public function toggleSubmissionLock(Request $request)
    {
        $isLocked = $request->boolean('is_submission_locked');
        ImaSetting::set('is_submission_locked', $isLocked ? '1' : '0');

        if ($request->filled('lock_notice_message')) {
            ImaSetting::set('lock_notice_message', $request->lock_notice_message);
        }

        $msg = $isLocked 
            ? 'Pengisian dan pembaruan inovasi oleh inovator BERHASIL DIKUNCI.' 
            : 'Pengisian inovasi TELAH DIBUKA kembali untuk seluruh inovator.';

        return back()->with('success', $msg);
    }
}