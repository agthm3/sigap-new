<?php

namespace App\Http\Controllers\page;

use App\Http\Controllers\Controller;
use App\Models\SiipidPrestasi;
use Illuminate\Http\Request;

class SiipidPublicController extends Controller
{
    /**
     * Halaman Publik: Galeri Direktori Database Prestasi Inovator Kota Makassar
     */
    public function index(Request $request)
    {
        $query = SiipidPrestasi::with(['user', 'innovable'])
            ->where(function ($q) {
                $q->where('is_published_public', true)
                  ->orWhereIn('status', ['direkomendasikan', 'ditetapkan_sk']);
            });

        // Filter Pencarian (Nama Inovator, Nama Prestasi, Ajang)
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_inovator', 'like', "%{$search}%")
                  ->where('nama_prestasi', 'like', "%{$search}%")
                  ->orWhere('ajang_kompetisi', 'like', "%{$search}%")
                  ->orWhere('opd_instansi', 'like', "%{$search}%");
            });
        }

        // Filter Tingkat Prestasi
        if ($tingkat = $request->input('tingkat')) {
            $query->where('tingkat_prestasi', $tingkat);
        }

        // Filter Kategori Sasaran (ASN / Perangkat Daerah / Masyarakat)
        if ($sasaran = $request->input('sasaran')) {
            $query->where('kategori_sasaran', $sasaran);
        }

        // Filter Tahun
        if ($tahun = $request->input('tahun')) {
            $query->where('tahun_perolehan', $tahun);
        }

        $prestasis = $query->latest('tahun_perolehan')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        // Statistik Ringkas
        $totalPrestasi = SiipidPrestasi::where(function ($q) {
            $q->where('is_published_public', true)
              ->orWhereIn('status', ['direkomendasikan', 'ditetapkan_sk']);
        })->count();

        $totalAsn = SiipidPrestasi::whereIn('kategori_sasaran', ['asn', 'perangkat_daerah'])
            ->where(function ($q) {
                $q->where('is_published_public', true)
                  ->orWhereIn('status', ['direkomendasikan', 'ditetapkan_sk']);
            })->count();

        $totalMasyarakat = SiipidPrestasi::where('kategori_sasaran', 'masyarakat')
            ->where(function ($q) {
                $q->where('is_published_public', true)
                  ->orWhereIn('status', ['direkomendasikan', 'ditetapkan_sk']);
            })->count();

        $tahunList = SiipidPrestasi::select('tahun_perolehan')
            ->distinct()
            ->orderByDesc('tahun_perolehan')
            ->pluck('tahun_perolehan');

        return view('SigapSiipid.index', compact(
            'prestasis',
            'totalPrestasi',
            'totalAsn',
            'totalMasyarakat',
            'tahunList'
        ));
    }

    /**
     * Halaman Publik: Detail Capaian Prestasi Inovator
     */
    public function show($uuid)
    {
        $prestasi = SiipidPrestasi::with(['user', 'innovable'])
            ->where('uuid', $uuid)
            ->where(function ($q) {
                $q->where('is_published_public', true)
                  ->orWhereIn('status', ['direkomendasikan', 'ditetapkan_sk']);
            })
            ->firstOrFail();

        return view('SigapSiipid.show', compact('prestasi'));
    }
}
