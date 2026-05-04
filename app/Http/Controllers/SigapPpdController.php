<?php
namespace App\Http\Controllers;

use App\Models\PpdKegiatan;
use App\Models\PpdLembarLaporan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PpdLembarFoto;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class SigapPpdController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = PpdKegiatan::with(['pegawai', 'creator'])
            ->latest();

        if (!$user->hasAnyRole(['admin', 'verif_ppd'])) {
            $query->whereHas('pegawai', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        $kegiatans = $query->paginate(10);

        return view('dashboard.ppd.index', compact('kegiatans'));
    }

    public function create()
    {
        $employees = User::role('employee')
            ->orderBy('name')
            ->get();

        return view('dashboard.ppd.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'         => ['required', 'string', 'max:255'],
            'kategori'       => ['required', 'in:bimtek,koordinasi'],
            'hari_tanggal'   => ['required', 'string', 'max:255'],
            'tempat'         => ['required', 'string', 'max:255'],
            'jumlah_lembar'  => ['nullable', 'integer', 'min:1', 'max:20'],
            'pegawai_ids'    => ['required', 'array', 'min:1'],
            'pegawai_ids.*'  => ['integer', 'exists:users,id'],
        ]);

        $jumlahDefault = $request->kategori === 'bimtek' ? 4 : 1;
        $jumlahLembar  = $request->filled('jumlah_lembar')
            ? (int) $request->jumlah_lembar
            : $jumlahDefault;

        $pegawaiInput = collect($request->pegawai_ids)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->values();

        $pegawaiIdsUnique = $pegawaiInput->unique()->values();
        $duplicateIds = $pegawaiInput->duplicates()->unique()->values();

        $duplicateNames = collect();
        if ($duplicateIds->isNotEmpty()) {
            $duplicateNames = User::whereIn('id', $duplicateIds)->pluck('name');
        }

        $kegiatan = null;

        DB::transaction(function () use ($request, $jumlahLembar, $pegawaiIdsUnique, &$kegiatan) {
            $kegiatan = PpdKegiatan::create([
                'judul'         => $request->judul,
                'kategori'      => $request->kategori,
                'hari_tanggal'  => $request->hari_tanggal,
                'tempat'        => $request->tempat,
                'jumlah_lembar' => $jumlahLembar,
                'status'        => 'draft',
                'created_by'    => Auth::id(),
            ]);

            $kegiatan->pegawai()->sync($pegawaiIdsUnique);

            $defaultDeskripsiBimtek = [
                1 => 'Registrasi dan Pembukaan',
                2 => 'Pemberian Materi',
                3 => 'Pemberian Materi',
                4 => 'Penutupan Kegiatan',
            ];

            foreach ($request->pegawai_ids as $userId) {
                for ($i = 1; $i <= $jumlahLembar; $i++) {

                    $deskripsi = null;

                    if ($request->kategori === 'bimtek') {
                        $deskripsi = $defaultDeskripsiBimtek[$i] ?? null;
                    }

                    PpdLembarLaporan::create([
                        'ppd_kegiatan_id' => $kegiatan->id,
                        'user_id'         => $userId,
                        'lembar_ke'       => $i,
                        'deskripsi'       => $deskripsi,
                    ]);
                }
            }
        });

        $redirect = redirect()
            ->route('sigap-ppd.show', $kegiatan->id)
            ->with('success', 'Kegiatan PPD berhasil dibuat.');

        if ($duplicateNames->isNotEmpty()) {
            $redirect->with(
                'warning_duplicate',
                'Anda dobel memasukkan nama: ' . $duplicateNames->implode(', ') . '. Data disimpan sekali.'
            );
        }

        return $redirect;
    }

    public function show(PpdKegiatan $kegiatan)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['admin', 'verif_ppd'])) {
            abort_unless(
                $kegiatan->pegawai()->where('users.id', $user->id)->exists(),
                403,
                'Anda tidak memiliki akses ke kegiatan ini.'
            );
        }

        $kegiatan->load(['pegawai', 'creator', 'lembar.user', 'lembar.fotos']);

        return view('dashboard.ppd.show', compact('kegiatan'));
    }

    public function exportPdf(PpdKegiatan $kegiatan)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['admin', 'verif_ppd'])) {
            abort_unless(
                $kegiatan->pegawai()->where('users.id', $user->id)->exists(),
                403,
                'Anda tidak memiliki akses ke kegiatan ini.'
            );
        }

        $kegiatan->load(['creator', 'pegawai']);

        $lembarPages = collect();

        for ($i = 1; $i <= (int) $kegiatan->jumlah_lembar; $i++) {
            $lembar = PpdLembarLaporan::with(['user', 'fotos'])
                ->where('ppd_kegiatan_id', $kegiatan->id)
                ->where('lembar_ke', $i)
                ->orderBy('user_id')
                ->first();

            if (!$lembar) {
                continue;
            }

            $lembar->foto_base64 = $lembar->fotos
                ->sortBy('urutan')
                ->values()
                ->map(function ($foto) {
                    $fullPath = storage_path('app/public/' . $foto->foto_path);

                    if (!file_exists($fullPath)) {
                        return null;
                    }

                    $mime = mime_content_type($fullPath) ?: 'image/jpeg';

                    return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($fullPath));
                })
                ->filter()
                ->values();

            $lembarPages->push($lembar);
        }

        $pdf = Pdf::loadView('dashboard.ppd.pdf', [
                'kegiatan'    => $kegiatan,
                'lembarPages'  => $lembarPages,
            ])
            ->setPaper('f4', 'portrait');

        return $pdf->download('ppd-' . str()->slug($kegiatan->judul) . '.pdf');
    }

    public function storeLembar(Request $request, PpdLembarLaporan $lembar)
    {
        $user = Auth::user();

        $allowed = $user->hasAnyRole(['admin', 'verif_ppd']) || (int) $lembar->user_id === (int) $user->id;
        abort_unless($allowed, 403, 'Anda tidak memiliki akses ke lembar ini.');

        $request->validate([
            'deskripsi' => ['nullable', 'string'],
            'foto_1'    => ['nullable', 'image', 'max:5120'],
            'foto_2'    => ['nullable', 'image', 'max:5120'],
            'foto_3'    => ['nullable', 'image', 'max:5120'],
            'foto_4'    => ['nullable', 'image', 'max:5120'],
            'foto_5'    => ['nullable', 'image', 'max:5120'],
            'foto_6'    => ['nullable', 'image', 'max:5120'],
        ]);

        $lembar->update([
            'deskripsi' => $request->deskripsi,
        ]);

        for ($i = 1; $i <= 6; $i++) {
            $key = 'foto_' . $i;

            if ($request->hasFile($key)) {
                $old = $lembar->fotos()->where('urutan', $i)->first();

                if ($old && Storage::disk('public')->exists($old->foto_path)) {
                    Storage::disk('public')->delete($old->foto_path);
                }

                $path = $request->file($key)->store(
                    'ppd/lembar/' . $lembar->id,
                    'public'
                );

                $lembar->fotos()->updateOrCreate(
                    ['urutan' => $i],
                    ['foto_path' => $path]
                );
            }
        }

        return back()->with('success', 'Lembar berhasil disimpan.');
    }

    public function publicIndex(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $kategori = $request->get('kategori', '');

        $query = PpdKegiatan::with(['pegawai', 'lembar.fotos'])
            ->where('status', 'selesai')
            ->latest();

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('tempat', 'like', "%{$q}%")
                    ->orWhere('hari_tanggal', 'like', "%{$q}%");
            });
        }

        if (in_array($kategori, ['bimtek', 'koordinasi'], true)) {
            $query->where('kategori', $kategori);
        }

        $kegiatans = $query->paginate(9)->withQueryString();

        $kegiatans->getCollection()->transform(function ($item) {
            $allFotos = $item->lembar->flatMap(function ($lembar) {
                return $lembar->fotos;
            })->values();

            $randomFoto = $allFotos->isNotEmpty()
                ? $allFotos->random()
                : null;

            $item->random_photo = $randomFoto
                ? asset('storage/' . $randomFoto->foto_path)
                : null;

            return $item;
        });

        $totalKegiatan = PpdKegiatan::where('status', 'selesai')->count();
        $totalBimtek = PpdKegiatan::where('status', 'selesai')->where('kategori', 'bimtek')->count();
        $totalKoordinasi = PpdKegiatan::where('status', 'selesai')->where('kategori', 'koordinasi')->count();
        $totalPegawai = PpdKegiatan::where('status', 'selesai')
            ->withCount('pegawai')
            ->get()
            ->sum('pegawai_count');

        return view('SigapPpd.home.index', compact(
            'kegiatans',
            'totalKegiatan',
            'totalBimtek',
            'totalKoordinasi',
            'totalPegawai',
            'q',
            'kategori'
        ));
    }
    public function updateStatus(Request $request, PpdKegiatan $kegiatan)
    {
        $request->validate([
            'status' => ['required', 'in:draft,proses,selesai'],
        ]);

        $kegiatan->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Status kegiatan berhasil diperbarui.');
    }
}