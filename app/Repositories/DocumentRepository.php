<?php

namespace App\Repositories;

use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentRepository
{
    /**
     * Simpan dokumen baru (beserta file & thumbnail bila ada).
     * Menghasilkan alias unik jika kosong.
     */
    public function create(array $data, ?UploadedFile $file = null, ?UploadedFile $thumb = null): Document
    {

        // dd($data, $file, $thumb);
        return DB::transaction(function () use ($data, $file, $thumb) {

            // Map status akses dari UI ke enum DB
            // UI: "Publik" | "Akses Terkendali"
            if (!empty($data['sensitivity'])) {
                $data['sensitivity'] = $data['sensitivity'] === 'Akses Terkendali' ? 'private' : 'public';
            }

            // Simpan file fisik
            if ($file) {
                // pastikan sudah: php artisan storage:link
                $data['file_path'] = $file->store('documents', 'public');
            }else if (empty($data['file_path'])) {
                throw new \InvalidArgumentException('File dokumen wajib diunggah.');
            }

            if ($thumb) {
                $data['thumb_path'] = $thumb->store('thumbnails', 'public');
            }

            // Alias unik bila kosong
            if (empty($data['alias'])) {
                $base = strtoupper(Str::slug(($data['title'] ?? 'dokumen') . '-' . ($data['year'] ?? now()->year), '_'));
                $alias = $base;
                $i = 1;
                while (Document::where('alias', $alias)->exists()) {
                    $alias = $base . '_' . $i++;
                }
                $data['alias'] = $alias;
            }

            // Tags optional: terima string dipisah koma atau array
            if (isset($data['tags'])) {
                if (is_string($data['tags'])) {
                    $tags = array_values(array_filter(array_map('trim', explode(',', $data['tags']))));
                    $data['tags'] = $tags ?: null;
                }
            }

            return Document::create($data);
        });
    }

    public function paginate(array $filters = [], int $perPage = 10)
    {
        $q = Document::query();

        if(!empty($filters['q'])){
            $kw = trim($filters['q']);
            $q->where(function($query) use ($kw) {
                $query->where('title', 'like', "%{$kw}%")
                      ->orWhere('alias', 'like', "%{$kw}%")
                      ->orWhere('number', 'like', "%{$kw}%")
                      ->orWhere('stakeholder', 'like', "%{$kw}%")
                      ->orWhere('description', 'like', "%{$kw}%");
            });
        }

        if(!empty($filters['stakeholder'])) {
            $q->where('stakeholder', 'like', "%{$filters['stakeholder']}%");
        }

        if(!empty($filters['category'])) {
            $q->where('category', $filters['category']);
        }

        if(!empty($filters['sensitivity'])) {
            $q->where('sensitivity', $filters['sensitivity']);
        }

        if(!empty($filters['year'])) {
            $q->where('year', $filters['year']);
        }

        $q->latest('created_at');

        Log::channel('giga')->info('Document search filters applied', $filters);
        //Log untuk menampilkan hasil filtering
        Log::channel('giga')->info('Document search query', ['query' => $q->toSql(), 'bindings' => $q->getBindings()]);

        return $q->paginate($perPage);
    }

    public function find(int $id): ?Document
    {
        return Document::findOrFail($id);
    }

    public function delete(int $id, bool $force = false): void
    {
        $doc = Document::findOrFail($id);

        if ($force) {
            // hapus file fisik
            if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }
            if ($doc->thumb_path && Storage::disk('public')->exists($doc->thumb_path)) {
                Storage::disk('public')->delete($doc->thumb_path);
            }

            $doc->forceDelete();
        } else {
            $doc->delete(); // soft delete
        }
    }

    public function update(
        int $id, 
        array $data, 
        ?UploadedFile $file = null,
        ?UploadedFile $thumb = null
    ):Document {
        return DB::transaction(function()use($id, $data, $file, $thumb){
            $doc = Document::findOrFail($id);


            if(!empty($data['sensitivity'])) {
                $data['sensitivity'] = $data['sensitivity'] === 'Akses Terkendali' ? 'private' : 'public';
            }

            if($thumb instanceof UploadedFile){
                if($doc->thumb_path && Storage::disk('public')->exists($doc->thumb_path)) {
                    Storage::disk('public')->delete($doc->thumb_path);
                }
                $data['thumb_path']= $thumb->store('thumbnails', 'public');
            }

            if(empty($data['alias'])) {
                $base = strtoupper(Str::slug(($data['title'] ?? 'dokumen') . '-' . ($data['year'] ?? now()->year), '_'));
                $alias = $base;
                $i = 1;
                while (Document::where('alias', $alias)->where('id', '!=', $id)->exists()) {
                    $alias = $base . '_' . $i++;
                }
                $data['alias'] = $alias;
            }   

            $doc->fill($data)->save();

            return $doc->refresh();
        });
    }
}