<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use setasign\FpdiProtection\FpdiProtection;
use setasign\Fpdi\PdfReader;

class SigapPdfController extends Controller
{
    public function landing(): View
    {
        return view('sigappdf.landing');
    }
 
    public function merge(): View
    {
        return view('sigappdf.merge');
    }

public function compress(): View
{
    return view('sigappdf.compress');
}

    public function split(): View
    {
        return view('sigappdf.split');
    }

    public function rotate(): View
    {
        return view('sigappdf.rotate');
    }

    public function extract(): View
    {
        return view('sigap.pdf.tool-placeholder', [
            'toolTitle' => 'Extract Pages',
            'toolDesc' => 'Ekstrak halaman tertentu dari dokumen PDF Anda secara instan.',
            'category' => 'Organize PDF'
        ]);
    }

    public function deletePages(): View
    {
        return view('sigappdf.delete-pages');
    }

    public function reorder(): View
    {
        return view('sigap.pdf.tool-placeholder', [
            'toolTitle' => 'Susun Ulang Halaman PDF',
            'toolDesc' => 'Ubah urutan halaman dokumen PDF dengan cepat.',
            'category' => 'Organize PDF'
        ]);
    }

    public function watermark(): View
    {
        return view('sigappdf.watermark');
    }

    public function addPassword(): View
    {
        return view('sigappdf.add-password');
    }
    
    public function removeMetadata(): View
    {
        return view('sigappdf.remove-metadata');
    }

    public function jpgToPdf(): View
    {
        return view('sigappdf.convert', ['type' => 'jpg-to-pdf', 'title' => 'JPG ke PDF']);
    }

    public function pngToPdf(): View
    {
        return view('sigappdf.convert', ['type' => 'png-to-pdf', 'title' => 'PNG ke PDF']);
    }

    public function pdfToImage(): View
    {
        return view('sigappdf.pdf-to-image');
    }
    public function changeVersion(): View
    {
        return view('sigappdf.change-version');
    }
}