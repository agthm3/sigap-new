<?php

namespace App\Http\Controllers;

use App\Models\SigapAgenda;
use App\Models\SigapAgendaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SigapAgendaController extends Controller
{
    public function publicIndex(Request $request)
    {

        return view('SigapAgenda.index');
    }

    public function index()
    {
        $agendas = SigapAgenda::query()
            ->withCount('items')
            ->with(['items' => function ($q) {
                $q->orderByRaw('COALESCE(order_no, 999999), id');
            }])
            ->orderByDesc('date')
            ->get();

        return view('dashboard.agenda.index', compact('agendas'));
    }

    public function create()
    {
        return view('dashboard.agenda.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'unit_title' => 'required|string|max:255',
            'items' => 'required|array|min:1',

            'items.*.description' => 'required|string',
            'items.*.time_text'   => 'required|string',
            'items.*.place'       => 'required|string',

            // izinkan sampai 20 MB (20480 KB)
            'items.*.file'        => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
        ]);

        DB::transaction(function () use ($request) {
            $agenda = SigapAgenda::create([
                'date'       => $request->date,
                'unit_title' => $request->unit_title,
                'is_public'  => $request->has('is_public'),
            ]);

            foreach ($request->input('items', []) as $index => $item) {
                $filePath = null;

                if ($request->hasFile("items.$index.file")) {
                    $uploaded = $request->file("items.$index.file");
                    // proses & kompres di server (PDF/Gambar), lalu simpan ke disk public
                    $filePath = $this->processAndStoreUploaded($uploaded, 'sigap/agenda_files');
                }

                SigapAgendaItem::create([
                    'sigap_agenda_id' => $agenda->id,
                    'order_no'   => $item['order_no'] ?? null,
                    'mode'       => $item['mode'] ?? 'kepala',
                    'assignees'  => $item['assignees'] ?? null,
                    'description'=> $item['description'],
                    'time_text'  => $item['time_text'],
                    'place'      => $item['place'],
                    'file_path'  => $filePath,
                ]);
            }
        });

        return redirect()->route('sigap-agenda.index')->with('success', 'Agenda berhasil disimpan!');
    }

    /** ===================== Upload & Kompresi ===================== */

    /**
     * Proses & kompres file upload sebelum disimpan.
     * - PDF: kompres via Ghostscript/qpdf/mutool/ps2pdf (smart); fallback: simpan original.
     * - JPG/PNG: kompres via GD (re-encode & optional resize).
     * - DOC/DOCX: simpan original.
     *
     * @param \Illuminate\Http\UploadedFile $uploaded
     * @param string $destDir  // relatif disk 'public'
     * @return string|null     // path relatif di disk 'public'
     */
    private function processAndStoreUploaded($uploaded, string $destDir): ?string
    {
        $ext = strtolower($uploaded->getClientOriginalExtension());
        $tmpPath = $uploaded->getRealPath();
        if (!$tmpPath) {
            // simpan langsung jika tidak dapat real path
            return $uploaded->store($destDir, 'public');
        }

        // Pastikan folder tujuan ada
        $destRelPath = $destDir.'/'.uniqid('ag_', true).'.'.($ext ?: 'bin');
        $destAbsPath = storage_path('app/public/'.$destRelPath);
        @mkdir(dirname($destAbsPath), 0775, true);

        // Routing berdasarkan ekstensi
        if (in_array($ext, ['jpg','jpeg','png'])) {
            // Kompres gambar dengan GD
            if ($this->compressImageGD($tmpPath, $destAbsPath, $ext)) {
                return $destRelPath;
            }
            // fallback simpan original
            return $uploaded->store($destDir, 'public');
        }

        if ($ext === 'pdf') {
            // Kompres PDF via beberapa backend (smart); fallback simpan original
            $ok = $this->compressPdfSmart($tmpPath, $destAbsPath);
            if ($ok) return $destRelPath;

            Log::warning('[SIGAP] PDF compressor: all backends failed or not installed. Storing original.');
            return $uploaded->store($destDir, 'public');
        }

        // doc/docx atau lainnya → simpan original
        return $uploaded->store($destDir, 'public');
    }

    /**
     * Kompres gambar pakai GD:
     * - Downscale jika lebih dari maxDim (default 2400px sisi terpanjang)
     * - JPEG quality ~80, PNG compression level ~6 (lossless-ish)
     */
    private function compressImageGD(string $srcPath, string $destAbsPath, string $ext, int $maxDim = 2400): bool
    {
        // load
        if ($ext === 'png') {
            $img = @imagecreatefrompng($srcPath);
        } else { // jpg/jpeg
            $img = @imagecreatefromjpeg($srcPath);
            if (!$img) {
                // kadang PNG diberi ekstensi JPEG: coba deteksi
                $img = @imagecreatefromstring(@file_get_contents($srcPath));
            }
        }
        if (!$img) return false;

        $w = imagesx($img); $h = imagesy($img);

        // resize jika perlu
        $scale = 1.0;
        $longer = max($w, $h);
        if ($longer > $maxDim) $scale = $maxDim / $longer;

        $out = $img;
        if ($scale < 1.0) {
            $nw = (int)floor($w * $scale);
            $nh = (int)floor($h * $scale);
            $res = imagecreatetruecolor($nw, $nh);
            imagealphablending($res, false);
            imagesavealpha($res, true);
            imagecopyresampled($res, $img, 0,0,0,0, $nw,$nh, $w,$h);
            imagedestroy($img);
            $out = $res;
        }

        // simpan
        $ok = false;
        if ($ext === 'png') {
            // compression 0 (none) .. 9 (max). 6 balance
            $ok = imagepng($out, $destAbsPath, 6);
        } else {
            // quality 0..100; 80 cukup baik
            $ok = imagejpeg($out, $destAbsPath, 80);
        }
        imagedestroy($out);
        return (bool)$ok;
    }

    /**
     * Kompres PDF secara "smart":
     * 1) Ghostscript (agresif namun readable)
     * 2) qpdf --stream-data=compress
     * 3) mutool clean -gggg
     * 4) ps2pdf (jika ada)
     * Return true jika output dibuat dan lebih kecil dari input.
     */
