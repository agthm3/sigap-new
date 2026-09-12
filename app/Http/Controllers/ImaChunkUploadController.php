<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImaChunkUploadController extends Controller
{
    public function uploadChunk(Request $request)
    {
        $request->validate([
            'file'          => 'required|file',
            'file_id'       => 'required|string',
            'chunk_index'   => 'required|integer',
            'total_chunks'  => 'required|integer',
            'original_name' => 'required|string',
        ]);

        $fileId      = preg_replace('/[^A-Za-z0-9_\-]/', '', $request->file_id);
        $chunkIndex  = (int) $request->chunk_index;
        $totalChunks = (int) $request->total_chunks;
        $originalName= $request->original_name;

        // Simpan chunk sementara
        $chunkDirectory = "ima/chunks/{$fileId}";
        $chunkName      = "chunk_{$chunkIndex}";
        $request->file('file')->storeAs($chunkDirectory, $chunkName, 'local');

        // Cek apakah seluruh chunk sudah terkumpul
        $allChunksUploaded = true;
        for ($i = 0; $i < $totalChunks; $i++) {
            if (!Storage::disk('local')->exists("{$chunkDirectory}/chunk_{$i}")) {
                $allChunksUploaded = false;
                break;
            }
        }

        // Gabungkan jika sudah lengkap
        if ($allChunksUploaded) {
            $extension   = pathinfo($originalName, PATHINFO_EXTENSION);
            $safeExt     = $extension ? '.' . strtolower($extension) : '';
            $finalName   = Str::uuid() . '_' . time() . $safeExt;
            $finalPath   = "ima/temp/{$finalName}";

            // Buat stream file utuh di disk public
            $finalFullPath = Storage::disk('public')->path($finalPath);
            @mkdir(dirname($finalFullPath), 0755, true);
            $finalFile = fopen($finalFullPath, 'wb');

            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkPath = Storage::disk('local')->path("{$chunkDirectory}/chunk_{$i}");
                $chunkFile = fopen($chunkPath, 'rb');
                stream_copy_to_stream($chunkFile, $finalFile);
                fclose($chunkFile);
            }
            fclose($finalFile);

            // Bersihkan chunk
            Storage::disk('local')->deleteDirectory($chunkDirectory);

            return response()->json([
                'completed'     => true,
                'temp_path'     => $finalPath,
                'original_name' => $originalName,
                'file_size'     => filesize($finalFullPath),
            ]);
        }

        return response()->json([
            'completed'   => false,
            'chunk_index' => $chunkIndex,
        ]);
    }
}