<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\DocumentRepository;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Log;

class SigapDokumenController extends Controller
{
    public function __construct(private DocumentRepository $repo)
    {
    }
    public function index(Request $request)
    {
        $filters = $request->only(['q', 'category', 'sensitivity', 'year']);
        $docs = $this->repo->paginate($filters, perPage: 10);
        return view('dashboard.dokumen.index', compact('docs'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // dd($request->all());
        $validated = $request->validate([
            'number'          => ['nullable','string','max:255'],
            'title'           => ['required','string','max:255'],
            'alias'           => ['nullable','string','max:255','unique:documents,alias'],
            'year'            => ['required','integer','between:1900,'.((int)date('Y')+1)],
            'category'        => ['required','string','max:100'],
            // 'stakeholder'     => ['nullable','string','max:255'],
            'description'     => ['nullable','string'],
            // 'tags'            => ['nullable'], // string "a,b,c" atau array
           'sensitivity' => ['required','in:public,private'],
            // 'related_user_id' => ['nullable','exists:users,id'],
            // 'version'         => ['nullable','string','max:50'],
            // 'doc_date'        => ['nullable','date'],
            'file'            => ['required','file','max:20480'], // 20 MB
            'thumb'           => ['nullable','image','max:4096'],
        ]);


        $validated['created_by'] = $validated['updated_by'] = FacadesAuth::id() ?? 1; // fallback jika belum pakai auth


        $doc = $this->repo->create(
            $validated,
            $request->file('file'),
            $request->file('thumb')
        );
        return redirect()
                    ->route('sigap-dokumen.index')
                    ->with('success', "Dokumen '{$doc->title}' berhasil disimpan!");
    }
}
