<?php

namespace App\Http\Controllers;

use App\Models\SigapDaftarHadirKegiatan;
use App\Models\SigapDaftarHadirPeserta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SigapDaftarHadirController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $base = SigapDaftarHadirKegiatan::query();

        if (!$user->hasAnyRole(['admin', 'verif_daftarhadir'])) {
            $base->where('created_by', $user->id);
        }

        $totalKegiatan = (clone $base)->count();
        $totalDraft    = (clone $base)->where('status', 'draft')->count();
        $totalProses   = (clone $base)->where('status', 'proses')->count();
        $totalSelesai  = (clone $base)->where('status', 'selesai')->count();

        $kegiatans = (clone $base)
            ->withCount('peserta')
            ->latest()
            ->paginate(10);

        return view('dashboard.daftar_hadir.index', compact(
            'kegiatans',
            'totalKegiatan',
            'totalDraft',
            'totalProses',
            'totalSelesai'
        ));
    }

    public function create()
    {
        return view('dashboard.daftar_hadir.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'hari_tanggal'  => ['required', 'string', 'max:255'],
            'tempat'        => ['required', 'string', 'max:255'],
            'waktu'         => ['required', 'string', 'max:255'],
        ]);

        $kegiatan = SigapDaftarHadirKegiatan::create([
            'uuid'          => (string) Str::uuid(),
            'nama_kegiatan' => $request->nama_kegiatan,
            'hari_tanggal'  => $request->hari_tanggal,
            'tempat'        => $request->tempat,
            'waktu'         => $request->waktu,
            'status'        => 'draft',
            'created_by'    => Auth::id(),
        ]);

        return redirect()
            ->route('sigap-daftar-hadir.show', $kegiatan->uuid)
            ->with('success', 'Kegiatan daftar hadir berhasil dibuat.');
    }

    public function show(SigapDaftarHadirKegiatan $kegiatan)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['admin', 'verif_daftarhadir'])) {
            abort_unless((int) $kegiatan->created_by === (int) $user->id, 403, 'Anda tidak memiliki akses ke kegiatan ini.');
        }

        $kegiatan->load([
            'creator',
            'peserta' => fn ($q) => $q->orderBy('urutan_absen')->orderBy('created_at'),
        ]);

        $qrUrl = route('sigap-daftar-hadir.public', $kegiatan->uuid);

        return view('dashboard.daftar_hadir.show', compact('kegiatan', 'qrUrl'));
    }

    public function edit(SigapDaftarHadirKegiatan $kegiatan)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['admin', 'verif_daftarhadir'])) {
            abort_unless((int) $kegiatan->created_by === (int) $user->id, 403, 'Anda tidak memiliki akses ke kegiatan ini.');
        }

        $kegiatan->load([
            'peserta' => fn ($q) => $q->orderBy('urutan_absen')->orderBy('created_at'),
        ]);

        return view('dashboard.daftar_hadir.edit', compact('kegiatan'));
    }

    public function update(Request $request, SigapDaftarHadirKegiatan $kegiatan)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['admin', 'verif_daftarhadir'])) {
            abort_unless((int) $kegiatan->created_by === (int) $user->id, 403, 'Anda tidak memiliki akses ke kegiatan ini.');
        }

        $request->validate([
            'nama_kegiatan'           => ['required', 'string', 'max:255'],
            'hari_tanggal'            => ['required', 'string', 'max:255'],
            'tempat'                  => ['required', 'string', 'max:255'],
            'waktu'                   => ['required', 'string', 'max:255'],
            'peserta'                 => ['sometimes', 'array'],
            'peserta.*.nama'          => ['required_with:peserta', 'string', 'max:255'],
            'peserta.*.instansi'      => ['required_with:peserta', 'string', 'max:255'],
            'peserta.*.gender'        => ['required_with:peserta', 'in:L,P'],
            'peserta.*.no_hp'         => ['required_with:peserta', 'string', 'max:30'],
            'peserta.*.email'         => ['nullable', 'email', 'max:255'],
            'peserta.*.urutan_absen'  => ['required_with:peserta', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($request, $kegiatan) {
            $kegiatan->update([
                'nama_kegiatan' => $request->nama_kegiatan,
                'hari_tanggal'  => $request->hari_tanggal,
                'tempat'        => $request->tempat,
                'waktu'         => $request->waktu,
            ]);

            $pesertaInput = collect($request->input('peserta', []));

            foreach ($pesertaInput as $id => $row) {
                $peserta = SigapDaftarHadirPeserta::where('kegiatan_id', $kegiatan->id)
                    ->where('id', $id)
                    ->firstOrFail();

                $peserta->update([
                    'nama'         => $row['nama'],
                    'instansi'     => $row['instansi'],
                    'gender'       => $row['gender'],
                    'no_hp'        => $row['no_hp'],
                    'email'        => $row['email'] ?? null,
                    'urutan_absen' => (int) $row['urutan_absen'],
                ]);
            }

            $sorted = $kegiatan->peserta()->orderBy('urutan_absen')->orderBy('created_at')->get();

            $urut = 1;
            foreach ($sorted as $item) {
                $item->update(['urutan_absen' => $urut++]);
            }
        });

        return redirect()
            ->route('sigap-daftar-hadir.show', $kegiatan->uuid)
            ->with('success', 'Kegiatan dan data peserta berhasil diperbarui.');
    }

    public function updateStatus(Request $request, SigapDaftarHadirKegiatan $kegiatan)
    {
        $request->validate([
            'status' => ['required', 'in:draft,proses,selesai'],
        ]);

        $kegiatan->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Status kegiatan berhasil diperbarui.');
    }

    public function destroy(SigapDaftarHadirKegiatan $kegiatan)
    {
        DB::transaction(function () use ($kegiatan) {
            foreach ($kegiatan->peserta as $peserta) {
                if ($peserta->ttd_path && Storage::disk('public')->exists($peserta->ttd_path)) {
                    Storage::disk('public')->delete($peserta->ttd_path);
                }
            }

            $kegiatan->peserta()->delete();
            $kegiatan->delete();
        });

        return redirect()
            ->route('sigap-daftar-hadir.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }


    public function publicForm(SigapDaftarHadirKegiatan $kegiatan)
    {
        $kegiatan->loadCount('peserta');

        return view('dashboard.daftar_hadir.public-form', compact('kegiatan'));
    }

    public function searchPeserta(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        if ($q === '') {
            return response()->json([]);
        }

        $items = SigapDaftarHadirPeserta::query()
            ->where('nama', 'like', "%{$q}%")
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->unique(function ($item) {
                return Str::lower(trim($item->nama));
            })
            ->values()
            ->map(function ($item) {
                return [
                    'id'         => $item->id,
                    'nama'       => $item->nama,
                    'instansi'   => $item->instansi,
                    'gender'     => $item->gender,
                    'no_hp'      => $item->no_hp,
                    'email'      => $item->email,
                    'ttd_path'   => $item->ttd_path ? asset('storage/' . $item->ttd_path) : null,
                ];
            });

        return response()->json($items);
    }

    public function storePublic(Request $request, SigapDaftarHadirKegiatan $kegiatan)
    {
        if ($kegiatan->status === 'selesai') {
            return redirect()
                ->route('sigap-daftar-hadir.public', $kegiatan->uuid)
                ->with('error', 'Maaf, kegiatan ini sudah selesai dan tidak menerima peserta baru.');
        }

        $request->validate([
            'nama'              => ['required', 'string', 'max:255'],
            'instansi'          => ['required', 'string', 'max:255'],
            'gender'            => ['required', 'in:L,P'],
            'no_hp'             => ['required', 'string', 'max:30'],
            'email'             => ['nullable', 'email', 'max:255'],
            'ttd_data'          => ['nullable', 'string'],
            'existing_ttd_path' => ['nullable', 'string'],
        ]);

        $nama = trim($request->nama);

        $duplicate = SigapDaftarHadirPeserta::where('kegiatan_id', $kegiatan->id)
            ->whereRaw('LOWER(nama) = ?', [Str::lower($nama)])
            ->exists();

        if ($duplicate) {
            return back()
                ->withInput()
                ->with('error', 'Nama tersebut sudah terdaftar pada kegiatan ini.');
        }

        $ttdPath = null;

        if ($request->filled('ttd_data')) {
            $ttdPath = $this->saveSignatureBase64(
                $request->ttd_data,
                'sigap/daftar-hadir/ttd/' . $kegiatan->id
            );
        } elseif ($request->filled('existing_ttd_path')) {
            $ttdPath = ltrim(str_replace(asset('storage/'), '', $request->existing_ttd_path), '/');
        }

        $nextOrder = (int) (SigapDaftarHadirPeserta::where('kegiatan_id', $kegiatan->id)->max('urutan_absen') ?? 0) + 1;

        SigapDaftarHadirPeserta::create([
            'kegiatan_id'  => $kegiatan->id,
            'nama'         => $nama,
            'instansi'     => $request->instansi,
            'gender'       => $request->gender,
            'no_hp'        => $request->no_hp,
            'email'        => $request->email,
            'ttd_path'     => $ttdPath,
            'urutan_absen' => $nextOrder,
            'created_by'   => null,
        ]);

        return redirect()
            ->route('sigap-daftar-hadir.public', $kegiatan->uuid)
            ->with('success_name', $nama)
            ->with('success_kegiatan', $kegiatan->nama_kegiatan);
    }

    // public function exportPdf(SigapDaftarHadirKegiatan $kegiatan)
    // {
    //     $user = Auth::user();

    //     if (!$user->hasAnyRole(['admin', 'verif_daftarhadir'])) {
    //         abort_unless((int) $kegiatan->created_by === (int) $user->id, 403, 'Anda tidak memiliki akses.');
    //     }

    //     abort_unless($kegiatan->status === 'selesai', 403, 'PDF hanya bisa diexport saat kegiatan berstatus selesai.');

    //     $kegiatan->load([
    //         'peserta' => fn ($q) => $q->orderBy('urutan_absen')->orderBy('created_at'),
    //     ]);

    //     $logoPemkot = null;
    //     $logoBrida  = null;

    //     $pemkotPath = public_path('images/logo-pemkot.png');
    //     $bridaPath  = public_path('images/logo-brida.png');

    //     if (file_exists($pemkotPath)) {
    //         $logoPemkot = 'data:image/png;base64,' . base64_encode(file_get_contents($pemkotPath));
    //     }

    //     if (file_exists($bridaPath)) {
    //         $logoBrida = 'data:image/png;base64,' . base64_encode(file_get_contents($bridaPath));
    //     }

    //     $pdf = Pdf::loadView('dashboard.daftar_hadir.pdf', [
    //         'kegiatan'   => $kegiatan,
    //         'logoPemkot' => $logoPemkot,
    //         'logoBrida'  => $logoBrida,
    //     ])->setPaper('letter', 'portrait');

    //     return $pdf->download('daftar-hadir-' . Str::slug($kegiatan->nama_kegiatan) . '.pdf');
    // }

    public function exportPdf(SigapDaftarHadirKegiatan $kegiatan)
{
    $user = Auth::user();

    if (!$user->hasAnyRole(['admin', 'verif_daftarhadir'])) {
        abort_unless(
            (int) $kegiatan->created_by === (int) $user->id,
            403,
            'Anda tidak memiliki akses.'
        );
    }

    abort_unless(
        $kegiatan->status === 'selesai',
        403,
        'PDF hanya bisa diexport saat kegiatan selesai.'
    );

    $kegiatan->load([
        'peserta' => fn ($q) =>
            $q->orderBy('urutan_absen')
              ->orderBy('created_at'),
    ]);

    /*
    |--------------------------------------------------------------------------
    | Logo Base64
    |--------------------------------------------------------------------------
    */

    $logoPemkot = null;
    $logoBrida  = null;

    $pemkotPath = base_path('public/images/logo-pemkot.png');
    $bridaPath  = base_path('public/images/logo-brida.png');

    if (file_exists($pemkotPath)) {

        $imageData = base64_encode(
            file_get_contents($pemkotPath)
        );

        $logoPemkot = 'data:image/png;base64,' . $imageData;
    }

    if (file_exists($bridaPath)) {

        $imageData = base64_encode(
            file_get_contents($bridaPath)
        );

        $logoBrida = 'data:image/png;base64,' . $imageData;
    }

    /*
    |--------------------------------------------------------------------------
    | Generate PDF
    |--------------------------------------------------------------------------
    */

    $pdf = Pdf::loadView('dashboard.daftar_hadir.pdf', [
        'kegiatan'   => $kegiatan,
        'logoPemkot' => $logoPemkot,
        'logoBrida'  => $logoBrida,
    ])->setPaper('letter', 'portrait');

    return $pdf->download(
        'daftar-hadir-' .
        str()->slug($kegiatan->nama_kegiatan) .
        '.pdf'
    );
}

    private function saveSignatureBase64(string $data, string $folder): string
    {
        if (!Str::startsWith($data, 'data:image/')) {
            return '';
        }

        [$meta, $content] = explode(',', $data, 2);

        $extension = 'png';
        if (Str::contains($meta, 'image/jpeg')) {
            $extension = 'jpg';
        }

        $binary = base64_decode($content);

        $fileName = Str::uuid() . '.' . $extension;
        $path = trim($folder, '/') . '/' . $fileName;

        Storage::disk('public')->put($path, $binary);

        return $path;
    }

    public function printQr(SigapDaftarHadirKegiatan $kegiatan)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['admin', 'verif_daftarhadir'])) {
            abort_unless(
                (int) $kegiatan->created_by === (int) $user->id,
                403,
                'Anda tidak memiliki akses.'
            );
        }

        $qrUrl = route('sigap-daftar-hadir.public', $kegiatan->uuid);

        $instagramUrl = 'https://www.instagram.com/bridakotamakassar/';

        return view('dashboard.daftar_hadir.print-qr', compact(
            'kegiatan',
            'qrUrl',
            'instagramUrl'
        ));
    }
}