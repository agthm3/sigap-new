<?php

namespace App\Imports;

use App\Models\SertifikatPeserta;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SertifikatImport implements ToModel, WithHeadingRow
{
    protected $kegiatan_id;

    public function __construct($kegiatan_id)
    {
        $this->kegiatan_id = $kegiatan_id;
    }

    public function model(array $row)
    {
        return new SertifikatPeserta([
            'kegiatan_id' => $this->kegiatan_id,
            'nomor_sertifikat' => $row['nomor_sertifikat'],
            'nama_penerima' => $row['nama_penerima'],
            'instansi' => $row['instansi'],
            'keterangan' => $row['keterangan'] ?? null,
            'status' => 'Aktif'
        ]);
    }
}