<?php

return [
    // kode_kategori => ['label' => 'teks panjang', 'rhks' => [kode_rhk => 'label_rhk']]
    'categories' => [
        '4p' => [
            'label' => 'Penelitian, Pengembangan, Pengkajian, dan Penerapan (4P)',
            'rhks'  => [
                'r1' => 'Pemantauan dan evaluasi penelitian, pengembangan,  pengkajian, dan penerapan',
                'r2' => 'Penyusunan kebijakan berbasis hasil riset',
                'r3' => 'Fasilitasi dan pembinaan pelaksanaan penelitian, pengembangan, pengkajian, dan penerapan',
                'r4' => 'Koordinasi dan sinkronisasi pelaksanaan kebijakan penelitian, pengembangan, pengkajian dan penerapan',
                'r5' => 'Bimbingan teknis dan supervisi di bidang penelitian, pengembangan , pengkajian, dan penerapan, kerja sama pembangunan ilmu pengetahuan dan teknologi, serta kemitraan penelitian, pengembangan, pengkajian dan penerapan',
                'r6' => 'Koordinasi sistem ilmu pengetahuan dan teknologi di daerah',
            ],
        ],
        'inovasi' => [
            'label' => 'Invensi dan inovasi',
            'rhks'  => [
                'r1' => 'Fasilitasi dan pembinaan untuk penguatan kelembagaan riset dan inovasi di daerah',
                'r2' => 'Fasilitasi dan pembinaan untuk peningkatan perlindungan dan pemanfaatan kekayaan intelektual',
                'r3' => 'Fasilitasi dan pembinaan untuk peningkatan difusi inovasi',
                'r4' => 'Fasilitasi dan pembinaan untuk penyediaan anggaran riset dan inovasi',
                'r5' => 'Koordinasi pelaksanaan penelitian dan pengabdian kepada masyarakat berbasis penelitian, pengembangan, pengkajian, dan penerapan ilmu pengetahuan dan teknologi yang dihasilkan oleh lembaga/pusat/organisasi penelitian lainnya di daerah',
                'r6' => 'Fasilitasi dan pembinaan untuk apresiasi prestasi inovasi',
                'r7' => 'Penyusunan kebijakan di bidang invensi dan inovasi',
                'r8' => 'Fasilitasi dan pembinaan untuk pengelolaan kebun raya daerah',
                'r9' => 'Fasilitasi dan pembinaan untuk pengembangan perusahaan pemula berbasis riset',
            ],
        ],
        'umpeg' => [
            'label' => 'Umum & Kepegawaian',
            'rhks'  => [
                'r1'  => 'Pembinaan, pengawasan, dan pengendalian barang milik daerah pada SKPD',
                'r2'  => 'Pengadaan pakaian dinas beserta atribut kelengkapannya',
                'r3'  => 'Pendidikan dan pelatihan pegawai berdasarkan tugas dan fungsi',
                'r4'  => 'Penyediaan komponen instalasi listrik/penerangan bangunan kantor',
                'r5'  => 'Penyediaan bahan logistik kantor',
                'r6'  => 'Penyediaan barang cetakan dan penggandaan',
                'r7'  => 'Penyediaan bahan bacaan dan peraturan perundang-undangan',
                'r8'  => 'Penyelenggaraan rapat koordinasi dan konsultasi SKPD',
                'r9'  => 'Pengadaan peralatan dan mesin lainnya',
                'r10' => 'Penyediaan jasa komunikasi, sumber daya air, dan listrik',
                'r11' => 'Penyediaan jasa peralatan dan perlengkapan kantor',
                'r12' => 'Penyediaan jasa pelayanan umum kantor',
                'r13' => 'Penyediaan jasa pemeliharaan, biaya pemeliharaan, dan pajak kendaraan perorangan dinas atau kendaraan dinas jabatan',
                'r14' => 'Penyediaan jasa pemeliharaan, biaya pemeliharaan, pajak dan perizinan kendaraan dinas operasional atau lapangan',
                'r15' => 'Pemeliharaan peralatan dan mesin lainnya',
                'r16' => 'Pemeliharaan/Rehabilitasi sarana dan prasarana gedung kantor atau bangunan lainnya',
            ],
        ],
        'renkeu' => [
            'label' => 'Perencanaan & Keuangan',
            'rhks'  => [
                'r1'  => 'Penyusunan dokumen perencanaan perangkat daerah',
                'r2'  => 'Koordinasi dan penyusunan dokumen RKA-SKPD',
                'r3'  => 'Koordinasi dan penyusunan dokumen perubahan RKA-SKPD',
                'r4'  => 'Koordinasi dan penyusunan DPA-SKPD',
                'r5'  => 'Koordinasi dan penyusunan perubahan DPA-SKPD',
                'r6'  => 'Koordinasi dan penyusunan laporan capaian kinerja dan ikhtisar realisasi kinerja SKPD',
                'r7'  => 'Evaluasi kinerja perangkat daerah',
                'r8'  => 'Penyediaan gaji dan tunjangan ASN',
                'r9'  => 'Penyediaan administrasi pelaksanaan tugas ASN',
                'r10' => 'Pelaksanaan penatausahaan dan pengujian/verifikasi keuangan SKPD',
                'r11' => 'Koordinasi dan penyusunan laporan keuangan akhir tahun SKPD',
                'r12' => 'Koordinasi dan penyusunan laporan keuangan bulanan/triwulan/semesteran SKPD',
            ],
        ],
    ],

    // daftar opsi kategori untuk filter sederhana (urut sesuai selera)
    'category_order' => ['4p','inovasi','umpeg','renkeu'],
];
