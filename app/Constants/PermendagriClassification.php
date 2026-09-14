<?php

namespace App\Constants;

class PermendagriClassification
{
    /**
     * Kode Klasifikasi Berdasarkan Permendagri No. 83 Tahun 2022
     * tentang Kode Klasifikasi Arsip di Lingkungan Kementerian Dalam Negeri
     * dan Pemerintah Daerah.
     */
    public const LIST = [
        ['code' => '000', 'name' => 'FASILITATIF - UMUM'],
        ['code' => '100', 'name' => 'FASILITATIF - PEMERINTAHAN'],
        ['code' => '200', 'name' => 'FASILITATIF - POLITIK'],
        ['code' => '300', 'name' => 'FASILITATIF - KEAMANAN DAN KETERTIBAN'],
        ['code' => '400', 'name' => 'FASILITATIF - KESEJAHTERAAN RAKYAT'],
        ['code' => '500', 'name' => 'FASILITATIF - PEREKONOMIAN'],
        ['code' => '600', 'name' => 'FASILITATIF - PEKERJAAN UMUM DAN KETENAGAKERJAAN'],
        ['code' => '700', 'name' => 'FASILITATIF - PENGAWASAN'],
        ['code' => '800', 'name' => 'FASILITATIF - KEPEGAWAIAN'],
        ['code' => '900', 'name' => 'FASILITATIF - KEUANGAN'],
        ['code' => '010', 'name' => 'URUSAN KEDINASAN'],
        ['code' => '020', 'name' => 'PERALATAN DAN SARANA PRASARANA'],
        ['code' => '040', 'name' => 'PERPUSTAKAAN, ARSIP, DAN DOKUMENTASI'],
        ['code' => '050', 'name' => 'PERENCANAAN PEMBANGUNAN'],
        ['code' => '060', 'name' => 'ORGANISASI DAN KETATALAKSANAAN'],
        ['code' => '070', 'name' => 'PENELITIAN, PENGKAJIAN, DAN PENGEMBANGAN'],
        ['code' => '080', 'name' => 'TEKNOLOGI INFORMASI DAN KOMUNIKASI'],
        ['code' => '090', 'name' => 'PERJALANAN DINAS'],
    ];

    public static function all(): array
    {
        return self::LIST;
    }
}