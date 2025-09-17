<?php

namespace App\Http\Controllers\page;

use App\Http\Controllers\Controller;
use App\Repositories\DocumentRepository;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    public function index()
    {
        return view('SigapDokumen.home.index');
    }

  public function show(Request $request)
    {
        // Ambil filter dari request
        $filters = $request->only(['q', 'category', 'stakeholder', 'year', 'sort']);

        // Paksa hanya dokumen public di halaman umum
        $filters['sensitivity'] = 'public';

        $perPage = 10;
        $documents = $this->documentRepository->paginate($filters, $perPage);

        return view('SigapDokumen.home.show', compact('documents'));
    }


    public function indexPegawai()
    {
        return view('SigapPegawai.index');
    }
}
