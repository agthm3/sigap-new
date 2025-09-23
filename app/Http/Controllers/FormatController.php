<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormatController extends Controller
{
    public function index()
    {
        return view('SigapFormat.index');
    }

    public function show($id)
    {
        $template = (object)[
            'id' => $id,
            'title' => 'Template Surat Tugas (DOCX)',
            'description' => 'Format surat tugas resmi...',
            'category' => 'Surat',
            'file_type' => 'PDF', // coba ganti ke PDF biar preview iframe jalan
            'lang' => 'id',
            'orientation' => 'portrait',
            'size' => '42 KB',
            'tags' => ['BRIDA','Resmi'],
            'updated_at' => now()->subDays(1),
            'file_path' => 'templates/surat-tugas.pdf',
        ];

        // kalau simpan di disk public
        $previewUrl = \Storage::disk('public')->url($template->file_path);

        return view('SigapFormat.show', compact('template','previewUrl'));
    }


    public function preview($id)
    {
        $t = FormatTemplate::findOrFail($id);

        // Dapatkan URL publik/temporary sesuai disk:
        // - Jika pakai 'public' disk:
        $url = \Storage::disk('public')->url($t->file_path);

        // - Jika file privat & pakai S3, buat temporary URL:
        // $url = \Storage::disk('s3')->temporaryUrl($t->file_path, now()->addMinutes(15));

        // Render
        return view('SigapFormat.preview', [
            'template'   => $t,
            'previewUrl' => $url,
        ]);
    }

}
