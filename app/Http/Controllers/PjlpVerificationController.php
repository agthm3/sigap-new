<?php

namespace App\Http\Controllers;

use App\Models\PjlpPeriode;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PjlpVerificationController extends Controller
{
    /**
     * Halaman Verifikasi Dokumen & Keabsahan Laporan PJLP via QR Code
     */
    public function verify(Request $request, $id)
    {
        $hash = $request->query('hash');

        // Ambil data periode beserta relasi user, profile, dan logbooks
        $periode = PjlpPeriode::with(['user.profile', 'logbooks'])->find($id);

        // Validasi Hash Token Keabsahan Dokumen
        $isValid = false;
        if ($periode) {
            $expectedHash = md5($periode->id . $periode->user_id . $periode->bulan_tahun);
            if ($hash === $expectedHash) {
                $isValid = true;
            }
        }

        if (!$isValid || !$periode) {
            return view('dashboard.pjlp.verify', [
                'isValid' => false,
                'periode' => null,
                'user' => null,
                'profile' => null,
            ]);
        }

        $user = $periode->user;
        $profile = $user->profile;
        $logbooks = $periode->logbooks;

        // Statistik Capaian Logbook
        $totalHariKerja = $logbooks->count();
        $totalTerisi = $logbooks->whereNotIn('status', ['belum_diisi'])->count();
        $totalDisetujui = $logbooks->where('status', 'terverifikasi')->count();
        $hasDaftarGaji = !empty($periode->file_daftar_gaji);

        $namaBulanTahun = Carbon::createFromFormat('Y-m', $periode->bulan_tahun)->translatedFormat('F Y');

        return view('dashboard.pjlp.verify', compact(
            'isValid',
            'periode',
            'user',
            'profile',
            'totalHariKerja',
            'totalTerisi',
            'totalDisetujui',
            'hasDaftarGaji',
            'namaBulanTahun'
        ));
    }
}