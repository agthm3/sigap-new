<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SigapPegawaiController extends Controller
{
    public function index()
    {
        // Logic to retrieve and display the list of employees
        return view('dashboard.pegawai.index');
    }
}
