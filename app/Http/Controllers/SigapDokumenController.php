<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\DocumentRepository;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SigapDokumenController extends Controller
{
    public function __construct(private DocumentRepository $repo)
    {
    }
    public function index(Request $request)
    {
        $filters = $request->only(['q', 'category', 'sensitivity', 'year', 'stakeholder']);
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

    public function show(int $id)
    {
        $doc = $this->repo->find($id);


        $fileUrl = asset('storage/' . $doc->file_path);
        $thumbUrl = $doc->thumb_path ? asset('storage/' . $doc->thumb_path) : null;

        $ext = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
        $isPdf = $ext === 'pdf';
        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);

        return view('dashboard.dokumen.show', compact('doc', 'fileUrl', 'thumbUrl', 'isPdf', 'isImage'));
    }


    public function download(int $id)
    {
    $doc = $this->repo->find($id);

    // Pastikan file ada di storage
    if (!Storage::disk('public')->exists($doc->file_path)) {
        abort(404, 'File tidak ditemukan');
    }

    // Ambil nama asli file (misalnya dari judul + ekstensi)
    $ext = pathinfo($doc->file_path, PATHINFO_EXTENSION);
    $filename = ($doc->alias ?? $doc->title).'.'.$ext;
    return Storage::disk('public')->download($doc->file_path, $filename);
    }

    public function destroy(int $id)
    {
        $this->repo->delete($id);
        return redirect()
            ->route('sigap-dokumen.index')
            ->with('success', 'Dokumen berhasil dihapus!');
    }
}
