<?php

namespace App\Repositories;

use App\Models\Evidence;
use App\Models\EvidenceTemplate;
use App\Models\EvidenceTemplateParam;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EvidenceRepository
{
    /**
     * Ambil 20 indikator (template) lengkap dengan parameter + merge isian inovasi (jika ada).
     * Return: collection 20 item terurut no=1..20.
     */
    private function evidenceMultipliers(): array
    {
        return [
            1=>3, 2=>2, 3=>2, 4=>1, 5=>2,
            6=>1, 7=>1, 8=>1, 9=>1, 10=>1,
            11=>1, 12=>2, 13=>2, 14=>3, 15=>2,
            16=>3, 17=>2, 18=>1, 19=>2, 20=>4,
        ];
    }

    private function weightedScoreFor(int $no, int $rawWeight): int
    {
        $mult = $this->evidenceMultipliers()[$no] ?? 1;
        return $rawWeight * $mult;
    }
    public function listForInovasi(int $inovasiId): array
    {
        $templates = EvidenceTemplate::with(['params' => function($q){
            $q->orderBy('sort_order')->orderBy('id');
        }])->orderBy('no')->get();

       $byNo = Evidence::with('files')
        ->where('inovasi_id', $inovasiId)
        ->get()
        ->keyBy('no');

        return $templates->map(function($t) use ($byNo) {
            /** @var \App\Models\Evidence|null $ev */
            $ev = $byNo->get($t->no);

            return [
                'no'          => (int) $t->no,
                'indikator'   => $t->indikator,
                'keterangan'  => $t->keterangan,
                'jenis_file'  => $t->jenis_file,
                'hint'        => $t->hint,
                // ⬇️ pastikan array biasa
                'params'      => $t->params->map(fn($p)=>[
                    'id'     => (int) $p->id,
                    'label'  => $p->label,
                    'weight' => (int) $p->weight,
                ])->values()->all(),

                'selected_label'  => $ev?->parameter_label,
                'selected_weight' => (int) ($ev?->parameter_weight ?? 0),
                'deskripsi'       => $ev?->deskripsi,
                'file_url'        => $ev?->file_path ? Storage::disk('public')->url($ev->file_path) : null,
                'file_name'       => $ev?->file_name,
                'files' => $ev
                ? $ev->files->map(fn($f) => [
                    'id'   => (int) $f->id,
                    'url'  => Storage::disk('public')->url($f->file_path),
                    'name' => $f->file_name,
                    'size' => $f->file_size,
                ])->values()->all()
                : [],

            ];
        })->values()->all(); // ⬅️ array murni
    }

    /**
     * Simpan bulk 1..20 indikator sekaligus.
     * $items: array of [
     *   'no' => 1..20,
     *   'param_id' => (optional) EvidenceTemplateParam id,
     *   'parameter_label' => (jika tidak pakai param_id),
     *   'parameter_weight' => (angka, jika tidak pakai param_id),
     *   'deskripsi' => string|null,
     *   'link_url' => string|null
     * ]
     * $files: array map no => UploadedFile (mis. [1 => UploadedFile, 5 => UploadedFile])
     */
    public function upsertBulk(int $inovasiId, array $items, array $files = []): void
    {
        DB::transaction(function () use ($inovasiId, $items, $files) {
            // preload templates dan params
            $templates = EvidenceTemplate::all()->keyBy('no');
            $paramsById = EvidenceTemplateParam::all()->keyBy('id');

            foreach ($items as $row) {
                $no = (int) ($row['no'] ?? 0);
                if ($no < 1 || $no > 20) continue;

                $tpl = $templates->get($no);
                if (!$tpl) continue;

                // tentukan parameter terpilih
                $paramId = $row['param_id'] ?? null;
                if ($paramId) {
                    $p = $paramsById->get($paramId);
                    $label  = $p?->label ?? null;
                    $weight = $p?->weight ?? 0;
                } else {
                    $label  = $row['parameter_label'] ?? null;
                    $weight = (int)($row['parameter_weight'] ?? 0);
                }

                // handle file jika ada
                $filePath = $fileName = $fileMime = null; $fileSize = 0;
                /** @var UploadedFile|null $f */
                $f = $files[$no] ?? null;
                if ($f instanceof UploadedFile) {
                    // folder: inovasi/{id}/evidence/no-{no}
                    $stored = $f->store("inovasi/{$inovasiId}/evidence/no-{$no}", 'public');
                    $filePath = $stored;
                    $fileName = $f->getClientOriginalName();
                    $fileMime = $f->getClientMimeType();
                    $fileSize = $f->getSize();
                }

                $evidence  = Evidence::updateOrCreate(
                    ['inovasi_id'=>$inovasiId,'no'=>$no],
                    [
                        'template_id'       => $tpl->id,
                        'indikator'         => $tpl->indikator,
                        'jenis_file'        => $tpl->jenis_file,

                        'parameter_label'   => $label,
                        'parameter_weight'  => $weight,
                        'deskripsi'         => $row['deskripsi'] ?? null,
                        'link_url'          => $row['link_url'] ?? null,

                        // file: hanya overwrite jika upload baru
                        'file_path'         => $filePath ?: DB::raw('file_path'),
                        'file_name'         => $fileName ?: DB::raw('file_name'),
                        'file_mime'         => $fileMime ?: DB::raw('file_mime'),
                        'file_size'         => $fileSize ?: DB::raw('file_size'),
                    ]
                );

                if (!empty($files[$no])) {
                foreach ($files[$no] as $f) {
                    $path = $f->store("inovasi/{$inovasiId}/evidence/no-{$no}", 'public');

                    $evidence->files()->create([
                        'file_path' => $path,
                        'file_name' => $f->getClientOriginalName(),
                        'file_mime' => $f->getClientMimeType(),
                        'file_size' => $f->getSize(),
                    ]);
                }
            }
            }
        });
    }

    /**
     * Hapus file dari satu indikator (isi evidence tetap ada).
     */
    public function removeFile(int $inovasiId, int $no): void
    {
        $ev = Evidence::where('inovasi_id',$inovasiId)->where('no',$no)->firstOrFail();
        if ($ev->file_path && Storage::disk('public')->exists($ev->file_path)) {
            Storage::disk('public')->delete($ev->file_path);
        }
        $ev->update(['file_path'=>null,'file_name'=>null,'file_mime'=>null,'file_size'=>0]);
    }

    /**
     * Hitung total bobot evidence untuk inovasi.
     */
    public function totalWeight(int $inovasiId): int
    {
        $rows = \App\Models\Evidence::where('inovasi_id', $inovasiId)
            ->get(['no','parameter_weight']);

        $total = 0;
        foreach ($rows as $r) {
            $no = (int) $r->no;
            $w  = (int) $r->parameter_weight;
            $total += $this->weightedScoreFor($no, $w);
        }
        return (int) $total;
    }

    public function evidenceChecklistText(): string
    {
        $rows = EvidenceTemplate::orderBy('no')->get(['no','indikator']);

        $lines = [];
        foreach ($rows as $r) {
            $lines[] = "{$r->no}. {$r->indikator}:";
        }

        return "Mohon dilakukan perbaikan pada Evidence berikut:\n\n"
            . implode("\n", $lines);
    }
    public function deleteMarkedFiles(int $inovasiId, array $deleteFiles): void
    {
        foreach ($deleteFiles as $no => $files) {
            foreach ($files as $fileId => $flag) {
                if ((int)$flag !== 1) continue;

                $file = \App\Models\EvidenceFile::where('id', $fileId)
                    ->whereHas('evidence', fn($q) =>
                        $q->where('inovasi_id', $inovasiId)
                        ->where('no', $no)
                    )->first();

                if (!$file) continue;

                if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                    Storage::disk('public')->delete($file->file_path);
                }

                $file->delete();
            }
        }
    }


}
