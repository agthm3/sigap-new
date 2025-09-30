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
                'indikator'   => 'Penggunaan IT *',
                'keterangan'  => 'Penggunaan IT dalam pelaksanaan Inovasi yang diterapkan',
                'jenis_file'  => 'Dokumen/Foto/Gambar',
                'hint'        => 'Dibuktikan dengan foto kegiatan/gambar screenshot layar (pdf/jpeg/jpg/png).',
                'params'      => [
                    ['label'=>'Pelaksanaan kerja sudah didukung sistem informasi online/daring', 'weight'=>3],
                    ['label'=>'Pelaksanaan kerja secara elektronik', 'weight'=>2],
                    ['label'=>'Pelaksanaan kerja secara manual/non elektronik', 'weight'=>1],
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
                    ['label'=>'Lebih dari 30 SDM', 'weight'=>3],
                    ['label'=>'11-30 SDM', 'weight'=>2],
                    ['label'=>'1-10 SDM', 'weight'=>1],
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
                    ['label'=>'Anggaran dialokasi pada kegiatan penerapan inovasi di T-0, T-1, dan T-2', 'weight'=>3],
                    ['label'=>'Anggaran dialokasi pada kegiatan penerapan inovasi di T-1 atau T-2', 'weight'=>2],
                    ['label'=>'Anggaran dialokasikan pada kegiatan penerapan inovasi di T-0 (Tahun berjalan)', 'weight'=>1],
                    ['label'=>'Tidak tercantum', 'weight'=>0],
                ],
            ],
            [
                'no' => 4,
                'indikator'   => 'Program dan kegiatan inovasi perangkat daerah dalam RKPD',
                'keterangan'  => 'Inovasi Perangkat Daerah telah dituangkan dalam program pembangunan daerah',
                'jenis_file'  => 'Dokumen/Foto/Gambar',
                'hint'        => 'Pilih tahun RKPD yang memuat program kegiatan inovasi daerah.
                                Dibuktikan dengan bab, bagian, dan halaman dokumen RKPD yang memuat program dan kegiatan inovasi daerah (pdf)',
                'params'      => [
                    ['label'=>'Pemerintah daerah sudah menuangkan program inovasi daerah dalam RKPD T-1, T-2 dan T0 (T0 adalah tahun berjalan)', 'weight'=>3],
                    ['label'=>'Pemerintah  daerah  sudah  menuangkan  program inovasi daerah dalam RKPD T-1 dan T-2', 'weight'=>2],
                    ['label'=>'Pemerintah daerah sudah menuangkan program inovasi daerah dalam RKPD T-1 atau T-2', 'weight'=>1],
                    ['label'=>'Tidak Ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 5,
                'indikator'   => 'Bimtek Inovasi',
                'keterangan'  => 'Peningkatan kapasitas pelaksana inovasi',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'SK/Surat Tugas/Sertifikat/Undangan + daftar hadir.',
                'params'      => [
                    ['label'=>'Dalam 2 tahun terakhir penrha lebih dari 2 kali bimtek (bimtbek, training dan TOT)', 'weight'=>3],
                    ['label'=>'Dalam 2 tahun terakhir pernah 2 kali bimtek (bimtek, training dan TOT)', 'weight'=>2],
                    ['label'=>'Dalam 2 tahun terakhir pernah 1 kali kegiatan transfer pengetahun (bimtek, sharing, FGD, atau kegiatan
                    transfer pengetahuan yang lain)', 'weight'=>1],
                    ['label'=>'Belum ada bimtek', 'weight'=>0],
                ],
            ],
            [
                'no' => 6,
                'indikator'   => 'Pelaksanaan Inovasi Daerah',
                'keterangan'  => 'Penetapan tim pelaksana inovasi daerah',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'Dibuktikan dengan SK Penetapan oleh Kepala Daerah/Kepala Perangkat Daerah.',
                'params'      => [
                    ['label'=>'Ada pelaksana dan ditetapkan dengan SK Kepala Daerah', 'weight'=>3],
                    ['label'=>'Ada pelaksana dan ditetapkan dengan SK Kepala Perangkat Daerah', 'weight'=>2],
                    ['label'=>'Ada pelaksana namun tidak ditetapkan dengan SK Kepala Perangkat Daerah', 'weight'=>1],
                    ['label'=>'Tidak tercantum', 'weight'=>0],
                ],
            ],
            [
                'no' => 7,
                'indikator'   => 'Keterlibatan Aktor Inovasi',
                'keterangan'  => 'Keikutsertaan unsur Stakeholder dalam pelaksanaan inovasi daerah (T-1 dan T-2)',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'Dibuktikan dengan Surat Keputusan Perangkat Daerah/Undangan rapat dalam 2 (dua) tahun terakhir (pdf).',
                'params'      => [
                    ['label'=>'>Inovasi melibatkan lebih dari 5 unsur aktor', 'weight'=>3],
                    ['label'=>'Inovasi melibatkan 4 aktor', 'weight'=>2],
                    ['label'=>'Inovasi melibatkan 3 aktor', 'weight'=>1],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 8,
                'indikator'   => 'Online Sistem',
                'keterangan'  => 'Perangkat jaringan prosedur yang dibuat secara daring',
                'jenis_file'  => 'Dokumen/Foto/Gambar',
                'hint'        => 'Dibuktikan dengan screenshot aplikasi layanan inovasi (jpg/jpeg/png)',
                'params'      => [
                    ['label'=>'Ada  dukungan  melalui  perangkat web  aplikasi  dan  aplikasi  mobile (android atau ios)', 'weight'=>3],
                    ['label'=>'Ada dukungan melalui web aplikasi', 'weight'=>2],
                    ['label'=>'Ada    dukungan    melalui informasi website atau sosial media', 'weight'=>1],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 9,
                'indikator'   => 'Jejaring Inovasi',
                'keterangan'  => 'Jumlah Perangkat Daerah yang terlibat dalam penerapan inovasi (dalam 2 tahun terakhir)',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'Dibuktikan dengan SK/ST tim pengelola penerapan inovasi daerah dalam 2 (dua) tahun terakhir (pdf)',
                'params'      => [
                    ['label'=>'Inovasi melibatkan 5 atau lebih perangkat daerah', 'weight'=>3],
                    ['label'=>'Inovasi melibatkan 3-4 perangkat daerah', 'weight'=>2],
                    ['label'=>'Inovasi melibatkan 1-2 perangkat daerah atau lebih', 'weight'=>1],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 10,
                'indikator'   => 'Sosialisasi Inovasi Daerah',
                'keterangan'  => 'Penyebarluasan informasi kebijakan inovasi daerah',
                'jenis_file'  => 'Dokumen/Foto/Gambar',
                'hint'        => 'Dibuktikan dengan dokumentasi dan publikasi (foto kegiatan/seminar/display pameran inovasi atau screenshot konten pada media sosial/website atau pemberitaan media massa cetak/elektronik) (jpeg/jpg/png',
                'params'      => [
                    ['label'=>'Media berita', 'weight'=>3],
                    ['label'=>'Konten melalui media sosial', 'weight'=>2],
                    ['label'=>'Foto kegiatan yang berlatar belakang spanduk kegiatan inovasi', 'weight'=>1],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 11,
                'indikator'   => 'Pedoman Teknis',
                'keterangan'  => 'Ketentuan dasar penggunaan inovasi daerah berupa buku petunjuk/manual book',
                'jenis_file'  => 'Dokumen/Foto/Gambar',
                'hint'        => 'Manual book (pdf) atau screenshot penggunaan.',
                'params'      => [
                    ['label'=>'Telah terdapat pedoman teknis berupa buku yang dapat diakses secara online', 'weight'=>3],
                    ['label'=>'Telah terdapat pedoman teknis berupa buku dalam bentuk elektronik', 'weight'=>2],
                    ['label'=>'Telah terdapat pedoman teknis berupa buku manual', 'weight'=>1],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 12,
                'indikator'   => 'Kemudahan Informasi Layanan',
                'keterangan'  => 'Manual/Hotline/Media Sosial/Layanan Online',
                'jenis_file'  => 'Dokumen/Foto/Gambar',
                'hint'        => 'Dibuktikan dengan Nomor layanan telp/ screenshot email/akun media sosial/nama aplikasi online/dokumen foto buku tamu layanan (pdf/jpeg/jpg/png).',
                'params'      => [
                    ['label'=>'Layanan melalui aplikasi online', 'weight'=>3],
                    ['label'=>'Layanan email/media sosial', 'weight'=>2],
                    ['label'=>'Layanan telp atau tetap muka langsung/noken', 'weight'=>1],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 13,
                'indikator'   => 'Kemudahan Proses Inovasi yang dihasilkan',
                'keterangan'  => 'Indikator ini ditujukan untuk mengukur kecepatan layanan inovasi yang diperoleh oleh pengguna.',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'Dibuktikan dengan SOP pelaksanaan inovasi daerah yang memuat durasi waktu layanan (pdf).',
                'params'      => [
                    ['label'=>'Hasil inovasi diperoleh dalam waktu 1 hari', 'weight'=>3],
                    ['label'=>'Hasil inovasi diperoleh dalam waktu 2-5 hari', 'weight'=>2],
                    ['label'=>'Hasil inovasi diperoleh dalam waktu 6 hari atau lebih', 'weight'=>1],
                    ['label'=>'Tidak ada SOP', 'weight'=>0],
                ],
            ],
            [
                'no' => 14,
                'indikator'   => 'Penyelesaian Layanan Pengaduan',
                'keterangan'  => 'Rasio pengaduan yang tertangani dalam tahun terakhir, meliputi keluhan, kritik konstruktif, saran, dan pengaduan lainnya terkait layanan inovasi.',
                'jenis_file'  => 'Dokumen/Foto/Gambar',
                'hint'        => 'Dibuktikan dengan dokumen foto kegiatan penyelesaian pengaduan/screenshot media layanan pengaduan yang disertai dengan rekapitulasi pengaduan dan persentase rasio penyelesaian pengaduan.',
                'params'      => [
                    ['label'=>'>71%', 'weight'=>3],
                    ['label'=>'41% sampai dengan 70%', 'weight'=>2],
                    ['label'=>'<40% tidak ada pengaduan', 'weight'=>1],
                    ['label'=>'Tidak ada pengelolaan', 'weight'=>0],
                ],
            ],
            [
                'no' => 15,
                'indikator'   => 'Layanan Terintegrasi',
                'keterangan'  => 'Integrasi & interoperabilitas',
                'jenis_file'  => 'Foto/Gambar',
                'hint'        => 'a) Digital: screenshot beranda & proses/superApps; b) Non-digital: foto/dokumen integrasi.',
                'params'      => [
                    ['label'=>'Digital: terhubung (superApps/APIs)', 'weight'=>10],
                    ['label'=>'Digital: integrasi terbatas', 'weight'=>6],
                    ['label'=>'Non-digital: integrasi alur layanan', 'weight'=>5],
                    ['label'=>'Tidak terintegrasi', 'weight'=>0],
                ],
            ],
            [
                'no' => 16,
                'indikator'   => 'Replikasi',
                'keterangan'  => 'Direplikasi oleh daerah lain',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'Dibuktikan dengan dokumen PKS/MoU/Surat Pernyataan dari pemda yang mereplikasi /dokumen replikasi lainnya (pdf)',
                'params'      => [
                    ['label'=>'Pernah 3 kali direplikasi di daerah lain yang berbeda', 'weight'=>3],
                    ['label'=>'Pernah 2 kali direplikasi di daerah lain yang berbeda', 'weight'=>2],
                    ['label'=>'Pernah 1 kali direplikasi daerah lain', 'weight'=>1],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 17,
                'indikator'   => 'Kecepatan Penciptaan Inovasi *',
                'keterangan'  => 'Durasi penciptaan inovasi kompleks',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'Dibuktikan dengan dokumen/laporan/proposal inovasi daerah yang memuat tahapan-tahapan proses dan durasi penciptaan inovasi daerah (pdf)..',
                'params'      => [
                    ['label'=>'Inovasi dapat diciptakan dalam waktu 1-4 bulan', 'weight'=>3],
                    ['label'=>'Inovasi dapat diciptakan dalam waktu 5-8 bulan', 'weight'=>2],
                    ['label'=>'Inovasi dapat diciptakan dalam waktu 9 bulan keatas', 'weight'=>1],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 18,
                'indikator'   => 'Kemanfaatan Inovasi *',
                'keterangan'  => 'Jumlah pengguna/penerima manfaat',
                'jenis_file'  => 'Dokumen/Foto/Gambar',
                'hint'        => 'Daftar penerima (pdf) atau screenshot jumlah pengguna (jpg/png).',
                'params'      => [
                    ['label'=>'Jumlah pengguna atau penerima manfaat 201 orang keatas', 'weight'=>3],
                    ['label'=>'Jumlah pengguna atau penerima manfaat 100-200 orang', 'weight'=>2],
                    ['label'=>'Jumlah pengguna atau penerima manfaat 1-100 orang', 'weight'=>1],
                    ['label'=>'Tidak ada data', 'weight'=>0],
                ],
            ],
            [
                'no' => 19,
                'indikator'   => 'Monitoring & Evaluasi Inovasi Daerah',
                'keterangan'  => 'Kepuasan pelaksanaan penggunaan inovasi daerah',
                'jenis_file'  => 'Dokumen/Foto/Gambar',
                'hint'        => 'Dibuktikan dengan screenshot testimoni pengguna (jpeg/jpg/png) atau laporan survei kepuasan masyarakat/laporan hasil penelitian (pdf)',
                'params'      => [
                    ['label'=>'Hasil laporan monev eksternal berdasarkan hasil penelitian/kajian/analisis', 'weight'=>3],
                    ['label'=>'Hasil pengukuran kepuasan pengguna dari evaluasi Survei kepuasan masyarakat', 'weight'=>2],
                    ['label'=>'Hasil laporan monev internal perangkat daerah', 'weight'=>1],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 20,
                'indikator'   => 'Kualitas Inovasi Daerah *',
                'keterangan'  => 'Kualitas inovasi daerah dapat dibuktikan dengan video penerapan inovasi daerah',
                'jenis_file'  => 'Upload Video .mp4',
                'hint'        => 'Unsur Video Inovasi Daerah meliputi:
                                Latar Belakang Inovasi;
                                Penjaringan Ide Inovasi;
                                Pemilihan Ide;
                                Manfaat;dan
                                Dampak
                                .',
                'params'      => [
                    ['label'=>'Memenuhi 5 unsur substansi', 'weight'=>3],
                    ['label'=>'Memenuhi 3 atau 4 unsur substansi', 'weight'=>2],
                    ['label'=>'Memenuhi 1 atau 2 unsur substansi', 'weight'=>1],
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
