<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class EvidenceTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Bersihkan tabel (hapus params dulu baru templates)
        DB::table('evidence_template_params')->delete();
        DB::table('evidence_templates')->delete();

        $items = [
            [
                'no' => 1,
                'indikator'   => 'REGULASI INOVASI DAERAH*',
                'keterangan'  => 'REGULASI YANG MENETAPKAN NAMA-NAMA INOVASI DAERAH YANG MENJADI LANDASAN OPERASIONAL PENERAPAN INOVASI DAERAH',
                'jenis_file'  => 'Dokumen/Foto/Gambar',
                'hint'        => 'PERDA ATAU PERKADA ATAU SK KEPALA DAERAH ATAU SK KEPALA PERANGKAT DAERAH SERTA HALAMAN YANG MEMUAT NAMA INOVASI YANG SAH DAN VALID SERTA SESUAI PADA TAHUN SAAT PENERAPAN (PDF)',
                'params'      => [
                    ['label'=>'PERATURAN KEPALA DAERAH / PERATURAN DAERAH⭐⭐⭐', 'weight'=>3],
                    ['label'=>'SK KEPALA DAERAH⭐⭐', 'weight'=>2],
                    ['label'=>'SK KEPALA PERANGKAT DAERAH⭐', 'weight'=>1],
                    ['label'=>'Tidak Ada/Belum Ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 2,
                'indikator'   => 'Ketersediaan SDM Terhadap Inovasi Daerah *',
                'keterangan'  => 'Jumlah SDM yang mengelola inovasi (2 tahun terakhir)',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'SK/Surat Penugasan/Surat Perintah yang disahkan.',
                'params'      => [
                    ['label'=>'Lebih dari 30 SDM⭐⭐⭐', 'weight'=>3],
                    ['label'=>'11-30 SDM⭐⭐', 'weight'=>2],
                    ['label'=>'1-10 SDM⭐', 'weight'=>1],
                    ['label'=>'Tidak Ada/Belum Ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 3,
                'indikator'   => 'Dukungan Anggaran',
                'keterangan'  => 'Anggaran inovasi daerah dalam APBD dengan tahapan penerapan (penyediaan sarana prasarana, 
                                  sumber daya manusia dan layanan, bimtek, urusan jenis layanan). Penerapan inovasi yang 
                                  dilakukan sudah menjadi bagian dari kegiatan yang mendapatkan alokasi anggaran.',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'Dokumen anggaran memuat program/kegiatan inovasi sesuai tahun anggaran.',
                'params'      => [
                    ['label'=>'Anggaran dialokasi pada kegiatan penerapan inovasi di T-0, T-1, dan T-2⭐⭐⭐', 'weight'=>3],
                    ['label'=>'Anggaran dialokasi pada kegiatan penerapan inovasi di T-1 atau T-2⭐⭐', 'weight'=>2],
                    ['label'=>'Anggaran dialokasikan pada kegiatan penerapan inovasi di T-0 (Tahun berjalan)⭐', 'weight'=>1],
                    ['label'=>'Tidak tercantum', 'weight'=>0],
                ],
            ],
            [
                'no' => 4,
                'indikator'   => 'BIMTEK INOVASI',
                'keterangan'  => 'PENINGKATAN KAPASITAS DAN KOMPETENSI PELAKSANA INOVASI DAERAH',
                'jenis_file'  => 'Dokumen/Foto/Gambar',
                'hint'        => 'SK KEGIATAN/SURAT TUGAS, DAFTAR HADIR, DAN UNDANGAN BIMTEK ATAU KEGIATAN TRANSFER PENGETAHUAN (PDF) SERTA BUKTI DUKUNG DARI SEJUMLAH FREKUENSI PELAKSANAAN BIMTEK.',
                'params'      => [
                    ['label'=>'DALAM 2 TAHUN TERAKHIR PERNAH LEBIH DARI 2 KALI BIMTEK (BIMTEK,TRAINING DAN TOT)⭐⭐⭐', 'weight'=>3],
                    ['label'=>'DALAM 2 TAHUN TERAKHIR PERNAH 2 KALI BIMTEK  (BIMTEK, TRAINING DAN TOT)⭐⭐', 'weight'=>2],
                    ['label'=>'DALAM 2 TAHUN TERAKHIR PERNAH 1 KALI KEGIATAN TRANSFER PENGETAHUAN (BIMTEK, SHARING, FGD, ATAU KEGIATAN TRANSFER PENGETAHUAN YANG LAIN)⭐', 'weight'=>1],
                    ['label'=>'Tidak Ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 5,
                'indikator'   => 'INTEGRASI PROGRAM DAN KEGIATAN INOVASI DALAM RKPD',
                'keterangan'  => 'INOVASI PERANGKAT DAERAH TELAH DITUANGKAN DALAM PROGRAM PEMBANGUNAN DAERAH',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'UPLOAD BAB, BAGIAN, DAN HALAMAN DOKUMEN RKPD YANG MEMUAT INOVASI DAERAH DALAM PROGRAM DAN KEGIATAN (PDF)
',
                'params'      => [
                    ['label'=>'PEMERINTAH DAERAH SUDAH MENUANGKAN PROGRAM INOVASI DAERAH DALAM RKPD T-1, T-2 DAN T0⭐⭐⭐', 'weight'=>3],
                    ['label'=>'PEMERINTAH DAERAH SUDAH MENUANGKAN PROGRAM INOVASI DAERAH DALAM RKPD T-1 DAN T-2⭐⭐', 'weight'=>2],
                    ['label'=>'PEMERINTAH DAERAH SUDAH MENUANGKAN PROGRAM INOVASI DAERAH DALAM RKPD T-1 ATAU T-2⭐', 'weight'=>1],
                    ['label'=>'Belum ada bimtek', 'weight'=>0],
                ],
            ],
            [
                'no' => 6,
                'indikator'   => 'KETERLIBATAN AKTOR INOVASI',
                'keterangan'  => 'KEIKUTSERTAAN UNSUR STAKEHOLDER DALAM PELAKSANAAN INOVASI DAERAH (T-1 DAN T-2)',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'SURAT KEPUTUSAN PERANGKAT DAERAH/UNDANGAN RAPAT DALAM 2 (DUA) TAHUN TERAKHIR (PDF)',
                'params'      => [
                    ['label'=>'INOVASI MELIBATKAN LEBIH DARI 5 AKTOR⭐⭐⭐', 'weight'=>3],
                    ['label'=>'INOVASI MELIBATKAN 4 AKTOR⭐⭐', 'weight'=>2],
                    ['label'=>'INOVASI MELIBATKAN 3 AKTOR⭐', 'weight'=>1],
                    ['label'=>'Tidak tercantum', 'weight'=>0],
                ],
            ],
            [
                'no' => 7,
                'indikator'   => 'PELAKSANA INOVASI DAERAH',
                'keterangan'  => 'PENETAPAN TIM PELAKSANA INOVASI DAERAH ',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'SK PENETAPAN OLEH KEPALA DAERAH/KEPALA OPD (PDF).',
                'params'      => [
                    ['label'=>'ADA PELAKSANA DAN DITETAPKAN DENGAN SK KEPALA DAERAH⭐⭐⭐', 'weight'=>3],
                    ['label'=>'ADA PELAKSANA DAN DITETAPKAN DENGAN SK KEPALA PERANGKAT DAERAH⭐⭐', 'weight'=>2],
                    ['label'=>'ADA PELAKSANA NAMUN TIDAK DITETAPKAN DENGAN SK KEPALA PERANGKAT DAERAH⭐', 'weight'=>1],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 8,
                'indikator'   => 'JEJARING INOVASI DAERAH',
                'keterangan'  => 'JUMLAH PERANGKAT DAERAH YANG TERLIBAT DALAM PENERAPAN INOVASI (DALAM 2 TAHUN TERAKHIR)',
                'jenis_file'  => 'Dokumen/Foto/Gambar',
                'hint'        => 'SK/ST TIM PENGELOLA PENERAPAN INOVASI DAERAH DALAM 2 (DUA) TAHUN TERAKHIR (PDF)',
                'params'      => [
                    ['label'=>'INOVASI MELIBATKAN 5 ATAU LEBIH PERANGKAT DAERAH⭐⭐⭐', 'weight'=>3],
                    ['label'=>'INOVASI MELIBATKAN 3-4 PERANGKAT DAERAH⭐⭐', 'weight'=>2],
                    ['label'=>'INOVASI MELIBATKAN 1-2 PERANGKAT DAERAH ATAU LEBIH⭐', 'weight'=>1],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 9,
                'indikator'   => 'SOSIALISASI INOVASI DAERAH',
                'keterangan'  => 'PENYEBARLUASAN INFORMASI KEBIJAKAN INOVASI DAERAH  ',
                'jenis_file'  => 'Dokumen/Foto/Gambar',
                'hint'        => 'DOKUMENTASI DAN PUBLIKASI (FOTO KEGIATAN/SEMINAR/DISPLAY PAMERAN INOVASI ATAU SCREENSHOT KONTEN PADA MEDIA SOSIAL/WEBSITE ATAU PEMBERITAAN MEDIA MASSA CETAK/ELEKTRONIK)
(FORMAT DOK : JPEG/JPG/PNG)',
                'params'      => [
                    ['label'=>'MEDIA BERITA ⭐⭐⭐', 'weight'=>3],
                    ['label'=>'KONTEN MELALUI MEDIA SOSIAL ⭐⭐', 'weight'=>2],
                    ['label'=>'FOTO KEGIATAN YANG BERLATAR BELAKANG SPANDUK KEGIATAN INOVASI YANG DITERAPKAN⭐', 'weight'=>1],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 10,
                'indikator'   => 'PEDOMAN TEKNIS',
                'keterangan'  => 'KETENTUAN DASAR PENGGUNAAN INOVASI DAERAH BERUPA BUKU PETUNJUK / MANUAL BOOK',
                'jenis_file'  => 'Dokumen/Foto/Gambar',
                'hint'        => 'DOKUMEN MANUAL BOOK/BUKU PETUNJUK (PDF) ATAU SCREENSHOT PENGGUNAAN INOVASI DAERAH (JPG/JPEG/PNG)                    ',
                'params'      => [
                    ['label'=>'TELAH TERDAPAT PEDOMAN TEKNIS BERUPA BUKU YANG DAPAT DIAKSES SECARA ONLINE  ⭐⭐⭐', 'weight'=>3],
                    ['label'=>'TELAH TERDAPAT PEDOMAN TEKNIS BERUPA BUKU DALAM BENTUK ELEKTRONIK ⭐⭐', 'weight'=>2],
                    ['label'=>'TELAH TERDAPAT PEDOMAN TEKNIS BERUPA BUKU MANUAL / CETAK ⭐', 'weight'=>1],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 11,
                'indikator'   => 'KUANTITAS / JUMLAH MEDIA INFORMASI LAYANAN',
                'keterangan'  => 'JUMLAH MEDIA YANG DIGUNAKAN DALAM DALAM PENERAPAN INOVASI  ',
                'jenis_file'  => 'Dokumen/Foto/Gambar',
                'hint'        => 'BUKU TAMU DAN FOTO PERTEMUAN PELAYANAN
PICSCREEN EMAIL, EMAIL LAYANAN ATAU NO TELP LAYANAN
PICSCREEN MEDIA SOSIAL YANG DIGUNAKAN
PICSCREEN APLIKASI (WEB & ANDROID/IOS) YANG DIGUNAKAN ',
                'params'      => [
                    ['label'=>'Parameter 3 : LAYANAN MELALUI 3  MEDIA ATAU LEBIH (3/4 ATAU 4/4) 
⭐⭐⭐ ', 'weight'=>3],
                    ['label'=>'Parameter 2 : LAYANAN MELALUI 2 DARI 4 MEDIA (2/4)⭐⭐', 'weight'=>2],
                    ['label'=>'Parameter 1 : LAYANAN MELALUI 1 DARI 4 MEDIA (¼)
⭐', 'weight'=>1],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 12,
                'indikator'   => 'KEMUDAHAN PROSES INOVASI YANG DIHASILKAN',
                'keterangan'  => 'INDIKATOR INI DITUJUKAN UNTUK MENGUKUR KECEPATAN LAYANAN YANG DIPEROLEH OLEH PENGGUNA',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'SOP PELAKSANAAN INOVASI DAERAH YANG MEMUAT DURASI WAKTU LAYANAN (PDF).',
          'params'      => [
                    ['label'=>'HASIL INOVASI DIPEROLEH DALAM WAKTU 1 HARI ⭐⭐⭐', 'weight'=>3],
                    ['label'=>'HASIL INOVASI DIPEROLEH DALAM WAKTU 2-5 HARI ⭐⭐', 'weight'=>2],
                    ['label'=>'HASIL INOVASI DIPEROLEH DALAM WAKTU 6 HARI ATAU LEBIH⭐', 'weight'=>1],
                    ['label'=>'Tidak ada SOP', 'weight'=>0],
                ],
            ],
            [
                'no' => 13,
                'indikator'   => 'INTEGRASI LAYANAN',
                'keterangan'  => 'INOVASI DIBANGUN SECARA TERPADU, MENGEDEPANKAN PRINSIP-PRINSIP INTEGRASI FUNGSI LAYANAN DAN INTEROPERABILITAS LAYANAN',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'EVIDEN : DARING  PICSCREEN DUKUNGAN APLIKASI LAYANAN/MEDSOS YANG TERINTEGRASI (SUPER APPS)
	 LURING  DOKUMEN PROGRAM TERINTEGRASI ATAU DATA YANG SUDAH TERINTEGRASI',
                'params'      => [
                    ['label'=>'[DARING]ADA DUKUNGAN MELALUI WEB APLIKASI/ MOBILE (ANDROID/ IOS) YG LAYANAN SUDAH TERINTEGRASI DGN UNIT ORGANISASI LAIN
                    [LURING]LAYANAN TELAH TERINTEGRASI DENGAN LAYANAN LAIN PADA PROGRAM ATAU KEGIATAN PADA UNIT ORGANISASI LAIN ATAU DALAM LEBIH DARI SATU URUSAN PEMERINTAHAN.
                    ⭐⭐⭐', 'weight'=>3],
                    ['label'=>'[DARING] ADA DUKUNGAN MELALUI INFORMASI WEBSITE, SOSIAL MEDIA, WEB APLIKASI/MOBILE (ANDROID/IOS) YG TELAH  TERINTEGRASI DALAM SATU PORTAL PADA UNIT ORGANISASI BERSANGKUTAN
                    [LURING]LAYANAN TELAH TERINTEGRASI DENGAN LAYANAN LAIN PADA PROGRAM ATAU KEGIATAN LAIN PADA SATU UNIT ORGANISASI ATAU DALAM SATU URUSAN PEMERINTAHAN.
                    ⭐⭐', 'weight'=>2],
                    ['label'=>'[DARING]ADA DUKUNGAN MELALUI INFORMASI WEBSITE/SOSIAL MEDIA/WEB APLIKASI/MOBILE (ANDROID/ IOS) YG BERJALAN TERPISAH
                    [LURING]LAYANAN INOVASI BERJALAN SECARA TERSENDIRI (MANDIRI/INDEPENDEN ⭐', 'weight'=>1],
                    ['label'=>'Tidak ada pengelolaan', 'weight'=>0],
                ],
            ],
            [
                'no' => 14,
                'indikator'   => 'REPLIKASI',
                'keterangan'  => 'INOVASI DAERAH TELAH DIREPLIKASI OLEH PEMERINTAH DAERAH LAIN ',
                'jenis_file'  => 'Foto/Gambar',
                'hint'        => 'DOKUMEN PKS/MOU/SURAT PERNYATAAN DARI PEMDA YANG MEREPLIKASI /DOKUMEN REPLIKASI LAINNYA (PDF)',
                'params'      => [
                    ['label'=>'PERNAH 3 KALI DIREPLIKASI DI DAERAH LAIN YANG BERBEDA ⭐⭐⭐', 'weight'=>10],
                    ['label'=>'PERNAH 2 KALI DIREPLIKASI DI DAERAH LAIN YANG BERBEDA⭐⭐', 'weight'=>6],
                    ['label'=>'PERNAH 1 KALI DIREPLIKASI DI DAERAH LAIN

⭐', 'weight'=>5],
                    ['label'=>'Tidak terintegrasi', 'weight'=>0],
                ],
            ],
            [
                'no' => 15,
               'indikator'   => 'ALAT KERJA',
                'keterangan'  => 'PENGUNAAN IT DALAM INOVASI YANG DITERAPKAN ',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'PICSCREEN PALTFORM DARING /FOTO/PICSCREEN PERNAGKAT ELEKTRONIK/No. TELP/ FOTO TATAP MUKA DAN FOTO LAYANAN MANUAL',
                'params'      => [
                    ['label'=>'PELAKSANAAN KERJA SUDAH DIDUKUNG SISTEM INFORMASI ONLINE/ DARING 
Contoh : pemanfaatan platform media sosial, AI, IoT, super-app, dll.
⭐⭐⭐', 'weight'=>3],
                    ['label'=>'PELAKSANAAN KERJA DIDUKUNG DENGAN PERANGKAT ELEKTRONIK Contoh : mesin edc, telp. ⭐⭐', 'weight'=>2],
                    ['label'=>'PELAKSANAAN KERJA SECARA MANUAL/NON ELEKTRONIK, Contoh : tatap muka/jemput bola/noken
⭐', 'weight'=>1],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 16,
                'indikator'   => 'KEMANFAATAN INOVASI DAERAH *',
                'keterangan'  => 'JUMLAH PENGGUNA ATAU PENERIMA MANFAAT INOVASI DAERAH',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'DOKUMEN/ LAPORAN/PROPOSAL INOVASI DAERAH YANG MEMUAT TAHAPAN-TAHAPAN PROSES DAN DURASI PENCIPTAAN INOVASI DAERAH (PDF)..',
                'params'      => [
                    ['label'=>'JUMLAH PENGGUNA ATAU PENERIMA MANFAAT 201 ORANG KEATAS / % PENINGKATAN JUMLAH UNIT 50 % / EFISIENSI BELANJA SEBESAR 20,1 -30 % / EFISIENSI BELANJA SEBESAR LEBI DARI SAMA DENGAN 100 % / JUMLAH PRODUK YANG DIHASILKAN ATAU DIPERJUALBELIKAN 201 ORANG ATAU LEBIH⭐⭐⭐', 'weight'=>3],
                    ['label'=>'JUMLAH PENGGUNA ATAU PENERIMA MANFAAT 101-200 ORANG / % PENINGKATAN JUMLAH UNIT 20,1 - 50 % / EFISIENSI BELANJA SEBESAR 10,1 -20 % / PENINGKATAN PENDAPATAN SEBESAR 50 -99,99 % / JUMLAH PRODUK YANG DIHASILKAN ATAU DIPERJUALBELIKAN 101-200 ORANG⭐⭐', 'weight'=>2],
                    ['label'=>'JUMLAH PENGGUNA ATAU PENERIMA MANFAAT 1-100 ORANG / % PENINGKATAN JUMLAH UNIT 5 -20 % / EFISIENSI BELANJA SEBESAR 0,1 -10 % / PENINGKATAN PENDAPATAN SEBESAR 0,1 – 49,99 % / JUMLAH PRODUK YANG DIHASILKAN ATAU DIPERJUALBELIKAN 1-100 ORANG⭐', 'weight'=>1],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 17,
                'indikator'   => 'KECEPATAN PENCIPTAAN INOVASI DAERAH',
                'keterangan'  => 'RENTANG WAKTU YANG DIGUNAKAN UNTUK MENCIPTAKAN INOVASI DAERAH',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'DOKUMEN/ LAPORAN/PROPOSAL INOVASI DAERAH YANG MEMUAT TAHAPAN-TAHAPAN PROSES DAN DURASI PENCIPTAAN INOVASI DAERAH (PDF).
.',
                'params'      => [
                    ['label'=>'Inovasi dapat diciptakan dalam waktu 1-4 bulan⭐⭐⭐', 'weight'=>3],
                    ['label'=>'Inovasi dapat diciptakan dalam waktu 5-8 bulan⭐⭐', 'weight'=>2],
                    ['label'=>'Inovasi dapat diciptakan dalam waktu 9 bulan keatas⭐', 'weight'=>1],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 18,
                'indikator'   => 'PENYELESAIAN LAYANAN PENGADUAN',
                'keterangan'  => 'RASIO PENGADUAN YANG TERTANGANI DALAM TAHUN TERAKHIR, MELIPUTI KELUHAN, KRITIK KONSTRUKTIF, SARAN, DAN PENGADUAN LAINNYA TERKAIT LAYANAN INOVASI',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'DOKUMEN FOTO KEGIATAN PENYELESAIAN PENGADUAN/ SCREENSHOT MEDIA LAYANAN PENGADUAN YANG DISERTAI DENGAN REKAPITULASI PENGADUAN DAN PERSENTASE RASIO PENYELESAIAN PENGADUAN .',
                'params'      => [
                    ['label'=>'≥ 86%  ⭐⭐⭐', 'weight'=>3],
                    ['label'=>'51% S.D. 85%
                    ⭐⭐', 'weight'=>2],
                    ['label'=>'≤ 50%  TIDAK ADA PENGADUAN 
                    ⭐', 'weight'=>1],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 19,
                'indikator'   => 'MONITORING & EVALUASI
INOVASI DAERAH',
                'keterangan'  => 'KEPUASAN PELAKSANAAN PENGGUNAAN INOVASI DAERAH ',
                'jenis_file'  => 'Dokumen/Foto/Gambar',
                'hint'        => 'SCREENSHOT TESTIMONI PENGGUNA (JPEG/JPG/PNG) ATAU LAPORAN SURVEI KEPUASAN MASYARAKAT/LAPORAN HASIL PENELITIAN (PDF)
',
                'params'      => [
                    ['label'=>'Hasil laporan monev eksternal berdasarkan hasil penelitian/kajian/analisis ⭐⭐⭐ ', 'weight'=>3],
                    ['label'=>'Hasil pengukuran kepuasaan pengguna dari evaluasi Survei Kepuasan Masyarakat⭐⭐', 'weight'=>2],
                    ['label'=>'Hasil laporan monev internal Perangkat Daerah⭐', 'weight'=>1],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 20,
                'indikator'   => 'Kualitas Inovasi Daerah *',
                'keterangan'  => 'Kualitas inovasi daerah dapat dibuktikan dengan video penerapan inovasi daerah',
                'jenis_file'  => 'Dokumen',
                'hint'        => 'Unsur Video Inovasi Daerah meliputi:
                                Latar Belakang Inovasi;
                                Penjaringan Ide Inovasi;
                                Pemilihan Ide;
                                Manfaat;dan
                                Dampak
                                .',
                'params'      => [
                    ['label'=>'Memenuhi 5 unsur substansi⭐⭐⭐', 'weight'=>3],
                    ['label'=>'Memenuhi 3 atau 4 unsur substansi⭐⭐', 'weight'=>2],
                    ['label'=>'Memenuhi 1 atau 2 unsur substansi⭐', 'weight'=>1],
                    ['label'=>'Tidak ada video', 'weight'=>0],
                ],
            ],
        ];

        DB::transaction(function () use ($items, $now) {
            foreach ($items as $it) {
                $templateId = DB::table('evidence_templates')->insertGetId([
                    'no'          => $it['no'],
                    'indikator'   => $it['indikator'],
                    'keterangan'  => $it['keterangan'],
                    'jenis_file'  => $it['jenis_file'],
                    'hint'        => $it['hint'],
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);

                $rows = [];
                $order = 1;
                foreach ($it['params'] as $p) {
                    $rows[] = [
                        'template_id' => $templateId,
                        'label'       => $p['label'],
                        'weight'      => (int) $p['weight'],
                        'sort_order'  => $order++,
                        'created_at'  => $now,
                        'updated_at'  => $now,
                    ];
                }
                DB::table('evidence_template_params')->insert($rows);
            }
        });
    }
}