// === REPLACE: smart PDF compressor dengan logging detail + ENV override ===
private function compressPdfSmart(string $srcPath, string $destAbsPath): bool
{
    @mkdir(dirname($destAbsPath), 0775, true);

    $srcSize = @filesize($srcPath) ?: PHP_INT_MAX;
    // Hanya kompres kalau > 1MB (hemat waktu; kecil-kecil biasanya sudah optimal)
    if ($srcSize < 1_000_000) {
        \Log::info("[SIGAP] Skip compress (src < 1MB): {$srcSize} bytes");
        return false;
    }

    // DEBUG info: cek PATH & disabled_functions
    $disabled = ini_get('disable_functions');
    \Log::info('[SIGAP] PDF compress: PATH='.(getenv('PATH') ?: '(empty)').' | disabled_functions='.($disabled ?: '(none)'));

    // 1) Ghostscript
    $gs = $this->findGhostscriptBinary();
    if ($gs) {
        // Pengaturan cenderung aman utk dokumen (downsample image, embed font)
        $cmd = escapeshellarg($gs)
             .' -sDEVICE=pdfwrite -dCompatibilityLevel=1.4'
             .' -dPDFSETTINGS=/ebook'
             .' -dDetectDuplicateImages=true'
             .' -dColorImageDownsampleType=/Bicubic -dColorImageResolution=120'
             .' -dGrayImageDownsampleType=/Bicubic  -dGrayImageResolution=120'
             .' -dMonoImageDownsampleType=/Subsample -dMonoImageResolution=300'
             .' -dConvertCMYKImagesToRGB=true'
             .' -dCompressFonts=true -dSubsetFonts=true'
             .' -dAutoRotatePages=/None'
             .' -dNOPAUSE -dQUIET -dBATCH'
             .' -sOutputFile='.escapeshellarg($destAbsPath)
             .' '.escapeshellarg($srcPath);

        \Log::info('[SIGAP] PDF compressor (gs) run: '.$cmd);
        $o = []; $code = null;
        @exec($cmd.' 2>&1', $o, $code);
        if (!empty($o)) \Log::info('[SIGAP] gs stdout: '.implode("\n", $o));
        \Log::info('[SIGAP] gs exit code: '.$code);

        if (is_file($destAbsPath)) {
            $dstSize = @filesize($destAbsPath) ?: 0;
            \Log::info("[SIGAP] gs output size={$dstSize}, src={$srcSize}");
            if ($dstSize > 0 && $dstSize < $srcSize) return true;
            @unlink($destAbsPath);
        }
    } else {
        \Log::warning('[SIGAP] Ghostscript not found. Set GS_BIN di .env jika terpasang di path non-standar.');
    }

    // 2) qpdf
    $qpdf = $this->findBinary(['QPDF_BIN','qpdf']);
    if ($qpdf) {
        $cmd = escapeshellarg($qpdf).' --linearize --stream-data=compress '
             .escapeshellarg($srcPath).' '.escapeshellarg($destAbsPath);
        \Log::info('[SIGAP] PDF compressor (qpdf) run: '.$cmd);
        $o = []; $code = null;
        @exec($cmd.' 2>&1', $o, $code);
        if (!empty($o)) \Log::info('[SIGAP] qpdf stdout: '.implode("\n", $o));
        \Log::info('[SIGAP] qpdf exit code: '.$code);

        if (is_file($destAbsPath)) {
            $dstSize = @filesize($destAbsPath) ?: 0;
            \Log::info("[SIGAP] qpdf output size={$dstSize}, src={$srcSize}");
            if ($dstSize > 0 && $dstSize < $srcSize) return true;
            @unlink($destAbsPath);
        }
    } else {
        \Log::warning('[SIGAP] qpdf not found. Set QPDF_BIN di .env bila tersedia.');
    }

    // 3) mutool (MuPDF)
    $mutool = $this->findBinary(['MUTOOL_BIN','mutool']);
    if ($mutool) {
        $cmd = escapeshellarg($mutool).' clean -gggg '
             .escapeshellarg($srcPath).' '.escapeshellarg($destAbsPath);
        \Log::info('[SIGAP] PDF compressor (mutool) run: '.$cmd);
        $o = []; $code = null;
        @exec($cmd.' 2>&1', $o, $code);
        if (!empty($o)) \Log::info('[SIGAP] mutool stdout: '.implode("\n", $o));
        \Log::info('[SIGAP] mutool exit code: '.$code);

        if (is_file($destAbsPath)) {
            $dstSize = @filesize($destAbsPath) ?: 0;
            \Log::info("[SIGAP] mutool output size={$dstSize}, src={$srcSize}");
            if ($dstSize > 0 && $dstSize < $srcSize) return true;
            @unlink($destAbsPath);
        }
    } else {
        \Log::warning('[SIGAP] mutool not found. Set MUTOOL_BIN di .env bila tersedia.');
    }

    // 4) ps2pdf
    $ps2pdf = $this->findBinary(['PS2PDF_BIN','ps2pdf']);
    if ($ps2pdf) {
        $cmd = escapeshellarg($ps2pdf).' -dPDFSETTINGS=/ebook '
             .escapeshellarg($srcPath).' '.escapeshellarg($destAbsPath);
        \Log::info('[SIGAP] PDF compressor (ps2pdf) run: '.$cmd);
        $o = []; $code = null;
        @exec($cmd.' 2>&1', $o, $code);
        if (!empty($o)) \Log::info('[SIGAP] ps2pdf stdout: '.implode("\n", $o));
        \Log::info('[SIGAP] ps2pdf exit code: '.$code);

        if (is_file($destAbsPath)) {
            $dstSize = @filesize($destAbsPath) ?: 0;
            \Log::info("[SIGAP] ps2pdf output size={$dstSize}, src={$srcSize}");
            if ($dstSize > 0 && $dstSize < $srcSize) return true;
            @unlink($destAbsPath);
        }
    } else {
        \Log::warning('[SIGAP] ps2pdf not found. Set PS2PDF_BIN di .env bila tersedia.');
    }

    \Log::warning('[SIGAP] Semua backend kompresi gagal/absen. Simpan original.');
    return false;
}

