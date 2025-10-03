<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;  // atau Imagick kalau kamu pakai
use Intervention\Image\ImageManager;

class ImageCompressor
{
    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver()); // v3 style
    }

    /**
     * Kompres & resize gambar, simpan ke disk public.
     *
     * @param UploadedFile $file
     * @param string       $dir      contoh: 'kinerja/files'
     * @param int          $maxWidth px (0 = tidak resize)
     * @param int          $quality  1..100 (jpeg/webp)
     * @param string       $format   'jpg'|'webp'|'png'|'auto'
     * @return array{path:string,mime:string,size:int}
     */
    public function compressAndStore(
        UploadedFile $file,
        string $dir = 'kinerja/files',
        int $maxWidth = 1600,
        int $quality = 80,
        string $format = 'auto'
    ): array {
        $streamMime = $file->getMimeType() ?: 'application/octet-stream';
        $ext        = strtolower($file->getClientOriginalExtension());

        // Non-image → simpan apa adanya
        if (!str_starts_with($streamMime, 'image/')) {
            $path = $file->store($dir, 'public');
            return [
                'path' => $path,
                'mime' => $streamMime,
                'size' => Storage::disk('public')->size($path),
            ];
        }

        // Image → load
        $img = $this->manager->read($file->getPathname());

        // v3: orient(), v2: orientate() → pakai yang tersedia
        if (method_exists($img, 'orient')) {
            $img->orient();
        } elseif (method_exists($img, 'orientate')) {
            $img->orientate();
        }

        // Resize down kalau lebih besar dari maxWidth (0 = skip)
        if ($maxWidth > 0 && $img->width() > $maxWidth) {
            // v3 punya scale(); kalau v2, juga ada resize/fit — tetapi scale() ada di v3.
            // Kita pakai scale() (no upscaling).
            if (method_exists($img, 'scale')) {
                $img->scale(width: $maxWidth);
            } else {
                // fallback sangat jarang diperlukan, tapi aman
                $img->resize($maxWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
        }

        // Tentukan format simpan
        $saveAs = $format === 'auto'
            ? (in_array($ext, ['jpg','jpeg','png','webp']) ? $ext : 'jpg')
            : $format;

        // Encode + mime
        if ($saveAs === 'webp') {
            $encoded  = $img->toWebp($quality);
            $mimeOut  = 'image/webp';
            $finalExt = 'webp';
        } elseif ($saveAs === 'png') {
            $encoded  = $img->toPng(); // lossless
            $mimeOut  = 'image/png';
            $finalExt = 'png';
        } else {
            $encoded  = $img->toJpeg($quality);
            $mimeOut  = 'image/jpeg';
            $finalExt = 'jpg';
        }

        // Simpan ke storage public
        $filename = uniqid('img_', true) . '.' . $finalExt;
        $path     = trim($dir, '/') . '/' . $filename;
        Storage::disk('public')->put($path, (string) $encoded);

        return [
            'path' => $path,
            'mime' => $mimeOut,
            'size' => Storage::disk('public')->size($path),
        ];
    }
}
