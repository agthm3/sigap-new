<?php 


namespace App\Repositories;
use App\Models\Inovasi;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InovasiRepository
{
 public function create(
        array $data,
        ?UploadedFile $anggaran = null,
        ?UploadedFile $profilBisnis = null,
        ?UploadedFile $haki = null,
        ?UploadedFile $penghargaan = null
    ): Inovasi {
        return DB::transaction(function () use ($data, $anggaran, $profilBisnis, $haki, $penghargaan) {

            // Normalisasi default tahap jika belum diisi
            $data['tahap_inovasi']  = $data['tahap_inovasi']  ?? 'Inisiatif';

            // Simpan file, path di kolom *_file (disajikan via storage:link)
            if ($anggaran instanceof UploadedFile) {
                $data['anggaran_file'] = $anggaran->store('inovasi/anggaran', 'public');
            }
            if ($profilBisnis instanceof UploadedFile) {
                $data['profil_bisnis_file'] = $profilBisnis->store('inovasi/profil_bisnis', 'public');
            }
            if ($haki instanceof UploadedFile) {
                $data['haki_file'] = $haki->store('inovasi/haki', 'public');
            }
            if ($penghargaan instanceof UploadedFile) {
                $data['penghargaan_file'] = $penghargaan->store('inovasi/penghargaan', 'public');
            }

            $inovasi = Inovasi::create($data);

            Log::channel('giga')->info('Inovasi created', ['id' => $inovasi->id, 'judul' => $inovasi->judul]);

            return $inovasi;
        });
    }

    public function paginate(array $filters = [], int $perPage = 10)
    {
        $q = Inovasi::query();

        if (!empty($filters['q'])) {
            $kw = trim($filters['q']);
            $q->where(function ($w) use ($kw) {
                $w->where('judul', 'like', "%{$kw}%")
                  ->orWhere('opd_unit', 'like', "%{$kw}%");
            });
        }

        if (!empty($filters['urusan']))    $q->where('urusan', $filters['urusan']);
        if (!empty($filters['inisiator'])) $q->where('inisiator', $filters['inisiator']);

        if (!empty($filters['tahap']) && !empty($filters['tahap_status'])) {
            $map = [
                'Inisiatif' => 'tahap_inovasi',
                'Uji Coba'  => 'tahap_inovasi',
                'Penerapan' => 'tahap_inovasi',
            ];
            $kolom = $map[$filters['tahap']] ?? null;
            if ($kolom) $q->where($kolom, $filters['tahap_status']);
        }

        if (($filters['sort'] ?? 'terbaru') === 'judul') {
            $q->orderBy('judul');
        } else {
            $q->latest('created_at');
        }

        Log::channel('giga')->info('Inovasi search filters applied', $filters);
        Log::channel('giga')->info('Inovasi search query', ['query' => $q->toSql(), 'bindings' => $q->getBindings()]);

        return $q->paginate($perPage)->withQueryString();
    }

    public function delete(int $id): void
    {
        $inovasi = Inovasi::findOrFail($id);
        $judul = $inovasi->judul;
        $inovasi->delete();

        Log::channel('giga')->info('Inovasi deleted', ['id' => $id, 'judul' => $judul]);
    }

    public function find(int $id): Inovasi
    {
        return Inovasi::findOrFail($id);
    }
}