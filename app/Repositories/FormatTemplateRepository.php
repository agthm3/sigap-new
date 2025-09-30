<?php

namespace App\Repositories;

use App\Models\FormatAccessLog;
use App\Models\FormatTemplate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FormatTemplateRepository
{
    public function paginate(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $q = FormatTemplate::query();

        if (!empty($filters['q'])) {
            $term = $filters['q'];
            $q->where(function($w) use ($term) {
                $w->where('title','like',"%$term%")
                  ->orWhere('description','like',"%$term%");
            });
        }
        foreach (['category','file_type','privacy','lang','orientation'] as $f) {
            if (!empty($filters[$f])) $q->where($f, $filters[$f]);
        }

        // sort
        $sort = $filters['sort'] ?? 'latest';
        $q->when($sort === 'latest', fn($qq)=>$qq->orderByDesc('updated_at'))
          ->when($sort === 'az',     fn($qq)=>$qq->orderBy('title'))
          ->when($sort === 'za',     fn($qq)=>$qq->orderByDesc('title'));

        return $q->paginate($perPage)->withQueryString();
    }

    public function findOrFail(int $id): FormatTemplate
    {
        return FormatTemplate::findOrFail($id);
    }

    public function create(array $data, UploadedFile $file, int $userId): FormatTemplate
    {
        // simpan file
        $disk = 'public';
        $dir  = 'sigap/format/'.date('Y/m');
        $path = $file->store($dir, $disk);

        $hash = null;
        if (($data['privacy'] ?? 'public') === 'private') {
            $code = $data['access_code'] ?? Str::random(8);
            $hash = Hash::make($code);
            // catatan: JANGAN simpan plaintext. Tampilkan hanya sekali di flash kalau perlu.
        }

        return FormatTemplate::create([
            'title'         => $data['title'],
            'category'      => $data['category'],
            'description'   => $data['description'] ?? null,
            'lang'          => $data['lang'] ?? 'id',
            'orientation'   => $data['orientation'] ?? 'portrait',
            'file_type'     => $data['file_type'],
            'file_path'     => $path,
            'original_name' => $file->getClientOriginalName(),
            'size'          => $file->getSize(),
            'privacy'       => $data['privacy'] ?? 'public',
            'access_code_hash' => $hash,
            'tags'          => isset($data['tags']) ? $this->normalizeTags($data['tags']) : null,
            'uploaded_by'   => $userId,
        ]);
    }

    public function update(FormatTemplate $t, array $data, ?UploadedFile $file = null): FormatTemplate
    {
        if ($file) {
            $disk = 'public';
            $dir  = 'sigap/format/'.date('Y/m');
            $path = $file->store($dir, $disk);
            $t->file_path     = $path;
            $t->original_name = $file->getClientOriginalName();
            $t->size          = $file->getSize();
            $t->file_type     = $data['file_type'] ?? $t->file_type;
        }

        if (($data['privacy'] ?? $t->privacy) === 'private') {
            // hanya jika user mengisi access_code baru → ganti hash
            if (!empty($data['access_code'])) {
                $t->access_code_hash = Hash::make($data['access_code']);
            }
        } else {
            $t->privacy = 'public';
            $t->access_code_hash = null;
        }

        $t->title       = $data['title']       ?? $t->title;
        $t->category    = $data['category']    ?? $t->category;
        $t->description = $data['description'] ?? $t->description;
        $t->lang        = $data['lang']        ?? $t->lang;
        $t->orientation = $data['orientation'] ?? $t->orientation;
        $t->tags        = isset($data['tags']) ? $this->normalizeTags($data['tags']) : $t->tags;

        $t->save();
        return $t;
    }

    public function delete(FormatTemplate $t): void
    {
        // (opsional) hapus file fisik kalau mau
        // Storage::disk('public')->delete($t->file_path);
        $t->delete();
    }

    public function verifyAccessCode(FormatTemplate $t, string $code): bool
    {
        if ($t->privacy !== 'private') return true;
        if (!$t->access_code_hash) return false;
        return Hash::check($code, $t->access_code_hash);
    }

    public function logAccess(FormatTemplate $t, ?int $userId, string $action = 'download', bool $success = true): void
    {
        try {
            FormatAccessLog::create([
                'format_template_id' => $t->id,
                'user_id'   => $userId,
                'action'    => $action,
                'success'   => $success,
                'ip'        => request()->ip(),
                'user_agent'=> request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // jangan ganggu flow utama
        }
    }

    private function normalizeTags(string|array|null $tags): ?array
    {
        if (is_null($tags)) return null;
        if (is_array($tags)) return array_values(array_filter(array_map(fn($v)=>trim($v), $tags)));
        // string "a, b, c"
        return array_values(array_filter(array_map(fn($v)=>trim($v), explode(',', $tags))));
    }
}
