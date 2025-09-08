<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SigapInovasiController extends Controller
{
    public function index()
    {
        return view('dashboard.inovasi.index');
    }

    public function konfigurasi()
    {
        return view('dashboard.inovasi.konfigurasi');
    }

    public function dashboard()
    {
        return view('dashboard.inovasi.dashboard');
    }
}
