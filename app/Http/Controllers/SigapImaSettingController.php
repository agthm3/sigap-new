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
        $dropdowns = ImaDropdown::orderBy('kategori')->orderBy('label')->get()->groupBy('kategori');
        
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

    public function destroyDropdown(ImaDropdown $dropdown)
    {
        $dropdown->delete();
        return back()->with('success', 'Opsi dropdown berhasil dihapus.');
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

    // ==========================================
    // HANDLER JADWAL & KUNCI PENGISIAN
    // ==========================================
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