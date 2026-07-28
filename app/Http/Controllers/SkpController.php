<?php

namespace App\Http\Controllers;

use App\Models\SigapAgenda;
use App\Models\Skp;
use App\Models\SkpFoto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;

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
        
        $agendas = SigapAgenda::with('items')->orderBy('date', 'desc')->get()->map(function($agenda) {
            $assigneesText = $agenda->items->pluck('assignees')->filter()->implode(', ');
            return [
                'id' => $agenda->id,
                'unit_title' => $agenda->unit_title,
                'date' => $agenda->date,
                'assignees' => $assigneesText
            ];
        });

        $total_dokumentasi = SkpFoto::count();

        return view('dashboard.skp.index', compact('skps', 'employees', 'agendas', 'total_dokumentasi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pegawai_ids'   => 'required|array',
            'pegawai_ids.*' => 'exists:users,id',
            'judul_kegiatan'=> 'required|string|max:255',
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

        // Query hanya SKP yang melibatkan User Login
        $query = Skp::with(['pegawais', 'creator', 'fotos'])
            ->withCount('fotos')
            ->whereHas('pegawais', function ($q) use ($userId) {
                $q->where('users.id', $userId);
            })
            ->latest();

        // Filter berdasarkan nama kegiatan
        if ($request->filled('search')) {
            $query->where('judul_kegiatan', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $skps = $query->paginate(9)->withQueryString();

        // Hitung total dokumentasi foto milik kegiatan pegawai ini
        $total_dokumentasi = SkpFoto::whereHas('skp.pegawais', function ($q) use ($userId) {
            $q->where('users.id', $userId);
        })->count();

        return view('dashboard.skp.pribadi', compact('skps', 'total_dokumentasi'));
    }

    public function uploadMandiri()
    {
        $userName = auth()->user()->name;

        // Ambil SIGAP AGENDA yang menugaskan pegawai login ini
        $myAgendas = SigapAgenda::with('items')
            ->whereHas('items', function ($q) use ($userName) {
                $q->where('assignees', 'like', '%' . $userName . '%');
            })
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($agenda) {
                $item = $agenda->items->first();
                return [
                    'id'          => $agenda->id,
                    'unit_title'  => $agenda->unit_title,
                    'date'        => $agenda->date,
                    'place'       => $item->place ?? '-',
                    'time_text'   => $item->time_text ?? '-',
                    'description' => $item->description ?? '',
                ];
            });

        return view('dashboard.skp.upload_mandiri', compact('myAgendas'));
    }

public function storeMandiri(Request $request)
    {
        // 1. Validasi Request Form Input
        $request->validate([
            'source_mode'    => 'required|in:agenda,manual',
            'agenda_id'      => 'nullable|required_if:source_mode,agenda|exists:sigap_agendas,id',
            'judul_kegiatan' => 'required|string|max:255',
            'tanggal'        => 'required|date',
            'lokasi'         => 'nullable|string|max:255',
            'deskripsi'      => 'nullable|string',
            'photo_data'     => 'required|string', // Data Base64 Image dari Kamera/Canvas
        ]);

        // 2. Simpan Data Utama SKP (Slug di-generate otomatis oleh Model/booted)
        $skp = Skp::create([
            'agenda_id'     => $request->source_mode === 'agenda' ? $request->agenda_id : null,
            'judul_kegiatan' => $request->judul_kegiatan,
            'tanggal'        => $request->tanggal,
            'creator_id'     => auth()->id(),
        ]);

        // 3. Otomatis kaitkan Pegawai yang sedang login ke tabel pivot (sigap_skp_user)
        $skp->pegawais()->attach(auth()->id());

        // 4. Olah & Kompres Foto Base64 Menggunakan Intervention Image v3
        if ($request->filled('photo_data')) {
            $photoData = $request->photo_data;

            // Pisahkan header data URI "data:image/jpeg;base64," dengan payload biner
            if (str_contains($photoData, ';base64,')) {
                $imageParts = explode(';base64,', $photoData);
                $imageBinary = base64_decode($imageParts[1]);
            } else {
                $imageBinary = base64_decode($photoData);
            }

            if ($imageBinary) {
                // Buat nama file unik berformat .jpg
                $filename = 'skp_mandiri_' . time() . '_' . Str::random(8) . '.jpg';
                $relativePath = 'skp_dokumentasi/' . $filename;

                // Inisialisasi Manager Intervention Image v3 (GD Driver)
                $manager = new ImageManager(new Driver());
                $image = $manager->read($imageBinary);

                // Scale down resolusi jika lebar melebihi 1200px (rasio tetap terjaga)
                $image->scaleDown(width: 1200);

                // Encode gambar ke format JPG dengan kualitas 75%
                $encodedImage = $image->toJpg(quality: 75);

                // Simpan biner file ke Storage Disk Public
                Storage::disk('public')->put($relativePath, (string) $encodedImage);

                // Simpan relasi foto ke tabel sigap_skp_fotos
                $skp->fotos()->create([
                    'file_path' => $relativePath
                ]);
            }
        }

        // 5. Susun Teks Caption WhatsApp & Generate Link Publik
        $userName = auth()->user()->name;
        $tglFormatted = \Carbon\Carbon::parse($request->tanggal)->translatedFormat('d F Y');

        // Menggunakan Route Publik tanpa Auth agar WhatsApp Bot Crawler bisa membaca Open Graph Thumbnail
        $publicSkpUrl = route('sigap-skp.public-show', $skp->slug);

        if ($request->source_mode === 'agenda') {
            $caption = "*LAPORAN KEGIATAN (AGENDA)*\n\n"
                     . "👤 *Pegawai:* {$userName}\n"
                     . "📌 *Kegiatan:* {$request->judul_kegiatan}\n"
                     . "📅 *Tanggal:* {$tglFormatted}\n"
                     . "📍 *Lokasi:* " . ($request->lokasi ?: '-') . "\n\n"
                     . "📷 *Lihat Bukti Foto:* {$publicSkpUrl}\n\n"
                     . "_Laporan evidence telah terunggah ke SIGAP SKP._";
        } else {
            $caption = "*LAPORAN MANDIRI KEGIATAN*\n\n"
                     . "👤 *Pegawai:* {$userName}\n"
                     . "📌 *Kegiatan:* {$request->judul_kegiatan}\n"
                     . "📅 *Tanggal:* {$tglFormatted}\n"
                     . "📍 *Lokasi:* " . ($request->lokasi ?: '-') . "\n"
                     . "📝 *Deskripsi:* " . ($request->deskripsi ?: '-') . "\n\n"
                     . "📷 *Lihat Bukti Foto:* {$publicSkpUrl}\n\n"
                     . "_Laporan evidence telah terunggah ke SIGAP SKP._";
        }

        $waUrl = "https://api.whatsapp.com/send?text=" . urlencode($caption);

        // 6. Return JSON Response ke Alpine.js
        return response()->json([
            'status'   => 'success',
            'message'  => 'Laporan mandiri berhasil disimpan.',
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
        

}