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
        $filters = $request->only(['q', 'category', 'stakeholder','sort', 'year']);
        $perPage = 10;

        $documents = $this->documentRepository->paginate($filters, $perPage);
        return view('SigapDokumen.home.show', compact('documents'));
    }
}