// === REPLACE: prefer ENV override utk Ghostscript ===
private function findGhostscriptBinary(): ?string
{
    // Bisa override lewat .env: GS_BIN="C:\Program Files\gs\gs10.04.0\bin\gswin64c.exe" (Windows)
    // atau GS_BIN="/usr/bin/gs" (Linux)
    $env = env('GS_BIN');
    if ($env && is_file($env)) return $env;

    // fallback ke pencarian generik
    return $this->findBinary(['gs','gswin64c','gswin32c']);
}

// === REPLACE: cari biner (dgn dukungan ENV key pertama) ===
// $candidates: boleh berisi nama ENV KEY terlebih dulu, lalu nama biner
private function findBinary(array $candidates): ?string
{
    // Jika elemen pertama adalah nama ENV KEY, coba dulu
    $first = $candidates[0] ?? null;
    if ($first && preg_match('/_BIN$/', $first)) {
        $env = env($first);
        if ($env && is_file($env)) return $env;
        // hapus kandidat ENV key utk pencarian berikutnya
        array_shift($candidates);
    }

    foreach ($candidates as $bin) {
        // POSIX
        $out = null; $ret = null;
        @exec('command -v '.escapeshellarg($bin).' 2>/dev/null', $out, $ret);
        if ($ret === 0 && !empty($out[0])) return trim($out[0]);

        // Windows
        $out = null; $ret = null;
        @exec('where '.escapeshellarg($bin).' 2> NUL', $out, $ret);
        if ($ret === 0 && !empty($out[0])) return trim($out[0]);
    }

    // Tambah beberapa lokasi umum Windows utk Ghostscript (kalau yang dicari gs)
    if (in_array('gs', $candidates, true) || in_array('gswin64c', $candidates, true) || in_array('gswin32c', $candidates, true)) {
        $common = [
            'C:\Program Files\gs\gs10.04.0\bin\gswin64c.exe',
            'C:\Program Files\gs\gs10.00.0\bin\gswin64c.exe',
            'C:\Program Files\gs\gs9.55.0\bin\gswin64c.exe',
            'C:\Program Files\gs\gs9.53.3\bin\gswin64c.exe',
        ];
        foreach ($common as $p) { if (is_file($p)) return $p; }
    }

    return null;
}

    /** ===================== Edit/Update/Show/Delete ===================== */

    public function edit(Request $request)
    {
        $id = $request->query('id');
        abort_if(!$id || !ctype_digit((string)$id), 404);

        $agenda = SigapAgenda::with(['items' => function ($q) {
            $q->orderByRaw('COALESCE(order_no, 999999), id');
        }])->findOrFail($id);

        return view('dashboard.agenda.edit', compact('agenda'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'         => 'required|integer|exists:sigap_agendas,id',
            'date'       => 'required|date',
            'unit_title' => 'required|string|max:255',
            'is_public'  => 'nullable',
            'items'                 => 'required|array|min:1',
            'items.*.id'            => 'nullable|integer|exists:sigap_agenda_items,id',
            'items.*.order_no'      => 'nullable|integer',
            'items.*.mode'          => 'nullable|in:kepala,menugaskan,custom',
            'items.*.assignees'     => 'nullable|string',
            'items.*.description'   => 'required|string',
            'items.*.time_text'     => 'required|string',
            'items.*.place'         => 'required|string',
            // >>> tambahkan validasi file & checkbox hapus
            'items.*.file'          => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
            'items.*.file_delete'   => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($request) {
            $agenda = SigapAgenda::with('items')->findOrFail($request->id);

            // update header agenda
            $agenda->update([
                'date'       => $request->date,
                'unit_title' => $request->unit_title,
                'is_public'  => $request->has('is_public'),
            ]);

            $keepIds = [];

            foreach ($request->items as $index => $item) {
                // existing item?
                if (!empty($item['id'])) {
                    /** @var \App\Models\SigapAgendaItem $it */
                    $it = SigapAgendaItem::where('sigap_agenda_id', $agenda->id)
                        ->where('id', $item['id'])
                        ->firstOrFail();

                    // --- FILE HANDLING (edit) ---
                    // 1) Hapus file jika dicentang "hapus berkas"
                    $deleteRequested = isset($item['file_delete']) && (int)$item['file_delete'] === 1;
                    if ($deleteRequested && !empty($it->file_path)) {
                        $this->deleteFileIfExists($it->file_path);
                        $it->file_path = null;
                    }

                    // 2) Upload baru? (items.$index.file)
                    if ($request->hasFile("items.$index.file")) {
                        $uploaded = $request->file("items.$index.file");

                        // kompres & simpan
                        $newPath = $this->processAndStoreUploaded($uploaded, 'sigap/agenda_files');

                        // hapus file lama jika ada
                        if (!empty($it->file_path)) {
                            $this->deleteFileIfExists($it->file_path);
                        }

                        $it->file_path = $newPath;
                    }

                    // update field lainnya
                    $it->order_no    = $item['order_no'] ?? null;
                    $it->mode        = $item['mode'] ?? 'kepala';
                    $it->assignees   = $item['assignees'] ?? null;
                    $it->description = $item['description'];
                    $it->time_text   = $item['time_text'];
                    $it->place       = $item['place'];
                    $it->save();

                    $keepIds[] = $it->id;
                } else {
                    // --- item baru ---
                    $filePath = null;
                    if ($request->hasFile("items.$index.file")) {
                        $uploaded = $request->file("items.$index.file");
                        $filePath = $this->processAndStoreUploaded($uploaded, 'sigap/agenda_files');
                    }

                    $new = SigapAgendaItem::create([
                        'sigap_agenda_id' => $agenda->id,
                        'order_no'   => $item['order_no'] ?? null,
                        'mode'       => $item['mode'] ?? 'kepala',
                        'assignees'  => $item['assignees'] ?? null,
                        'description'=> $item['description'],
                        'time_text'  => $item['time_text'],
                        'place'      => $item['place'],
                        'file_path'  => $filePath,
                    ]);
                    $keepIds[] = $new->id;
                }
            }

            // --- Hapus item yang dibuang dari form + bersihkan file-nya ---
            $toDelete = $agenda->items()->whereNotIn('id', $keepIds)->get();
            foreach ($toDelete as $row) {
                if (!empty($row->file_path)) {
                    $this->deleteFileIfExists($row->file_path);
                }
                $row->delete();
            }
        });

        return redirect()->route('sigap-agenda.index')->with('success', 'Agenda berhasil diperbarui!');
    }

    public function show(Request $request)
    {
        // dukung id dari route param ataupun query ?id=
        $id = $request->route('id') ?? $request->query('id');
        abort_if(!$id || !ctype_digit((string)$id), 404);

        $agenda = SigapAgenda::with(['items' => function ($q) {
            $q->orderByRaw('COALESCE(order_no, 999999), id');
        }])->findOrFail($id);

        // JSON jika diminta
        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json([
                'id'         => $agenda->id,
                'date'       => $agenda->date,
                'unit_title' => $agenda->unit_title,
                'is_public'  => (bool)$agenda->is_public,
                'items'      => $agenda->items->map(function($it){
                    return [
                        'id'          => $it->id,
                        'order_no'    => $it->order_no,
                        'mode'        => $it->mode,
                        'assignees'   => $it->assignees,
                        'description' => $it->description,
                        'time_text'   => $it->time_text,
                        'place'       => $it->place,
                        'file_url'    => $it->file_path ? asset('storage/'.$it->file_path) : null,
                    ];
                }),
            ]);
        }

        // default: render HTML
        return view('dashboard.agenda.show', compact('agenda'));
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:sigap_agendas,id',
        ]);

        DB::transaction(function () use ($request) {
            $agenda = SigapAgenda::with('items')->findOrFail($request->id);

            // Hapus file setiap item (jika ada)
            foreach ($agenda->items as $it) {
                if (!empty($it->file_path)) {
                    $this->deleteFileIfExists($it->file_path);
                }
            }

            // Hapus file share image (jika pernah dibuat)
            $shareRel = "sigap/agenda/agenda-{$agenda->id}.jpg";
            $this->deleteFileIfExists($shareRel);

            // Hapus record anak & parent
            $agenda->items()->delete();
            $agenda->delete();
        });

        return redirect()
            ->route('sigap-agenda.index')
            ->with('success', 'Agenda beserta berkas terkait berhasil dihapus!');
    }

    private function deleteFileIfExists(?string $relPath): void
    {
        if (!$relPath) return;
        try {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($relPath);
        } catch (\Throwable $e) {
            // optional: log
            \Log::warning('[SIGAP] gagal hapus file: '.$relPath.' | '.$e->getMessage());
        }
    }


    /**
     * SHARE IMAGE (SERVER-SIDE) — Pure PHP GD (tanpa composer)
     * Hasil: 1 gambar panjang berisi SEMUA kegiatan (tinggi dinamis, tidak terpotong)
     */
