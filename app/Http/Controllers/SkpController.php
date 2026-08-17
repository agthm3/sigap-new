<?php

namespace App\Http\Controllers;

use App\Models\SigapAgenda;
use App\Models\Skp;
use App\Models\SkpFoto;
use App\Models\SkpKumpulan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use App\Models\PpdKegiatan;

class SkpController extends Controller
{
    public function index(Request $request)
    {
        $query = Skp::with(['pegawais', 'creator', 'fotos'])->withCount('fotos')->latest();

        if ($request->filled('search')) {
            $query->where('judul_kegiatan', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('pegawai_id')) {
            $query->whereHas('pegawais', function ($q) use ($request) {
                $q->where('users.id', $request->pegawai_id);
            });
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $skps = $query->paginate(9)->withQueryString();
        $employees = User::role('employee')->select('id', 'name')->get(); 

        // OLAH SUB-ITEM PENUGASAN SECARA SPESIFIK
        $agendas = [];
        $rawAgendas = SigapAgenda::with('items')->orderBy('date', 'desc')->get();

        foreach ($rawAgendas as $agenda) {
            foreach ($agenda->items as $item) {
                // Ambil deskripsi penugasan spesifik (misal: "Menghadiri acara Penyusunan Dokumen...")
                $judulSpesifik = !empty($item->description) ? $item->description : $agenda->unit_title;

                $agendas[] = [
                    'id'          => $agenda->id,
                    'unit_title'  => $judulSpesifik, // Menggunakan judul penugasan spesifik
                    'date'        => $agenda->date,
                    'place'       => $item->place ?? '-',
                    'assignees'   => $item->assignees ?? ''
                ];
            }
        }

        $total_dokumentasi = SkpFoto::count();

        return view('dashboard.skp.index', compact('skps', 'employees', 'agendas', 'total_dokumentasi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pegawai_ids'   => 'required|array',
            'pegawai_ids.*' => 'exists:users,id',
            'judul_kegiatan'=> 'required|string|max:500',
            'tanggal'       => 'required|date',
            'dokumentasi'   => 'required|array',
            'dokumentasi.*' => 'image|mimes:jpeg,png,jpg,webp|max:20480', // Max 20MB per file sebelum dikompres
        ]);

        // 1. Simpan Data Utama SKP
        $skp = Skp::create([
            'agenda_id'     => $request->source_mode === 'agenda' ? $request->agenda_id : null,
            'judul_kegiatan'=> $request->judul_kegiatan,
            'tanggal'       => $request->tanggal,
            'creator_id'    => auth()->id(),
        ]);

        $skp->pegawais()->attach($request->pegawai_ids);

        // 2. Inisialisasi Manager Intervention Image v3 (GD Driver)
        $manager = new ImageManager(new Driver());

        if ($request->hasFile('dokumentasi')) {
            foreach ($request->file('dokumentasi') as $file) {
                
                // Buat nama file unik berformat .jpg
                $filename = 'skp_' . time() . '_' . Str::random(8) . '.jpg';
                $relativePath = 'skp_dokumentasi/' . $filename;

                // Baca & olah gambar menggunakan Intervention Image v3
                $image = $manager->read($file->getRealPath());

                // Scale down jika lebar melebihi 1200px (rasio tetap terjaga)
                $image->scaleDown(width: 1200);

                // Encode gambar ke format JPG dengan kualitas 75%
                $encodedImage = $image->toJpg(quality: 75);

                // Simpan langsung ke Storage Disk public
                Storage::disk('public')->put($relativePath, (string) $encodedImage);

                // Simpan path ke database
                $skp->fotos()->create([
                    'file_path' => $relativePath
                ]);
            }
        }

        return redirect()->back()->with('success', 'Laporan SKP beserta foto yang telah dikompresi berhasil disimpan.');
    }

    // CARI BERDASARKAN SLUG
    public function show($slug)
    {
        $skp = Skp::with(['pegawais', 'creator', 'fotos', 'agenda'])->where('slug', $slug)->firstOrFail();
        
        return view('dashboard.skp.show', compact('skp')); 
    }

    // HAPUS BERDASARKAN SLUG
    public function destroy($slug)
    {
        $skp = Skp::with('fotos')->where('slug', $slug)->firstOrFail();

        foreach ($skp->fotos as $foto) {
            if (Storage::disk('public')->exists($foto->file_path)) {
                Storage::disk('public')->delete($foto->file_path);
            }
        }

        $skp->delete();

        return redirect()->back()->with('success', 'Laporan SKP berhasil dihapus.');
    }

    public function pribadi(Request $request)
    {
        $userId = auth()->id();

        $query = Skp::with(['pegawais', 'creator', 'fotos'])
            ->withCount('fotos')
            ->whereHas('pegawais', function ($q) use ($userId) {
                $q->where('users.id', $userId);
            })
            ->latest();

        // Filter Nama Kegiatan / Judul
        if ($request->filled('search')) {
            $query->where('judul_kegiatan', 'like', '%' . $request->search . '%');
        }

        // Filter Tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        // Filter Kategori (Misal: TUPOKSI / DIREKTIF)
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter Tipe Evidence (Misal: foto / pdf)
        if ($request->filled('tipe_evidence')) {
            $query->where('tipe_evidence', $request->tipe_evidence);
        }

        $skps = $query->paginate(9)->withQueryString();

        $total_dokumentasi = SkpFoto::whereHas('skp.pegawais', function ($q) use ($userId) {
            $q->where('users.id', $userId);
        })->count();

        return view('dashboard.skp.pribadi', compact('skps', 'total_dokumentasi'));
    }

    public function uploadMandiri()
    {
        $userName = auth()->user()->name;

        $myAgendas = [];
        $rawAgendas = SigapAgenda::with('items')
            ->whereHas('items', function ($q) use ($userName) {
                $q->where('assignees', 'like', '%' . $userName . '%');
            })
            ->orderBy('date', 'desc')
            ->get();

        foreach ($rawAgendas as $agenda) {
            foreach ($agenda->items as $item) {
                // Cek apakah item ini menugaskan pegawai yang sedang login
                if (str_contains(strtolower($item->assignees ?? ''), strtolower($userName))) {
                    $judulSpesifik = !empty($item->description) ? $item->description : $agenda->unit_title;

                    // PARSE JSON ASSIGNEES UNTUK MENGAMBIL USER DITUGASKAN
                    $assignedPegawais = [];
                    if (!empty($item->assignees)) {
                        try {
                            $decoded = json_decode($item->assignees, true);
                            
                            // Jika format JSON valid
                            if (is_array($decoded) && isset($decoded['users'])) {
                                foreach ($decoded['users'] as $u) {
                                    // Ambil hanya pegawai LAIN (selain user yang sedang login)
                                    if (isset($u['id']) && $u['id'] != auth()->id()) {
                                        $assignedPegawais[] = [
                                            'id'   => $u['id'],
                                            'name' => $u['name'] ?? 'Pegawai'
                                        ];
                                    }
                                }
                            }
                        } catch (\Throwable $e) {
                            // Fallback jika assignees berformat string biasa
                            $assignedPegawais = [];
                        }
                    }

                    $myAgendas[] = [
                        'id'          => $agenda->id,
                        'unit_title'  => $judulSpesifik,
                        'date'        => $agenda->date,
                        'place'       => $item->place ?? '-',
                        'time_text'   => $item->time_text ?? '-',
                        'description' => $item->description ?? '',
                        'pegawais'    => $assignedPegawais // Hanya berisi rekan se-tim di agenda ini!
                    ];
                }
            }
        }

        return view('dashboard.skp.upload_mandiri', compact('myAgendas'));
    }

    public function storeMandiri(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'agenda_id'      => 'nullable|exists:sigap_agendas,id',
            'judul_kegiatan' => 'required|string|max:255',
            'tanggal'        => 'required|date',
            'photo_data'     => 'nullable', // bisa array atau string
            'pegawai_ids'    => 'nullable|array',
            'pegawai_ids.*'  => 'exists:users,id',
        ]);

        // Kumpulkan list foto base64
        $photos = [];
        if ($request->filled('photo_data')) {
            if (is_array($request->photo_data)) {
                $photos = $request->photo_data;
            } else {
                $photos = [$request->photo_data];
            }
        }

        if (empty($photos)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Harap sertakan minimal satu foto dokumentasi evidence.'
            ], 422);
        }

        // 2. Simpan Data Utama SKP
        $skp = Skp::create([
            'agenda_id'      => $request->agenda_id,
            'judul_kegiatan' => $request->judul_kegiatan,
            'kategori'       => 'TUPOKSI',
            'tipe_evidence'  => 'foto',
            'tanggal'        => $request->tanggal,
            'deskripsi'      => $request->deskripsi ?? null,
            'creator_id'     => auth()->id(),
        ]);

        // 3. Hubungkan ID Pegawai
        $targetPegawaiIds = array_unique(array_merge([auth()->id()], $request->pegawai_ids ?? []));
        $skp->pegawais()->sync($targetPegawaiIds);

        // 4. Decode Setiap Foto Base64 & Simpan ke Storage
        foreach ($photos as $photoData) {
            if (empty($photoData)) continue;

            $type = 'jpg';
            if (preg_match('/^data:image\/(\w+);base64,/', $photoData, $match)) {
                $type = strtolower($match[1]);
                $photoData = substr($photoData, strpos($photoData, ',') + 1);
            }

            $decoded = base64_decode($photoData);

            if ($decoded !== false) {
                $fileName = 'skp_fotos/' . uniqid() . '_' . Str::random(6) . '.' . $type;
                Storage::disk('public')->put($fileName, $decoded);

                $skp->fotos()->create([
                    'file_path' => $fileName,
                    'caption'   => $request->judul_kegiatan
                ]);
            }
        }

        // 5. Format Daftar Nama Pegawai
        $daftarPegawai = User::whereIn('id', $targetPegawaiIds)->pluck('name')->toArray();
        
        if (count($daftarPegawai) > 1) {
            $listPegawaiStr = "\n" . implode("\n", array_map(fn($nama) => "  • " . $nama, $daftarPegawai));
        } else {
            $listPegawaiStr = " " . ($daftarPegawai[0] ?? auth()->user()->name);
        }

        // 6. Susun Pesan WhatsApp
        $waMessage = "📸 *DOKUMENTASI EVIDENCE SKP MANDIRI*\n"
                   . "━━━━━━━━━━━━━━━━━━━━━━━━\n"
                   . "📌 *Kegiatan:* " . $skp->judul_kegiatan . "\n"
                   . "📅 *Tanggal:* " . \Carbon\Carbon::parse($skp->tanggal)->translatedFormat('d F Y') . "\n"
                   . "👥 *Pegawai Terlibat:*" . $listPegawaiStr . "\n\n"
                   . "🔗 *Lihat Laporan:* " . route('sigap-skp.public-show', $skp->slug);

        $waUrl = 'https://api.whatsapp.com/send?text=' . urlencode($waMessage);

        return response()->json([
            'status'   => 'success',
            'message'  => 'Berhasil menyimpan SKP untuk ' . count($targetPegawaiIds) . ' pegawai.',
            'wa_url'   => $waUrl,
            'redirect' => route('sigap-skp.pribadi')
        ]);
    }
    
    public function publicShow($slug)
    {
        $skp = Skp::with(['pegawais', 'creator', 'fotos', 'agenda'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Arahkan ke view publik khusus tanpa middleware auth
        return view('dashboard.skp.public_show', compact('skp'));
    }

    public function kumpulanIndex(Request $request)
    {
        $userId = auth()->id();

        $kumpulans = SkpKumpulan::where('user_id', $userId)
            ->latest()
            ->paginate(10);

        return view('dashboard.skp.kumpulan_index', compact('kumpulans'));
    }

    /**
     * Halaman Form Pembuatan Kumpulan SKP Berdasarkan Kategori & Bulan
     */
   public function kumpulanCreate(Request $request)
    {
        $userId = auth()->id();
        $bulanTahun = $request->get('bulan', date('Y-m'));
        $kategori = $request->get('kategori', 'DIREKTIF (TUGAS TAMBAHAN)');

        // 1. Ambil seluruh Kumpulan yang sudah pernah dibuat oleh user di bulan ini
        $existingKumpulans = SkpKumpulan::where('user_id', $userId)
            ->where('bulan_tahun', $bulanTahun)
            ->get();

        // 2. Petakan ID SKP & PPD ke kategori kumpulan tempat mereka terdaftar
        $usedSkpCategories = [];
        $usedPpdCategories = [];

        foreach ($existingKumpulans as $kump) {
            $katName = $kump->kategori;

            if (!empty($kump->skp_ids) && is_array($kump->skp_ids)) {
                foreach ($kump->skp_ids as $skpId) {
                    $usedSkpCategories[$skpId][] = $katName;
                }
            }

            if (!empty($kump->ppd_ids) && is_array($kump->ppd_ids)) {
                foreach ($kump->ppd_ids as $ppdId) {
                    $usedPpdCategories[$ppdId][] = $katName;
                }
            }
        }

        // 3. Ambil daftar SKP user di bulan terpilih
        $skpList = Skp::with('fotos')
            ->whereHas('pegawais', function ($q) use ($userId) {
                $q->where('users.id', $userId);
            })
            ->whereYear('tanggal', substr($bulanTahun, 0, 4))
            ->whereMonth('tanggal', substr($bulanTahun, 5, 2))
            ->latest()
            ->get();

        // 4. Ambil daftar PPD user di bulan terpilih
        $ppdList = PpdKegiatan::with(['lembar' => function($q) use ($userId) {
                $q->where('user_id', $userId)->with('fotos');
            }])
            ->whereHas('pegawai', function ($q) use ($userId) {
                $q->where('users.id', $userId);
            })
            ->whereYear('created_at', substr($bulanTahun, 0, 4))
            ->whereMonth('created_at', substr($bulanTahun, 5, 2))
            ->latest()
            ->get();

        return view('dashboard.skp.kumpulan_create', compact(
            'skpList', 
            'ppdList', 
            'bulanTahun', 
            'kategori', 
            'usedSkpCategories', 
            'usedPpdCategories'
        ));
    }

    // 2. UPDATE: kumpulanStore
    public function kumpulanStore(Request $request)
    {
        $request->validate([
            'kategori'       => 'required|string',
            'bulan_tahun'    => 'required|string',
            'judul_kumpulan' => 'required|string|max:255',
            'skp_ids'        => 'nullable|array',
            'ppd_ids'        => 'nullable|array',
        ]);

        // Pastikan minimal ada 1 SKP atau 1 PPD yang dipilih
        if (empty($request->skp_ids) && empty($request->ppd_ids)) {
            return response()->json(['status' => 'error', 'message' => 'Pilih minimal 1 kegiatan SKP atau PPD.'], 400);
        }

        $kumpulan = SkpKumpulan::create([
            'user_id'        => auth()->id(),
            'kategori'       => strtoupper($request->kategori),
            'bulan_tahun'    => $request->bulan_tahun,
            'judul_kumpulan' => $request->judul_kumpulan,
            'skp_ids'        => $request->skp_ids ?? [],
            'ppd_ids'        => $request->ppd_ids ?? [],
        ]);

        $publicUrl = route('sigap-skp.kumpulan.public-show', $kumpulan->slug);

        return response()->json([
            'status'     => 'success',
            'message'    => 'Kumpulan laporan berhasil dibuat!',
            'public_url' => $publicUrl,
            'redirect'   => route('sigap-skp.kumpulan.index')
        ]);
    }

    public function kumpulanEdit($slug)
    {
        $userId = auth()->id();
        
        // Cari data kumpulan yang sedang diedit
        $kumpulan = SkpKumpulan::where('slug', $slug)
            ->where('user_id', $userId)
            ->firstOrFail();

        $bulanTahun = $kumpulan->bulan_tahun;
        $kategori = $kumpulan->kategori;

        // Ambil kumpulan lain milik user di bulan yang sama (kecuali yang sedang diedit)
        $otherKumpulans = SkpKumpulan::where('user_id', $userId)
            ->where('bulan_tahun', $bulanTahun)
            ->where('id', '!=', $kumpulan->id)
            ->get();

        $usedSkpCategories = [];
        $usedPpdCategories = [];

        foreach ($otherKumpulans as $kump) {
            $katName = $kump->kategori;

            if (!empty($kump->skp_ids) && is_array($kump->skp_ids)) {
                foreach ($kump->skp_ids as $skpId) {
                    $usedSkpCategories[$skpId][] = $katName;
                }
            }

            if (!empty($kump->ppd_ids) && is_array($kump->ppd_ids)) {
                foreach ($kump->ppd_ids as $ppdId) {
                    $usedPpdCategories[$ppdId][] = $katName;
                }
            }
        }

        // Ambil daftar SKP di bulan yang sama
        $skpList = Skp::with('fotos')
            ->whereHas('pegawais', function ($q) use ($userId) {
                $q->where('users.id', $userId);
            })
            ->whereYear('tanggal', substr($bulanTahun, 0, 4))
            ->whereMonth('tanggal', substr($bulanTahun, 5, 2))
            ->latest()
            ->get();

        // Ambil daftar PPD di bulan yang sama
        $ppdList = PpdKegiatan::with(['lembar' => function($q) use ($userId) {
                $q->where('user_id', $userId)->with('fotos');
            }])
            ->whereHas('pegawai', function ($q) use ($userId) {
                $q->where('users.id', $userId);
            })
            ->whereYear('created_at', substr($bulanTahun, 0, 4))
            ->whereMonth('created_at', substr($bulanTahun, 5, 2))
            ->latest()
            ->get();

        return view('dashboard.skp.kumpulan_edit', compact(
            'kumpulan', 
            'skpList', 
            'ppdList', 
            'bulanTahun', 
            'kategori',
            'usedSkpCategories',
            'usedPpdCategories'
        ));
    }

    public function kumpulanUpdate(Request $request, $slug)
    {
        $request->validate([
            'kategori'       => 'required|string',
            'bulan_tahun'    => 'required|string',
            'judul_kumpulan' => 'required|string|max:255',
            'skp_ids'        => 'nullable|array',
            'ppd_ids'        => 'nullable|array',
        ]);

        // Pastikan minimal ada 1 SKP atau 1 PPD yang dipilih
        if (empty($request->skp_ids) && empty($request->ppd_ids)) {
            return response()->json(['status' => 'error', 'message' => 'Pilih minimal 1 kegiatan SKP atau PPD.'], 400);
        }

        $kumpulan = SkpKumpulan::where('slug', $slug)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Update data ke database
        $kumpulan->update([
            'kategori'       => strtoupper($request->kategori),
            'bulan_tahun'    => $request->bulan_tahun,
            'judul_kumpulan' => $request->judul_kumpulan,
            'skp_ids'        => $request->skp_ids ?? [],
            'ppd_ids'        => $request->ppd_ids ?? [],
        ]);

        return response()->json([
            'status'     => 'success',
            'message'    => 'Kumpulan laporan berhasil diperbarui!',
            'public_url' => route('sigap-skp.kumpulan.public-show', $kumpulan->slug),
            'redirect'   => route('sigap-skp.kumpulan.index')
        ]);
    }

    // 3. UPDATE: publicShowKumpulan
    public function publicShowKumpulan($slug)
    {
        $kumpulan = SkpKumpulan::with('user')->where('slug', $slug)->firstOrFail();

        // Data SKP
        $skpList = [];
        if (!empty($kumpulan->skp_ids)) {
            $skpList = Skp::with(['fotos', 'creator'])
                ->whereIn('id', $kumpulan->skp_ids)
                ->orderBy('tanggal', 'asc')
                ->get();
        }

        // Data PPD
        $ppdList = [];
        if (!empty($kumpulan->ppd_ids)) {
            $ppdList = PpdKegiatan::with(['lembar' => function($q) use ($kumpulan) {
                    // Hanya load foto dari lembar milik pegawai pembuat rekap ini
                    $q->where('user_id', $kumpulan->user_id)->with('fotos');
                }])
                ->whereIn('id', $kumpulan->ppd_ids)
                ->latest()
                ->get();
        }

        return view('dashboard.skp.kumpulan_public_show', compact('kumpulan', 'skpList', 'ppdList'));
    }
    /**
     * Hapus Kumpulan Rekap SKP
     */
    public function kumpulanDestroy($slug)
    {
        $kumpulan = SkpKumpulan::where('slug', $slug)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $kumpulan->delete();

        return redirect()->back()->with('success', 'Kumpulan laporan berhasil dihapus.');
    }

public function storePdf(Request $request)
    {
        $request->validate([
            'judul_kegiatan' => 'required|string|max:255',
            'tanggal'        => 'required|date',
            'kategori'       => 'nullable|string|max:100',
            'deskripsi'      => 'nullable|string',
            'dokumen_pdf'    => 'required|file|mimes:pdf|max:10240', // Maksimal 10MB
            'pegawai_ids'    => 'nullable|array',
            'pegawai_ids.*'  => 'exists:users,id',
        ], [
            'dokumen_pdf.mimes' => 'Berkas yang diunggah harus berformat PDF.',
            'dokumen_pdf.max'   => 'Ukuran dokumen PDF tidak boleh melebihi 10 MB.',
        ]);

        if ($request->hasFile('dokumen_pdf')) {
            $file = $request->file('dokumen_pdf');
            $filename = 'skp_doc_' . time() . '_' . Str::random(8) . '.pdf';
            $relativePath = $file->storeAs('skp_dokumen', $filename, 'public');

            $skp = Skp::create([
                'judul_kegiatan' => $request->judul_kegiatan,
                'kategori'       => $request->kategori ?? 'TUPOKSI',
                'tipe_evidence'  => 'pdf',
                'file_pdf_path'  => $relativePath,
                'deskripsi'      => $request->deskripsi,
                'tanggal'        => $request->tanggal,
                'creator_id'     => auth()->id(),
            ]);

            // Jika ada pegawai_ids yang dipilih (dari SKP Umum), attach daftar tersebut
            // Jika kosong (dari SKP Pribadi), otomatis attach ke user login
            $targetPegawai = !empty($request->pegawai_ids) ? $request->pegawai_ids : [auth()->id()];
            $skp->pegawais()->sync($targetPegawai);

            return redirect()->back()->with('success', 'Dokumen PDF SKP berhasil diunggah.');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah dokumen PDF.');
    }
    public function monitoring(Request $request)
    {
        // Tangkap bulan & tahun dari filter (Format: YYYY-MM, Default: Bulan & Tahun saat ini)
        $bulanTahun = $request->get('bulan', date('Y-m'));

        // Ambil seluruh user dengan role 'employee' (Pegawai)
        $employees = User::role('employee')
            ->select('id', 'name', 'nip', 'email')
            ->orderBy('name', 'asc')
            ->get();

        $sudahMengisi = [];
        $belumMengisi = [];

        foreach ($employees as $employee) {
            // Cek apakah pegawai sudah membuat Kumpulan SKP Kategori di bulan tersebut
            $kumpulans = SkpKumpulan::where('user_id', $employee->id)
                ->where('bulan_tahun', $bulanTahun)
                ->latest()
                ->get();

            $jumlahKumpulan = $kumpulans->count();

            if ($jumlahKumpulan > 0) {
                // Ambil kumpulans terakhir yang dibuat
                $kumpulanTerakhir = $kumpulans->first();

                // Hitung total item kegiatan (SKP + PPD) yang digabungkan
                $totalKegiatan = (is_array($kumpulanTerakhir->skp_ids) ? count($kumpulanTerakhir->skp_ids) : 0) 
                            + (is_array($kumpulanTerakhir->ppd_ids) ? count($kumpulanTerakhir->ppd_ids) : 0);

                $sudahMengisi[] = [
                    'id'              => $employee->id,
                    'name'            => $employee->name,
                    'nip'             => $employee->nip ?? '-',
                    'total_kumpulan'  => $jumlahKumpulan,
                    'judul_kumpulan'  => $kumpulanTerakhir->judul_kumpulan,
                    'kategori'        => $kumpulanTerakhir->kategori,
                    'total_kegiatan'  => $totalKegiatan,
                    'slug'            => $kumpulanTerakhir->slug,
                    'tgl_dibuat'      => $kumpulanTerakhir->created_at->translatedFormat('d F Y (H:i)'),
                ];
            } else {
                $belumMengisi[] = [
                    'id'   => $employee->id,
                    'name' => $employee->name,
                    'nip'  => $employee->nip ?? '-',
                ];
            }
        }

        $totalPegawai = $employees->count();
        $totalSudah = count($sudahMengisi);
        $totalBelum = count($belumMengisi);

        return view('dashboard.skp.monitoring', compact(
            'sudahMengisi',
            'belumMengisi',
            'bulanTahun',
            'totalPegawai',
            'totalSudah',
            'totalBelum'
        ));
    }

}