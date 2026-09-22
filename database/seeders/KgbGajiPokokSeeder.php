<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KgbGajiPokok;

class KgbGajiPokokSeeder extends Seeder
{
    public function run(): void
    {
        // 1. DATA PNS (PP No. 5 Tahun 2024)
        $dataPns = [
            // Golongan III/a (Masa kerja kelipatan 2 tahun)
            ['jenis_pegawai' => 'pns', 'golongan' => 'III/a', 'masa_kerja' => 0, 'nominal' => 2785700, 'regulasi' => 'PP 5/2024'],
            ['jenis_pegawai' => 'pns', 'golongan' => 'III/a', 'masa_kerja' => 2, 'nominal' => 2873500, 'regulasi' => 'PP 5/2024'],
            ['jenis_pegawai' => 'pns', 'golongan' => 'III/a', 'masa_kerja' => 4, 'nominal' => 2963900, 'regulasi' => 'PP 5/2024'],
            ['jenis_pegawai' => 'pns', 'golongan' => 'III/a', 'masa_kerja' => 6, 'nominal' => 3057200, 'regulasi' => 'PP 5/2024'],
            ['jenis_pegawai' => 'pns', 'golongan' => 'III/a', 'masa_kerja' => 8, 'nominal' => 3153500, 'regulasi' => 'PP 5/2024'],
            ['jenis_pegawai' => 'pns', 'golongan' => 'III/a', 'masa_kerja' => 10, 'nominal' => 3252800, 'regulasi' => 'PP 5/2024'],
            ['jenis_pegawai' => 'pns', 'golongan' => 'III/a', 'masa_kerja' => 12, 'nominal' => 3355200, 'regulasi' => 'PP 5/2024'],
            ['jenis_pegawai' => 'pns', 'golongan' => 'III/a', 'masa_kerja' => 14, 'nominal' => 3460900, 'regulasi' => 'PP 5/2024'],
            ['jenis_pegawai' => 'pns', 'golongan' => 'III/a', 'masa_kerja' => 16, 'nominal' => 3569900, 'regulasi' => 'PP 5/2024'],
            ['jenis_pegawai' => 'pns', 'golongan' => 'III/a', 'masa_kerja' => 18, 'nominal' => 3682300, 'regulasi' => 'PP 5/2024'],
            ['jenis_pegawai' => 'pns', 'golongan' => 'III/a', 'masa_kerja' => 20, 'nominal' => 3798300, 'regulasi' => 'PP 5/2024'],
            ['jenis_pegawai' => 'pns', 'golongan' => 'III/a', 'masa_kerja' => 22, 'nominal' => 3917900, 'regulasi' => 'PP 5/2024'],
            ['jenis_pegawai' => 'pns', 'golongan' => 'III/a', 'masa_kerja' => 24, 'nominal' => 4041300, 'regulasi' => 'PP 5/2024'],
            ['jenis_pegawai' => 'pns', 'golongan' => 'III/a', 'masa_kerja' => 26, 'nominal' => 4168600, 'regulasi' => 'PP 5/2024'],
            ['jenis_pegawai' => 'pns', 'golongan' => 'III/a', 'masa_kerja' => 28, 'nominal' => 4299900, 'regulasi' => 'PP 5/2024'],
            ['jenis_pegawai' => 'pns', 'golongan' => 'III/a', 'masa_kerja' => 30, 'nominal' => 4435400, 'regulasi' => 'PP 5/2024'],
            ['jenis_pegawai' => 'pns', 'golongan' => 'III/a', 'masa_kerja' => 32, 'nominal' => 4575200, 'regulasi' => 'PP 5/2024'],

            // Golongan III/b
            ['jenis_pegawai' => 'pns', 'golongan' => 'III/b', 'masa_kerja' => 0, 'nominal' => 2903600, 'regulasi' => 'PP 5/2024'],
            ['jenis_pegawai' => 'pns', 'golongan' => 'III/b', 'masa_kerja' => 2, 'nominal' => 2995000, 'regulasi' => 'PP 5/2024'],
            ['jenis_pegawai' => 'pns', 'golongan' => 'III/b', 'masa_kerja' => 4, 'nominal' => 3089300, 'regulasi' => 'PP 5/2024'],
            ['jenis_pegawai' => 'pns', 'golongan' => 'III/b', 'masa_kerja' => 6, 'nominal' => 3186600, 'regulasi' => 'PP 5/2024'],
            ['jenis_pegawai' => 'pns', 'golongan' => 'III/b', 'masa_kerja' => 8, 'nominal' => 3287000, 'regulasi' => 'PP 5/2024'],
            ['jenis_pegawai' => 'pns', 'golongan' => 'III/b', 'masa_kerja' => 10, 'nominal' => 3390500, 'regulasi' => 'PP 5/2024'],

            // Golongan IV/a
            ['jenis_pegawai' => 'pns', 'golongan' => 'IV/a', 'masa_kerja' => 0, 'nominal' => 3287800, 'regulasi' => 'PP 5/2024'],
            ['jenis_pegawai' => 'pns', 'golongan' => 'IV/a', 'masa_kerja' => 2, 'nominal' => 3391300, 'regulasi' => 'PP 5/2024'],
            ['jenis_pegawai' => 'pns', 'golongan' => 'IV/a', 'masa_kerja' => 4, 'nominal' => 3498100, 'regulasi' => 'PP 5/2024'],
            ['jenis_pegawai' => 'pns', 'golongan' => 'IV/a', 'masa_kerja' => 6, 'nominal' => 3608300, 'regulasi' => 'PP 5/2024'],
        ];

        // 2. DATA PPPK (Perpres No. 11 Tahun 2024)
        $dataPppk = [
            // Golongan IX (Setara S1 / Golongan III/a PNS)
            ['jenis_pegawai' => 'pppk', 'golongan' => 'IX', 'masa_kerja' => 0, 'nominal' => 3203600, 'regulasi' => 'Perpres 11/2024'],
            ['jenis_pegawai' => 'pppk', 'golongan' => 'IX', 'masa_kerja' => 2, 'nominal' => 3304500, 'regulasi' => 'Perpres 11/2024'],
            ['jenis_pegawai' => 'pppk', 'golongan' => 'IX', 'masa_kerja' => 4, 'nominal' => 3408600, 'regulasi' => 'Perpres 11/2024'],
            ['jenis_pegawai' => 'pppk', 'golongan' => 'IX', 'masa_kerja' => 6, 'nominal' => 3515900, 'regulasi' => 'Perpres 11/2024'],
            ['jenis_pegawai' => 'pppk', 'golongan' => 'IX', 'masa_kerja' => 8, 'nominal' => 3626700, 'regulasi' => 'Perpres 11/2024'],
            ['jenis_pegawai' => 'pppk', 'golongan' => 'IX', 'masa_kerja' => 10, 'nominal' => 3740900, 'regulasi' => 'Perpres 11/2024'],

            // Golongan X
            ['jenis_pegawai' => 'pppk', 'golongan' => 'X', 'masa_kerja' => 0, 'nominal' => 3339100, 'regulasi' => 'Perpres 11/2024'],
            ['jenis_pegawai' => 'pppk', 'golongan' => 'X', 'masa_kerja' => 2, 'nominal' => 3444200, 'regulasi' => 'Perpres 11/2024'],
            ['jenis_pegawai' => 'pppk', 'golongan' => 'X', 'masa_kerja' => 4, 'nominal' => 3552700, 'regulasi' => 'Perpres 11/2024'],
        ];

        foreach (array_merge($dataPns, $dataPppk) as $item) {
            KgbGajiPokok::updateOrCreate(
                [
                    'jenis_pegawai' => $item['jenis_pegawai'],
                    'golongan' => $item['golongan'],
                    'masa_kerja' => $item['masa_kerja'],
                    'regulasi' => $item['regulasi'],
                ],
                [
                    'nominal' => $item['nominal'],
                ]
            );
        }
    }
}