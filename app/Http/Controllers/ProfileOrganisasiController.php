<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileOrganisasiController extends Controller
{
    public function struktur()
    {
        return view('profilOrganisasi.struktur');
    }

    public function visiMisi()
    {
        return view('profilOrganisasi.visimisi');
    }

    public function berita()
    {
        return view('profilOrganisasi.berita');
    }

    public function tentang()
    {
        return view('profilOrganisasi.tentang');
    }

    public function kontak()
    {
        return view('profilOrganisasi.kontak');
    }
}
