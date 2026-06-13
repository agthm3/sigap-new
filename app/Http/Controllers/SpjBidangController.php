<?php

namespace App\Http\Controllers;

use App\Models\SpjBidang;
use Illuminate\Http\Request;

class SpjBidangController extends Controller
{
    public function index()
    {
        $bidangs = SpjBidang::withCount('subKegiatans')->latest()->paginate(10);
        return view('dashboard.spj.bidang.index', compact('bidangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_bidang' => 'required|string|max:255|unique:spj_bidangs,nama_bidang'
        ]);

        SpjBidang::create([
            'nama_bidang' => $request->nama_bidang
        ]);

        return back()->with('success', 'Bidang berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_bidang' => 'required|string|max:255|unique:spj_bidangs,nama_bidang,' . $id
        ]);

        $bidang = SpjBidang::findOrFail($id);
        $bidang->update([
            'nama_bidang' => $request->nama_bidang
        ]);

        return back()->with('success', 'Bidang berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $bidang = SpjBidang::findOrFail($id);
        $bidang->delete();

        return back()->with('success', 'Bidang berhasil dihapus!');
    }


    public function indexSub($bidang_id)
    {
        $bidang = SpjBidang::with(['subKegiatans' => function($q) {
            $q->withCount('kegiatans')->latest();
        }])->findOrFail($bidang_id);

        return view('dashboard.spj.bidang.sub_index', compact('bidang'));
    }

    public function storeSub(Request $request, $bidang_id)
    {
        $request->validate([
            'nama_sub_kegiatan' => 'required|string|max:255'
        ]);

        $bidang = SpjBidang::findOrFail($bidang_id);
        
        $bidang->subKegiatans()->create([
            'nama_sub_kegiatan' => $request->nama_sub_kegiatan
        ]);

        return back()->with('success', 'Sub-Kegiatan berhasil ditambahkan!');
    }

    public function destroySub($id)
    {
        $sub = \App\Models\SpjSubKegiatan::findOrFail($id);
        $sub->delete();

        return back()->with('success', 'Sub-Kegiatan berhasil dihapus!');
    }

    public function indexKegiatan($sub_id)
    {
        // Ambil data Sub-Kegiatan beserta Bidang induknya, lalu muat daftar Kegiatan dan hitung jumlah Gelombang di tiap Kegiatan
        $subKegiatan = \App\Models\SpjSubKegiatan::with(['bidang', 'kegiatans' => function($q) {
            $q->withCount('gelombangs')->latest();
        }])->findOrFail($sub_id);

        return view('dashboard.spj.bidang.kegiatan_index', compact('subKegiatan'));
    }

    public function storeKegiatan(Request $request, $sub_id)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255'
        ]);

        $subKegiatan = \App\Models\SpjSubKegiatan::findOrFail($sub_id);
        
        $subKegiatan->kegiatans()->create([
            'nama_kegiatan' => $request->nama_kegiatan
        ]);

        return back()->with('success', 'Kegiatan berhasil ditambahkan!');
    }

    public function destroyKegiatan($id)
    {
        $kegiatan = \App\Models\SpjKegiatan::findOrFail($id);
        $kegiatan->delete();

        return back()->with('success', 'Kegiatan berhasil dihapus!');
    }

    public function indexGelombang($kegiatan_id)
    {
        // Ambil data Kegiatan beserta Sub-Kegiatan dan Bidang (untuk breadcrumbs), lalu muat daftar Gelombang
        $kegiatan = \App\Models\SpjKegiatan::with(['subKegiatan.bidang', 'gelombangs' => function($q) {
            $q->latest();
        }])->findOrFail($kegiatan_id);

        return view('dashboard.spj.bidang.gelombang_index', compact('kegiatan'));
    }

    public function storeGelombang(Request $request, $kegiatan_id)
    {
        $request->validate([
            'nama_gelombang' => 'required|string|max:255',
            'tanggal'        => 'required|date',
            'waktu'          => 'required|string|max:255',
            'tempat'         => 'required|string|max:255',
        ]);

        $kegiatan = \App\Models\SpjKegiatan::findOrFail($kegiatan_id);
        
        $kegiatan->gelombangs()->create([
            'nama_gelombang' => $request->nama_gelombang,
            'tanggal'        => $request->tanggal,
            'waktu'          => $request->waktu,
            'tempat'         => $request->tempat,
        ]);

        return back()->with('success', 'Gelombang / Angkatan berhasil ditambahkan!');
    }

    public function destroyGelombang($id)
    {
        $gelombang = \App\Models\SpjGelombang::findOrFail($id);
        $gelombang->delete();

        return back()->with('success', 'Gelombang / Angkatan berhasil dihapus!');
    }
}