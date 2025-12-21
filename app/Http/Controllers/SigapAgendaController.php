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
            ->select('id','date','unit_title','is_public')
            ->with([
                'items' => function ($q) {
                    $q->orderByRaw('COALESCE(order_no, 999999), id');
                }
            ])
            ->withCount('items')
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
        if ($it->mode !== 'menugaskan') {
            return $it->mode === 'kepala'
                ? 'Kepala Brida, '.$it->description
                : $it->description;
        }

        $lines = [];

        try {
            $data = json_decode($it->assignees, true);
            foreach ($data['users'] ?? [] as $u) {
                $lines[] = '• '.$u['name'];
            }
            foreach ($data['manual'] ?? [] as $m) {
                $lines[] = '• '.$m;
            }
        } catch (\Throwable $e) {
            if ($it->assignees) $lines[] = '• '.$it->assignees;
        }

        return "Menugaskan:\n".implode("\n", $lines)."\n\n".$it->description;
    }

    private function assigneeBadgeText($it): ?string
    {
        if (empty($it->assignees)) return null;

        try {
            $data = json_decode($it->assignees, true);
            if (!is_array($data)) return null;

            $names = [];

            foreach ($data['users'] ?? [] as $u) {
                if (!empty($u['name'])) $names[] = $u['name'];
            }
            foreach ($data['manual'] ?? [] as $m) {
                if (!empty($m)) $names[] = $m;
            }

            if (empty($names)) return null;

            // Badge cukup ringkas
            if (count($names) === 1) {
                return $names[0];
            }

            if (count($names) === 2) {
                return $names[0].' & '.$names[1];
            }

            return $names[0].' +'.(count($names) - 1);
        } catch (\Throwable $e) {
            return null;
        }
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
