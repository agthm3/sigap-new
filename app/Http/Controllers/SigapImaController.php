<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ImaDropdown;
use App\Models\ImaEvidence;
use App\Models\ImaEvidenceFile;
use App\Models\ImaIndicator;
use App\Models\ImaInovasi;
use App\Models\ImaSchedule;
use App\Models\ImaSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SigapImaController extends Controller
{
    /**
     * Helper cek apakah pengisian sedang dikunci untuk Inovator
     */
    protected function checkSubmissionLock()
    {
        $user = Auth::user();
        $isLocked = ImaSetting::get('is_submission_locked', '0') === '1';

        // Jika dikunci dan bukan tim admin/verifikator
        if ($isLocked && !$user->hasAnyRole(['admin', 'superadmin', 'verif_inovasi'])) {
            $message = ImaSetting::get('lock_notice_message', 'Periode pengisian dan pembaruan inovasi telah ditutup.');
            return $message;
        }

        return false;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = ImaInovasi::with('evidences.indicator');

        // Jika bukan admin, hanya bisa melihat inovasi yang dibuatnya
        if (!$user->hasAnyRole(['admin', 'superadmin', 'verif_inovasi'])) {
            $query->where('user_id', $user->id);
        }

        // Pencarian (q)
        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function ($w) use ($searchTerm) {
                $w->where('judul', 'like', "%{$searchTerm}%")
                  ->orWhere('operator_nama', 'like', "%{$searchTerm}%")
                  ->orWhere('opd_unit', 'like', "%{$searchTerm}%");
            });
        }

        // Filter Kategori (PRO/PEMULA)
        if ($request->filled('kategori')) {
            $query->where('kategori_ima', $request->kategori);
        }

        // Filter Status Asistensi
        if ($request->filled('status')) {
            $query->where('asistensi_status', $request->status);
        }

        // Ambil data dengan paginasi
        $items = $query->latest()->paginate(15)->withQueryString();

        // Data Linimasa & Status Kunci
        $schedules  = ImaSchedule::where('is_active', true)->orderBy('urutan')->orderBy('tanggal_mulai')->get();
        $isLocked   = ImaSetting::get('is_submission_locked', '0') === '1';
        $lockNotice = ImaSetting::get('lock_notice_message', 'Periode pengisian dan pembaruan inovasi telah ditutup oleh panitia.');

        return view('dashboard.ima.index', compact('items', 'schedules', 'isLocked', 'lockNotice'));
    }

    public function create()
    {
        if ($lockMsg = $this->checkSubmissionLock()) {
            return redirect()->route('sigap-ima.index')->with('error', $lockMsg);
        }

        $dropdowns = ImaDropdown::where('is_active', true)
                        ->get()
                        ->groupBy('kategori');

        return view('dashboard.ima.create', compact('dropdowns'));
    }

    public function uploadTemp(Request $request)
    {
        if ($lockMsg = $this->checkSubmissionLock()) {
            return response()->json(['success' => false, 'message' => $lockMsg], 403);
        }

        $request->validate([
            'file' => 'required|file'
        ]);

        $file = $request->file('file');
        $path = $file->store('ima/temp', 'public');

        return response()->json([
            'success'       => true,
            'temp_path'     => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getClientMimeType(),
            'size'          => $file->getSize()
        ]);
    }

    public function store(Request $request)
    {
        if ($lockMsg = $this->checkSubmissionLock()) {
            return redirect()->route('sigap-ima.index')->with('error', $lockMsg);
        }

        $request->validate([
            'kategori_ima'      => 'required|string',
            'operator_nama'     => 'required|string|max:255',
            'operator_wa'       => 'required|string|max:20',
            'judul'             => 'required|string|max:255',
            'sampul_file'       => 'required|string',
            'anggaran_file'     => 'required|string',
            'koordinat'         => 'nullable|string|max:300',
            'rancang_bangun'    => 'required|string|min:300',
            'videos'            => 'required|array|min:3|max:5',
            'videos.*.judul'    => 'required|string|max:255',
            'videos.*.url'      => 'required|url|max:500',
        ]);

        $data = $request->except(['_token', 'sampul_file', 'anggaran_file', 'profil_bisnis_file', 'haki_file', 'penghargaan_file', 'videos']);
        $data['user_id'] = Auth::id();
        $data['asistensi_status'] = 'Menunggu Verifikasi';

        // Pindahkan Foto Sampul
        if ($request->filled('sampul_file')) {
            $tempPath = $request->sampul_file;
            if (Storage::disk('public')->exists($tempPath)) {
                $newPath = str_replace('ima/temp/', 'ima/sampul/', $tempPath);
                Storage::disk('public')->makeDirectory('ima/sampul');
                Storage::disk('public')->move($tempPath, $newPath);
                $data['sampul_file'] = $newPath;
            }
        }

        // Pindahkan Lampiran
        Storage::disk('public')->makeDirectory('ima/lampiran');
        foreach (['anggaran_file', 'profil_bisnis_file', 'haki_file', 'penghargaan_file'] as $fileKey) {
            if ($request->filled($fileKey)) {
                $tempPath = $request->$fileKey;
                if (Storage::disk('public')->exists($tempPath)) {
                    $newPath = str_replace('ima/temp/', 'ima/lampiran/', $tempPath);
                    Storage::disk('public')->move($tempPath, $newPath);
                    $data[$fileKey] = $newPath;
                }
            }
        }

        $inovasi = ImaInovasi::create($data);

        if ($request->has('videos') && method_exists($inovasi, 'referensiVideos')) {
            foreach ($request->videos as $video) {
                if (!empty($video['judul']) && !empty($video['url'])) {
                    $inovasi->referensiVideos()->create([
                        'judul'      => $video['judul'],
                        'deskripsi'  => $video['deskripsi'] ?? null,
                        'video_url'  => $video['url'],
                    ]);
                }
            }
        }

        return redirect()->route('sigap-ima.index')->with('success', 'Profil Inovasi SIGAP IMA berhasil didaftarkan.');
    }

    public function evidenceForm($id)
    {
        $inovasi = ImaInovasi::findOrFail($id);
        
        if (Auth::id() !== $inovasi->user_id && !Auth::user()->hasAnyRole(['admin', 'superadmin', 'verif_inovasi'])) {
            abort(403, 'Anda tidak berhak mengakses halaman ini.');
        }

        if ($lockMsg = $this->checkSubmissionLock()) {
            return redirect()->route('sigap-ima.show', $inovasi->id)->with('error', $lockMsg);
        }

        $indicators = ImaIndicator::orderBy('no_urut')->get();
        $evidences = ImaEvidence::where('ima_inovasi_id', $id)
                        ->with('files')
                        ->get()
                        ->keyBy('ima_indicator_id');

        return view('dashboard.ima.evidence', compact('inovasi', 'indicators', 'evidences'));
    }

    public function evidenceStore(Request $request, $id)
    {
        $inovasi = ImaInovasi::findOrFail($id);

        if (Auth::id() !== $inovasi->user_id && !Auth::user()->hasAnyRole(['admin', 'superadmin', 'verif_inovasi'])) {
            abort(403);
        }

        if ($lockMsg = $this->checkSubmissionLock()) {
            return redirect()->route('sigap-ima.show', $inovasi->id)->with('error', $lockMsg);
        }

        $inputData = $request->input('indikator', []);

        foreach ($inputData as $ind_id => $data) {
            if (empty($data['parameter_label']) && empty($data['deskripsi']) && empty($data['link_url']) && empty($data['temp_files'])) {
                continue;
            }

            $evidence = ImaEvidence::updateOrCreate(
                ['ima_inovasi_id' => $inovasi->id, 'ima_indicator_id' => $ind_id],
                [
                    'no_urut'          => $data['no_urut'],
                    'parameter_label'  => $data['parameter_label'] ?? null,
                    'parameter_weight' => $data['parameter_weight'] ?? 0,
                    'deskripsi'        => $data['deskripsi'] ?? null,
                    'link_url'         => $data['link_url'] ?? null,
                ]
            );

            if (!empty($data['temp_files'])) {
                foreach ($data['temp_files'] as $tempPath) {
                    if (Storage::disk('public')->exists($tempPath)) {
                        $newPath = str_replace('ima/temp/', 'ima/evidence/', $tempPath);
                        Storage::disk('public')->move($tempPath, $newPath);

                        ImaEvidenceFile::create([
                            'ima_evidence_id' => $evidence->id,
                            'file_path'       => $newPath,
                            'file_name'       => basename($newPath),
                        ]);
                    }
                }
            }
        }

        return back()->with('success', 'Data Evidence berhasil disimpan/diperbarui.');
    }

    public function destroyEvidenceFile($fileId)
    {
        if ($lockMsg = $this->checkSubmissionLock()) {
            return back()->with('error', $lockMsg);
        }

        $file = ImaEvidenceFile::findOrFail($fileId);
        
        if (Auth::id() !== $file->evidence->inovasi->user_id && !Auth::user()->hasAnyRole(['admin', 'superadmin', 'verif_inovasi'])) {
            abort(403);
        }

        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }
        $file->delete();

        return back()->with('success', 'File bukti berhasil dihapus.');
    }

    public function evidenceStoreJson(Request $request, $id)
    {
        $inovasi = ImaInovasi::findOrFail($id);

        if (Auth::id() !== $inovasi->user_id && !Auth::user()->hasAnyRole(['admin', 'superadmin', 'verif_inovasi'])) {
            return response()->json(['success' => false, 'message' => 'Tidak memiliki hak akses.'], 403);
        }

        if ($lockMsg = $this->checkSubmissionLock()) {
            return response()->json(['success' => false, 'message' => $lockMsg], 403);
        }

        $items = $request->input('items', []);

        foreach ($items as $item) {
            $indicatorId = $item['indicator_id'] ?? null;
            if (!$indicatorId) continue;

            $paramLabel  = $item['parameter_label'] ?? null;
            $paramWeight = (int) ($item['parameter_weight'] ?? 0);
            $deskripsi   = $item['deskripsi'] ?? null;
            $linkUrl     = $item['link_url'] ?? null;
            $tempFiles   = $item['temp_files'] ?? [];

            if (blank($paramLabel) && blank($deskripsi) && blank($linkUrl) && empty($tempFiles)) {
                continue;
            }

            $evidence = ImaEvidence::updateOrCreate(
                [
                    'ima_inovasi_id'   => $inovasi->id,
                    'ima_indicator_id' => $indicatorId
                ],
                [
                    'no_urut'          => $item['no_urut'] ?? 0,
                    'parameter_label'  => $paramLabel,
                    'parameter_weight' => $paramWeight,
                    'deskripsi'        => $deskripsi,
                    'link_url'         => $linkUrl,
                ]
            );

            foreach ($tempFiles as $fileData) {
                $tempPath = is_array($fileData) ? ($fileData['temp_path'] ?? '') : $fileData;
                $origName = is_array($fileData) ? ($fileData['original_name'] ?? basename($tempPath)) : basename($tempPath);

                if ($tempPath && Storage::disk('public')->exists($tempPath)) {
                    $finalPath = str_replace('ima/temp/', "ima/evidence/{$inovasi->id}/", $tempPath);
                    Storage::disk('public')->move($tempPath, $finalPath);

                    ImaEvidenceFile::create([
                        'ima_evidence_id' => $evidence->id,
                        'file_path'       => $finalPath,
                        'file_name'       => $origName,
                        'file_size'       => Storage::disk('public')->exists($finalPath) ? Storage::disk('public')->size($finalPath) : null,
                    ]);
                }
            }
        }

        return response()->json([
            'success'  => true,
            'message'  => 'Seluruh evidence dan berkas berhasil disimpan.',
            'redirect' => route('sigap-ima.evidence', $inovasi->id)
        ]);
    }

    public function show($id)
    {
        $inovasi = ImaInovasi::with(['user', 'evidences.files', 'verifikator'])->findOrFail($id);
        $user = Auth::user();

        if ($user->id !== $inovasi->user_id && !$user->hasAnyRole(['admin', 'superadmin', 'verif_inovasi'])) {
            abort(403, 'Anda tidak memiliki akses untuk melihat inovasi ini.');
        }

        $indicators = ImaIndicator::orderBy('no_urut')->get();
        $evidencesMap = $inovasi->evidences->keyBy('ima_indicator_id');
        $dropdowns = ImaDropdown::where('is_active', true)->get()->groupBy('kategori');
        
        $isLocked = ImaSetting::get('is_submission_locked', '0') === '1';
        $lockNotice = ImaSetting::get('lock_notice_message', 'Pengisian dan pengubahan inovasi saat ini dikunci oleh panitia.');

        return view('dashboard.ima.show', compact('inovasi', 'indicators', 'evidencesMap', 'user', 'dropdowns', 'isLocked', 'lockNotice'));
    }

    public function update(Request $request, $id)
    {
        $inovasi = ImaInovasi::findOrFail($id);

        if (Auth::id() !== $inovasi->user_id && !Auth::user()->hasAnyRole(['admin', 'superadmin', 'verif_inovasi'])) {
            abort(403, 'Akses ditolak.');
        }

        if ($lockMsg = $this->checkSubmissionLock()) {
            return redirect()->route('sigap-ima.show', $inovasi->id)->with('error', $lockMsg);
        }

        $request->validate([
            'judul'         => 'required|string|max:255',
            'operator_nama' => 'required|string|max:255',
            'operator_wa'   => 'required|string|max:20',
        ]);

        $data = $request->except(['_token', '_method', 'sampul_file', 'anggaran_file', 'profil_bisnis_file', 'haki_file', 'penghargaan_file']);

        if ($request->filled('sampul_file')) {
            $tempPath = $request->sampul_file;
            if (Storage::disk('public')->exists($tempPath)) {
                $newPath = str_replace('ima/temp/', 'ima/sampul/', $tempPath);
                Storage::disk('public')->makeDirectory('ima/sampul');
                Storage::disk('public')->move($tempPath, $newPath);
                $data['sampul_file'] = $newPath;
            }
        }

        $lampiranFiles = ['anggaran_file', 'profil_bisnis_file', 'haki_file', 'penghargaan_file'];
        foreach ($lampiranFiles as $key) {
            if ($request->filled($key)) {
                $tempPath = $request->$key;
                if (Storage::disk('public')->exists($tempPath)) {
                    $newPath = str_replace('ima/temp/', 'ima/lampiran/', $tempPath);
                    Storage::disk('public')->makeDirectory('ima/lampiran');
                    Storage::disk('public')->move($tempPath, $newPath);
                    $data[$key] = $newPath;
                }
            }
        }

        $inovasi->update($data);

        return redirect()->route('sigap-ima.show', $inovasi->id)->with('success', 'Profil inovasi berhasil diperbarui.');
    }

    public function reviewProfile(Request $request, $id)
    {
        $inovasi = ImaInovasi::findOrFail($id);
        
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin', 'verif_inovasi'])) {
            abort(403);
        }

        $request->validate([
            'asistensi_status' => 'required|in:Menunggu Verifikasi,Disetujui,Revisi,Ditolak',
            'asistensi_note'   => 'nullable|string'
        ]);

        $inovasi->update([
            'asistensi_status' => $request->asistensi_status,
            'asistensi_note'   => $request->asistensi_note,
            'asistensi_by'     => Auth::id(),
            'asistensi_at'     => now(),
        ]);

        return back()->with('success', 'Review Profil Inovasi berhasil disimpan.');
    }

    public function reviewEvidence(Request $request, $evidence_id)
    {
        $evidence = ImaEvidence::findOrFail($evidence_id);
        
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin', 'verif_inovasi'])) {
            abort(403);
        }

        $request->validate([
            'review_status' => 'required|in:Disetujui,Revisi,Ditolak',
            'review_note'   => 'nullable|string'
        ]);

        $evidence->update([
            'review_status' => $request->review_status,
            'review_note'   => $request->review_note,
        ]);

        return back()->with('success', 'Review Indikator Evidence berhasil disimpan.');
    }
}