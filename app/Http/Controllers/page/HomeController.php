<?php

namespace App\Http\Controllers\page;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $filters = $request->only(['q', 'category', 'stakeholder', 'year', 'sort']);
        $filters['sensitivity'] = 'public';

        $documents = $this->documentRepository->paginate($filters, 10);

        return view('SigapDokumen.home.show', compact('documents'));
    }

    public function detail(Document $document)
    {
        abort_if($document->sensitivity !== 'public', 403);

        $fileUrl  = asset('storage/' . $document->file_path);
        $thumbUrl = $document->thumb_path ? asset('storage/' . $document->thumb_path) : null;

        $ext = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
        $isPdf = $ext === 'pdf';
        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);

        $logs = ActivityLog::where('module', 'dokumen')
            ->where('object_type', Document::class)
            ->where('object_id', $document->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('SigapDokumen.home.detail', compact(
            'document',
            'fileUrl',
            'thumbUrl',
            'isPdf',
            'isImage',
            'logs'
        ));
    }

    public function download(Request $request, Document $document)
    {
        abort_if($document->sensitivity !== 'public' && !auth()->check(), 403);

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan');
        }

        if (auth()->check()) {
            ActivityLogger::log('dokumen', 'download', $document);
        } else {
            $data = $request->validate([
                'guest_name' => ['required', 'string', 'max:255'],
            ]);

            ActivityLogger::log('dokumen', 'download', $document, [
                'user_name' => $data['guest_name'],
                'user_role' => 'tamu',
                'source'    => 'public',
            ]);
        }

        $ext = pathinfo($document->file_path, PATHINFO_EXTENSION);
        $filename = ($document->alias ?? $document->title) . '.' . $ext;

        return Storage::disk('public')->download($document->file_path, $filename);
    }

    public function indexPegawai()
    {
        return view('SigapPegawai.index');
    }

    public function about()
    {
        return view('about');
    }
}