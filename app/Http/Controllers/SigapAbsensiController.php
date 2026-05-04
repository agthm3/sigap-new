<?php

namespace App\Http\Controllers;

use App\Models\SigapAbsensi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class SigapAbsensiController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $myTodayAbsensi = SigapAbsensi::where('user_id', Auth::id())
            ->whereDate('absen_date', $today)
            ->first();

        return view('dashboard.absensi.index', compact('myTodayAbsensi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'photo_base64'  => ['required', 'string'],
            'latitude'      => ['required', 'numeric'],
            'longitude'     => ['required', 'numeric'],
        ]);

        $today = now()->toDateString();

        $already = SigapAbsensi::where('user_id', Auth::id())
            ->whereDate('absen_date', $today)
            ->exists();

        if ($already) {
            return back()->with('error', 'Anda sudah melakukan absensi hari ini.');
        }

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;

        $distance = $this->distanceMeters(
            $lat,
            $lng,
            config('sigap_absensi.center_lat'),
            config('sigap_absensi.center_lng')
        );

        $isOutsideRadius = $distance > config('sigap_absensi.radius_meter');

        $now = now();
        $cutoff = Carbon::createFromFormat('H:i:s', config('sigap_absensi.cutoff_time'));
        $currentTime = Carbon::createFromFormat('H:i:s', $now->format('H:i:s'));

        $lateMinutes = $currentTime->gt($cutoff) ? $cutoff->diffInMinutes($currentTime) : 0;
        $keterangan = $lateMinutes > 0 ? 'TERLAMBAT' : 'HADIR';

        $photoPath = $this->saveWatermarkedPhoto(
            $request->photo_base64,
            Auth::user()->name ?? 'PEGAWAI',
            config('sigap_absensi.location_label'),
            $isOutsideRadius ? 'DI LUAR RADIUS BALAIKOTA MAKASSAR' : 'DALAM RADIUS BALAIKOTA MAKASSAR',
            $lat,
            $lng
        );

        SigapAbsensi::create([
            'user_id'           => Auth::id(),
            'absen_date'        => $today,
            'absen_time'        => $now->format('H:i:s'),
            'latitude'          => $lat,
            'longitude'         => $lng,
            'distance_meter'    => round($distance, 2),
            'is_outside_radius' => $isOutsideRadius,
            'location_text'     => config('sigap_absensi.location_label'),
            'photo_path'        => $photoPath,
            'keterangan'        => $keterangan,
            'late_minutes'      => $lateMinutes,
        ]);

        $message = $isOutsideRadius
            ? 'Absensi berhasil disimpan, tetapi Anda berada di luar radius Balaikota Makassar.'
            : 'Absensi berhasil disimpan.';

        return redirect()
            ->route('sigap-absensi.index')
            ->with('success', $message);
    }

    public function dashboard()
    {
        $today = now()->toDateString();

        $todayRecords = SigapAbsensi::with('user')
            ->whereDate('absen_date', $today)
            ->latest('absen_time')
            ->get();

        $totalToday = $todayRecords->count();
        $totalLate = $todayRecords->where('late_minutes', '>', 0)->count();

        $weeklyStart = now()->startOfWeek();
        $weeklyEnd   = now()->endOfWeek();
        $weeklyTotal = SigapAbsensi::whereBetween('absen_date', [$weeklyStart, $weeklyEnd])->count();

        $monthlyStart = now()->startOfMonth();
        $monthlyEnd   = now()->endOfMonth();
        $monthlyTotal = SigapAbsensi::whereBetween('absen_date', [$monthlyStart, $monthlyEnd])->count();

        return view('dashboard.absensi.dashboard', compact(
            'todayRecords',
            'totalToday',
            'totalLate',
            'weeklyStart',
            'weeklyEnd',
            'weeklyTotal',
            'monthlyTotal'
        ));
    }

    // public function rekapHarian()
    // {
    //     $rekap = SigapAbsensi::selectRaw('absen_date, COUNT(*) as total, SUM(CASE WHEN late_minutes > 0 THEN 1 ELSE 0 END) as terlambat')
    //         ->groupBy('absen_date')
    //         ->orderByDesc('absen_date')
    //         ->paginate(15);

    //     return view('dashboard.absensi.rekap-harian', compact('rekap'));
    // }

    public function rekapMingguan()
    {
        $rekap = SigapAbsensi::selectRaw("
                YEARWEEK(absen_date, 1) as week_key,
                MIN(absen_date) as start_date,
                MAX(absen_date) as end_date,
                COUNT(*) as total,
                SUM(CASE WHEN late_minutes > 0 THEN 1 ELSE 0 END) as terlambat
            ")
            ->groupBy('week_key')
            ->orderByDesc('week_key')
            ->paginate(12);

        return view('dashboard.absensi.rekap-mingguan', compact('rekap'));
    }

    public function rekapBulanan()
    {
        $rekap = SigapAbsensi::selectRaw("
                YEAR(absen_date) as tahun,
                MONTH(absen_date) as bulan,
                COUNT(*) as total,
                SUM(CASE WHEN late_minutes > 0 THEN 1 ELSE 0 END) as terlambat
            ")
            ->groupByRaw('YEAR(absen_date), MONTH(absen_date)')
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->paginate(12);

        return view('dashboard.absensi.rekap-bulanan', compact('rekap'));
    }

    public function edit(SigapAbsensi $absensi)
    {
        return view('dashboard.absensi.edit', compact('absensi'));
    }

    public function update(Request $request, SigapAbsensi $absensi)
    {
        $request->validate([
            'absen_date'        => ['required', 'date_format:Y-m-d'],
            'absen_time'        => ['required', 'date_format:H:i'],
            'keterangan'        => ['required', 'string', 'max:50'],
            'location_text'     => ['required', 'string', 'max:255'],
            'latitude'          => ['nullable', 'numeric'],
            'longitude'         => ['nullable', 'numeric'],
            'distance_meter'    => ['nullable', 'numeric'],
            'is_outside_radius' => ['nullable'],
        ]);

        $time = Carbon::createFromFormat('H:i', $request->absen_time);
        $cutoff = Carbon::createFromFormat('H:i:s', config('sigap_absensi.cutoff_time'));

        $lateMinutes = $time->gt($cutoff) ? $cutoff->diffInMinutes($time) : 0;

        $absensi->update([
            'absen_date'        => $request->absen_date,
            'absen_time'        => $time->format('H:i:s'),
            'keterangan'        => $request->keterangan,
            'location_text'     => $request->location_text,
            'latitude'          => $request->latitude,
            'longitude'         => $request->longitude,
            'distance_meter'    => $request->distance_meter,
            'is_outside_radius' => $request->boolean('is_outside_radius'),
            'late_minutes'      => $lateMinutes,
        ]);

        return redirect()
            ->route('sigap-absensi.dashboard')
            ->with('success', 'Data absensi berhasil diubah.');
    }

    protected function distanceMeters(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    protected function saveWatermarkedPhoto(
        string $photoBase64,
        string $name,
        string $locationLabel,
        string $radiusStatus,
        ?float $lat = null,
        ?float $lng = null
    ): string {
        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $photoBase64);
        $binary = base64_decode($base64);

        if ($binary === false) {
            abort(422, 'Foto tidak valid.');
        }

        $image = imagecreatefromstring($binary);

        if (!$image) {
            abort(422, 'Gagal membaca gambar.');
        }

        $width  = imagesx($image);
        $height = imagesy($image);

        $maroon = imagecolorallocate($image, 122, 34, 34);
        $white  = imagecolorallocate($image, 255, 255, 255);
        $overlay = imagecolorallocatealpha($image, 122, 34, 34, 72);

        imagefilledrectangle($image, 0, 0, $width, $height, $overlay);
        imagerectangle($image, 10, 10, $width - 11, $height - 11, $maroon);
        imagerectangle($image, 18, 18, $width - 19, $height - 19, $maroon);

        $footerHeight = 118;
        imagefilledrectangle($image, 0, $height - $footerHeight, $width, $height, $overlay);

        imagestring($image, 5, 28, 28, 'SIGAP ABSENSI', $white);
        imagestring($image, 4, 28, $height - 98, 'Nama: ' . $name, $white);
        imagestring($image, 4, 28, $height - 78, 'Lokasi: ' . $locationLabel, $white);
        imagestring($image, 4, 28, $height - 58, 'Status: ' . $radiusStatus, $white);
        imagestring($image, 3, 28, $height - 38, 'Waktu: ' . now()->format('d-m-Y H:i:s'), $white);
        imagestring($image, 3, 28, $height - 20, 'GPS: ' . ($lat ?? '-') . ', ' . ($lng ?? '-'), $white);

        $label = 'HADIR';
        $labelWidth = imagefontwidth(5) * strlen($label) + 24;
        imagefilledrectangle($image, $width - $labelWidth - 28, 28, $width - 28, 64, $maroon);
        imagestring($image, 5, $width - $labelWidth, 38, $label, $white);

        ob_start();
        imagejpeg($image, null, 90);
        $jpeg = ob_get_clean();

        imagedestroy($image);

        $fileName = 'absensi/' . now()->format('Y/m/') . Str::uuid() . '.jpg';
        Storage::disk('public')->put($fileName, $jpeg);

        return $fileName;
    }

    public function rekapHarian(Request $request)
    {
        $tanggal = $request->tanggal ?: now()->toDateString();

        $rekap = SigapAbsensi::with('user')
            ->whereDate('absen_date', $tanggal)
            ->orderBy('absen_time')
            ->get();

        return view('dashboard.absensi.rekap-harian', compact('rekap', 'tanggal'));
    }

    public function exportRekapHarianPdf(Request $request)
    {
        $tanggal = $request->tanggal ?: now()->toDateString();

        $rekap = SigapAbsensi::with('user')
            ->whereDate('absen_date', $tanggal)
            ->orderBy('absen_time')
            ->get()
            ->map(function ($row) {
                $photoPath = storage_path('app/public/' . $row->photo_path);

                $row->photo_base64 = (file_exists($photoPath))
                    ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($photoPath))
                    : null;

                return $row;
            });

        $rowsHtml = '';
        foreach ($rekap as $i => $row) {
            $nama = e($row->user->name ?? '-');
            $nip = e($row->user->nip ?? '-');
            $statusRadius = $row->is_outside_radius ? 'DI LUAR RADIUS' : 'DALAM RADIUS';
            $absensi = e($row->keterangan ?? 'HADIR');
            $koordinat = e(($row->latitude ?? '-') . ', ' . ($row->longitude ?? '-'));
            $waktu = e(\Carbon\Carbon::parse($row->absen_time)->format('H:i'));

            $fotoHtml = !empty($row->photo_base64)
                ? '<img src="'.$row->photo_base64.'" style="width:85px;height:85px;object-fit:cover;border-radius:8px;border:1px solid #d1d5db;">'
                : '<div style="width:85px;height:85px;border:1px solid #d1d5db;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:9px;color:#6b7280;">No Photo</div>';

            $rowsHtml .= '
                <tr>
                    <td>'.($i + 1).'</td>
                    <td>'.$nama.'</td>
                    <td>'.$nip.'</td>
                    <td><span class="badge '.($row->is_outside_radius ? 'badge-red' : 'badge-green').'">'.$statusRadius.'</span></td>
                    <td>'.$koordinat.'</td>
                    <td><strong>'.$absensi.'</strong><br><span class="small">'.$waktu.'</span></td>
                    <td><div class="small">Absen Terverifikasi oleh SIGAP ABSENSI</div></td>
                    <td>'.$fotoHtml.'</td>
                </tr>
            ';
        }

        $tanggalTampil = \Carbon\Carbon::parse($tanggal)->format('d F Y');

        $html = '
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <style>
                body {
                    font-family: DejaVu Sans, sans-serif;
                    font-size: 10px;
                    color: #1f2937;
                }
                .header {
                    text-align: center;
                    margin-bottom: 14px;
                }
                .title {
                    font-size: 16px;
                    font-weight: bold;
                    color: #7a2222;
                    margin-bottom: 4px;
                }
                .subtitle {
                    font-size: 11px;
                    color: #6b7280;
                }
                .meta {
                    margin: 10px 0 14px;
                    font-size: 10px;
                    color: #374151;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                th {
                    background: #f3f4f6;
                    color: #4b5563;
                    font-size: 9px;
                    text-transform: uppercase;
                    padding: 7px 6px;
                    border: 1px solid #d1d5db;
                    text-align: left;
                }
                td {
                    border: 1px solid #d1d5db;
                    padding: 6px;
                    vertical-align: top;
                }
                .badge {
                    display: inline-block;
                    padding: 2px 6px;
                    border-radius: 9999px;
                    font-size: 9px;
                    border: 1px solid #d1d5db;
                }
                .badge-green {
                    background: #ecfdf5;
                    color: #047857;
                    border-color: #a7f3d0;
                }
                .badge-red {
                    background: #fef2f2;
                    color: #b91c1c;
                    border-color: #fecaca;
                }
                .small {
                    font-size: 8px;
                    color: #6b7280;
                    line-height: 1.2;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="title">REKAP HARIAN ABSENSI SIGAP</div>
                <div class="subtitle">Badan Riset dan Inovasi Daerah Kota Makassar</div>
            </div>

            <div class="meta">
                Tanggal: '.$tanggalTampil.'
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 22px;">No</th>
                        <th>Nama</th>
                        <th>NIP</th>
                        <th style="width: 85px;">Status</th>
                        <th style="width: 110px;">Koordinat</th>
                        <th style="width: 85px;">Absensi</th>
                        <th>Keterangan</th>
                        <th style="width: 95px;">Foto</th>
                    </tr>
                </thead>
                <tbody>
                    '.($rowsHtml ?: '<tr><td colspan="8" style="text-align:center;padding:14px;">Belum ada data absensi.</td></tr>').'
                </tbody>
            </table>
        </body>
        </html>';

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');

        return $pdf->download('rekap-harian-absensi-' . $tanggal . '.pdf');
    }
}