public function shareImageGD(Request $request)
{
    $request->validate(['id' => 'required|integer|exists:sigap_agendas,id']);

    @ini_set('memory_limit', '512M');
    @ini_set('gd.jpeg_ignore_warning', 1);

    $agenda = \App\Models\SigapAgenda::with(['items' => function ($q) {
        $q->orderByRaw('COALESCE(order_no, 999999), id');
    }])->findOrFail($request->id);

    // ===== Layout base
    $W         = 1080;
    $P_LEFT    = 64;
    $P_RIGHT   = 64;
    $P_TOP     = 64;
    $P_BOTTOM  = 120; // <-- dinaikkan agar footer aman

    $HDR_BAR   = 110;
    $HDR_GAP   = 50;
    $HEADER_H  = $HDR_BAR + $HDR_GAP;
    $NUMBER_GAP = 52;

    // ===== Font & metrik
    $fontBold    = $this->findFont(['Inter-Bold.ttf','Inter-SemiBold.ttf','DejaVuSans-Bold.ttf','arialbd.ttf']);
    $fontRegular = $this->findFont(['Inter-Regular.ttf','DejaVuSans.ttf','arial.ttf']);
    $hasTTF      = is_readable($fontBold) && is_readable($fontRegular);
    $fs28 = 28; $fs22 = 22;
    $LH28 = (int)ceil($fs28 * 1.22);
    $LH22 = (int)ceil($fs22 * 1.25);

    $contentW = $W - $P_LEFT - $P_RIGHT - $NUMBER_GAP;

    // ====== PASS #1: hitung tinggi konten
    $y = $P_TOP + $HEADER_H + 36;

    foreach ($agenda->items as $it) {
        $x = $P_LEFT + $NUMBER_GAP;

        if (!empty($it->assignees)) {
            $badgeText = trim($it->assignees);
            $badgeW = $this->ttfTextWidth($badgeText, $fontBold, 22);
            $badgeW = min($badgeW + 40, $contentW);
            $y += 52;
        }

        $desc = $this->composeDescGD($it);
        $y += $this->wrapHeight($desc, $hasTTF ? $fontRegular : null, $fs28, $contentW, $LH28) + 6;

        $y += 26;
        $y += $this->wrapHeight('Waktu : '.$it->time_text, $hasTTF ? $fontBold : null, $fs22, $contentW, $LH22);

        $y += 26;
        $y += $this->wrapHeight('Tempat : '.$it->place, $hasTTF ? $fontBold : null, $fs22, $contentW, $LH22);

        $y += 20;
        $y += 24;
    }

    // ===== Footer dynamic (ditambah ruangnya)
    $verifyText = 'Agenda telah diverifikasi melalui SIGAP AGENDA';
    $verifyH = $this->wrapHeight($verifyText, $hasTTF ? $fontBold : null, $fs22, $contentW, $LH22);
    $linkH   = $LH22;

    $footerGapTop   = 36;  // <-- lebih besar
    $betweenLines   = 28;  // <-- lebih besar
    $footerPaddingB = 56;  // <-- lebih besar
    $descenderSafe  = 12;  // <-- tambahan kecil agar tidak kepotong pixel terakhir

    $footerBlockH = $footerGapTop + $verifyH + $betweenLines + $linkH + $footerPaddingB + $descenderSafe;

    // Tinggi total
    $H = $y + $footerBlockH + $P_BOTTOM;

    // ====== PASS #2: gambar
    $im = imagecreatetruecolor($W, $H);
    imagesavealpha($im, true);
    $transparent = imagecolorallocatealpha($im, 0,0,0,127);
    imagefill($im, 0, 0, $transparent);

    // background
    $white = imagecolorallocate($im, 255,255,255);
    imagefilledrectangle($im, 0, 0, $W, $H, $white);
    $bgGray = imagecolorallocate($im, 247,247,249);
    imagefilledrectangle($im, 0, 0, $W, $H, $bgGray);

    $maroon   = imagecolorallocate($im, 122, 34, 34);
    $black    = imagecolorallocate($im, 17,17,17);
    $grayDark = imagecolorallocate($im, 68,68,68);
    $gray     = imagecolorallocate($im, 107,114,128);
    $lineCol  = imagecolorallocate($im, 229,231,235);
    $whiteCol = imagecolorallocate($im, 255,255,255);

    // header bar
    imagefilledrectangle($im, $P_LEFT, $P_TOP, $W - $P_RIGHT, $P_TOP + $HDR_BAR, $maroon);

    // header text
    if ($hasTTF) {
        imagettftext($im, 44, 0, $P_LEFT+28, $P_TOP+62,  $whiteCol, $fontBold, 'AGENDA');
        imagettftext($im, 28, 0, $P_LEFT+28, $P_TOP+102, $whiteCol, $fontBold, $agenda->unit_title);
    } else {
        imagestring($im, 5, $P_LEFT+28, $P_TOP+40, 'AGENDA', $whiteCol);
        imagestring($im, 4, $P_LEFT+28, $P_TOP+80, $agenda->unit_title, $whiteCol);
    }

    // tanggal
    $dateStr = \Carbon\Carbon::parse($agenda->date)->locale('id')->translatedFormat('l, d F Y');
    if ($hasTTF) imagettftext($im, 30, 0, $P_LEFT, $P_TOP + $HDR_BAR + 54, $black, $fontBold, $dateStr);
    else imagestring($im, 5, $P_LEFT, $P_TOP + $HDR_BAR + 36, $dateStr, $black);

    // watermark diagonal (gambar lebih dulu, nanti footer diberi background putih)
    $this->gdWatermark($im, $W, $H, 'Diverifikasi melalui SIGAP AGENDA', $fontBold ?: $fontRegular);

    // body
    $y = $P_TOP + $HEADER_H + 36;
    $idx = 1;
    foreach ($agenda->items as $it) {
        if ($hasTTF) imagettftext($im, 28, 0, $P_LEFT, $y+28, $black, $fontBold, $idx.'.');
        else imagestring($im, 5, $P_LEFT, $y+12, $idx.'.', $black);

        $x = $P_LEFT + $NUMBER_GAP;

        if (!empty($it->assignees)) {
            $badgeText = trim($it->assignees);
            $badgeW = $this->ttfTextWidth($badgeText, $fontBold, 22);
            $badgeW = min($badgeW + 40, $contentW);
            $badgeFill= imagecolorallocatealpha($im, 122,34,34, 100);
            imagefilledrectangle($im, $x, $y, $x+$badgeW, $y+40, $badgeFill);
            imagerectangle($im,     $x, $y, $x+$badgeW, $y+40, $maroon);
            if ($hasTTF) {
                $textFitted = $this->gdFit($im, $badgeText, $fontBold, 22, $badgeW-16);
                imagettftext($im, 22, 0, $x+14, $y+26, $maroon, $fontBold, $textFitted);
            } else {
                imagestring($im, 4, $x+14, $y+12, $badgeText, $maroon);
            }
            $y += 52;
        }

        $desc = $this->composeDescGD($it);
        if ($hasTTF) {
            $y = $this->gdDrawWrap($im, $desc, $fontRegular, $fs28, $black, $x, $y+34, $contentW, $LH28) + 6;
        } else {
            $y = $this->gdDrawWrapBuiltin($im, $desc, 4, $black, $x, $y+20, $contentW, $LH28) + 6;
        }

        if ($hasTTF) {
            $y = $this->gdDrawWrap($im, 'Waktu : '.$it->time_text, $fontBold, $fs22, $grayDark, $x, $y+26, $contentW, $LH22);
            $y = $this->gdDrawWrap($im, 'Tempat : '.$it->place,    $fontBold, $fs22, $grayDark, $x, $y+26, $contentW, $LH22);
        } else {
            $y = $this->gdDrawWrapBuiltin($im, 'Waktu : '.$it->time_text, 3, $grayDark, $x, $y+18, $contentW, $LH22);
            $y = $this->gdDrawWrapBuiltin($im, 'Tempat : '.$it->place,    3, $grayDark, $x, $y+18, $contentW, $LH22);
        }

        $y += 20;
        imageline($im, $P_LEFT, $y, $W - $P_RIGHT, $y, $lineCol);
        $y += 24;

        $idx++;
    }

    // ===== Footer BG putih (agar tak ketimpa watermark) + teks
    $footerTop = $y + $footerGapTop;
    $footerBottom = $footerTop + ($verifyH + $betweenLines + $linkH + $footerPaddingB + $descenderSafe);
    $footerBG = imagecolorallocatealpha($im, 255,255,255, 0);
    imagefilledrectangle($im, $P_LEFT, $footerTop - 18, $W - $P_RIGHT, $footerBottom, $footerBG);

    $link = rtrim(config('app.url'), '/').'/agenda/'.$agenda->id;

    if ($hasTTF) {
        $yFooter = $this->gdDrawWrap($im, $verifyText, $fontBold, $fs22, $maroon, $P_LEFT, $footerTop + $LH22, $contentW, $LH22);
        $yFooter = $this->gdDrawWrap($im, $link,       $fontRegular, $fs22, $gray,   $P_LEFT, $yFooter + $betweenLines, $contentW, $LH22);
    } else {
        $yFooter = $this->gdDrawWrapBuiltin($im, $verifyText, 3, $maroon, $P_LEFT, $footerTop + ($LH22 - 22), $contentW, $LH22);
        $yFooter = $this->gdDrawWrapBuiltin($im, $link,       3, $gray,   $P_LEFT, $yFooter + $betweenLines - 8, $contentW, $LH22);
    }

    // ===== Simpan: nama unik + hapus lama
    $dirRel = "sigap/agenda";
    $dirAbs = storage_path('app/public/'.$dirRel);
    @mkdir($dirAbs, 0775, true);
    foreach (glob($dirAbs."/agenda-{$agenda->id}-*.jpg") ?: [] as $old) { @unlink($old); }

    $ts = time();
    $relPath = "{$dirRel}/agenda-{$agenda->id}-{$ts}.jpg";
    $abs     = $dirAbs."/agenda-{$agenda->id}-{$ts}.jpg";
    imagejpeg($im, $abs, 90);
    imagedestroy($im);

    $text = "AGENDA ".mb_strtoupper($agenda->unit_title)."\n{$dateStr}\n{$link}";

    return response()->json([
        'ok'        => true,
        'image_url' => asset('storage/'.$relPath).'?t='.$ts,
        'text'      => $text,
    ]);
}



    /* ===================== Helpers (GD) ===================== */

    private function findFont(array $candidates): ?string
    {
        $places = [
            public_path('fonts'), resource_path('fonts'), base_path(),
            '/usr/share/fonts/truetype/dejavu', '/usr/share/fonts/truetype',
            'C:\Windows\Fonts'
        ];
        foreach ($places as $dir) {
            foreach ($candidates as $name) {
                $p = rtrim($dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$name;
                if (is_readable($p)) return $p;
            }
        }
        return null;
    }

    private function composeDescGD($it): string
    {
        $mode = $it->mode ?? 'kepala';
        if ($mode === 'kepala') return 'Kepala Brida, '.$it->description;
        if ($mode === 'menugaskan') {
            $who = trim((string)$it->assignees);
            return 'Menugaskan '.($who ? ($who.', ') : '').$it->description;
        }
        return (string)$it->description;
    }

    private function ttfTextWidth(string $text, ?string $font, int $size): int
    {
        if (!$font || !is_readable($font)) return (int)(mb_strlen($text) * 8); // fallback kasar
        $box = imagettfbbox($size, 0, $font, $text);
        return abs($box[2]-$box[0]);
    }

    private function wrapHeight(string $text, ?string $font, int $fontSize, int $maxW, int $lineH): int
    {
        if (!$font || !is_readable($font)) {
            $charsPerLine = max(1, floor($maxW / 8)); // fallback tanpa TTF
            $lines = (int)ceil(mb_strlen($text) / $charsPerLine);
            return max($lineH, $lines * $lineH);
        }
        $words = preg_split('/\s+/', trim($text));
        if (!$words) return $lineH;
        $line = ''; $h=0;
        foreach ($words as $w) {
            $try = $line ? "$line $w" : $w;
            $box = imagettfbbox($fontSize, 0, $font, $try);
            $wpx = abs($box[2]-$box[0]);
            if ($wpx <= $maxW) { $line = $try; }
            else { $h += $lineH; $line = $w; }
        }
        $h += $lineH;
        return $h;
    }

    private function gdDrawWrap($im, string $text, string $font, int $size, $color, int $x, int $y, int $maxW, int $lineH): int
    {
        $words = preg_split('/\s+/', trim($text));
        $line = '';
        foreach ($words as $w) {
            $try = $line ? "$line $w" : $w;
            $box = imagettfbbox($size, 0, $font, $try);
            $wpx = abs($box[2]-$box[0]);
            if ($wpx <= $maxW) {
                $line = $try;
            } else {
                imagettftext($im, $size, 0, $x, $y, $color, $font, $line);
                $line = $w;
                $y += $lineH;
            }
        }
        if ($line !== '') imagettftext($im, $size, 0, $x, $y, $color, $font, $line);
        return $y + $lineH;
    }

    private function gdDrawWrapBuiltin($im, string $text, int $font, $color, int $x, int $y, int $maxW, int $lineH): int
    {
        $charW = 8; // approx
        $maxChars = max(1, floor($maxW / $charW));
        $chunks = str_split($text, $maxChars);
        foreach ($chunks as $line) {
            imagestring($im, $font, $x, $y-8, $line, $color);
            $y += $lineH;
        }
        return $y;
    }

    private function gdFit($im, string $text, string $font, int $size, int $maxW): string
    {
        $out = trim($text);
        while (mb_strlen($out) > 3) {
            $box = imagettfbbox($size, 0, $font, $out);
            $w   = abs($box[2]-$box[0]);
            if ($w <= $maxW) break;
            $out = rtrim(mb_substr($out, 0, -2));
        }
        if ($out !== trim($text)) $out .= '…';
        return $out;
    }

    private function gdWatermark($im, int $W, int $H, string $text, ?string $font): void
    {
        if (!$font || !is_readable($font)) return;
        $layer = imagecreatetruecolor($W, $H);
        imagesavealpha($layer, true);
        $clear = imagecolorallocatealpha($layer, 0,0,0,127);
        imagefill($layer, 0, 0, $clear);
        $wmCol = imagecolorallocatealpha($layer, 0,0,0,110); // ~0.43 alpha
        imagettftext($layer, 90, 0, intval($W/2 - 600), intval($H/2 + 30), $wmCol, $font, $text);
        $rot = imagerotate($layer, 36, $clear);
        imagecopy($im, $rot, intval(($W - imagesx($rot)) / 2), intval(($H - imagesy($rot))/2), 0,0, imagesx($rot), imagesy($rot));
        imagedestroy($layer);
        imagedestroy($rot);
    }
}
