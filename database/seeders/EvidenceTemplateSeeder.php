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
                'indikator'   => 'Regulasi Inovasi Daerah *',
                'keterangan'  => 'Regulasi yang menetapkan nama-nama inovasi daerah yang menjadi landasan operasional penerapan Inovasi Daerah',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'Halaman depan Perda/Perkada/SK yang memuat nama inovasi dan sesuai tahun penerapan.',
                'params'      => [
                    ['label'=>'Peraturan Daerah (Perda)', 'weight'=>10],
                    ['label'=>'Peraturan Kepala Daerah (Perkada)', 'weight'=>8],
                    ['label'=>'SK Kepala Daerah', 'weight'=>6],
                    ['label'=>'SK Kepala Perangkat Daerah', 'weight'=>5],
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
                    ['label'=>'SK Tim Pengelola (terbaru)', 'weight'=>10],
                    ['label'=>'Surat Penugasan/Perintah', 'weight'=>8],
                    ['label'=>'Dokumen tidak lengkap', 'weight'=>3],
                    ['label'=>'Tidak Ada/Belum Ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 3,
                'indikator'   => 'Dukungan Anggaran',
                'keterangan'  => 'Belanja yang mendukung penerapan inovasi pada program/kegiatan',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'Dokumen anggaran memuat program/kegiatan inovasi sesuai tahun anggaran.',
                'params'      => [
                    ['label'=>'Tercantum eksplisit (program & kegiatan)', 'weight'=>10],
                    ['label'=>'Tercantum sebagian/indikatif', 'weight'=>6],
                    ['label'=>'Tidak tercantum', 'weight'=>0],
                ],
            ],
            [
                'no' => 4,
                'indikator'   => 'Alat Kerja',
                'keterangan'  => 'Alat kerja pelaksanaan inovasi (manual/elektronik/online)',
                'jenis_file'  => 'Dokumen/Foto/Gambar',
                'hint'        => 'Foto/screenshot platform, AI, IoT, superApp, dsb.',
                'params'      => [
                    ['label'=>'Sistem online/daring/AI aktif', 'weight'=>10],
                    ['label'=>'Perangkat elektronik (non-online)', 'weight'=>6],
                    ['label'=>'Manual/non-elektronik', 'weight'=>3],
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
                    ['label'=>'Bimtek rutin dgn bukti lengkap', 'weight'=>10],
                    ['label'=>'Bimtek pernah dilakukan', 'weight'=>6],
                    ['label'=>'Belum ada bimtek', 'weight'=>0],
                ],
            ],
            [
                'no' => 6,
                'indikator'   => 'Integrasi Program & Kegiatan Inovasi dalam RKPD',
                'keterangan'  => 'Program inovasi dituangkan dalam RKPD',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'Dokumen RKPD yang memuat program & kegiatan inovasi.',
                'params'      => [
                    ['label'=>'Tercantum di RKPD T-2, T-1, dan T0', 'weight'=>10],
                    ['label'=>'Tercantum sebagian (mis. T-1 & T0)', 'weight'=>7],
                    ['label'=>'Tidak tercantum', 'weight'=>0],
                ],
            ],
            [
                'no' => 7,
                'indikator'   => 'Keterlibatan Aktor Inovasi',
                'keterangan'  => 'Keterlibatan stakeholder (Pentahelix) T-2/T-1',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'SK/Undangan/FGD/Kerja sama + keterangan unsur aktor.',
                'params'      => [
                    ['label'=>'>3 unsur aktor terlibat', 'weight'=>10],
                    ['label'=>'2–3 unsur aktor', 'weight'=>6],
                    ['label'=>'1 unsur atau tidak jelas', 'weight'=>3],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 8,
                'indikator'   => 'Pelaksana Inovasi Daerah',
                'keterangan'  => 'Penetapan tim pelaksana inovasi',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'SK/Surat Penugasan/Perintah yang disahkan.',
                'params'      => [
                    ['label'=>'SK Tim Pelaksana aktif', 'weight'=>10],
                    ['label'=>'Ada penugasan non-SK', 'weight'=>5],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 9,
                'indikator'   => 'Jejaring Inovasi',
                'keterangan'  => 'Jumlah PD terlibat (2 tahun)',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'SK/Surat Tugas tim pengelola/penerapan.',
                'params'      => [
                    ['label'=>'≥5 PD terlibat', 'weight'=>10],
                    ['label'=>'2–4 PD terlibat', 'weight'=>6],
                    ['label'=>'1 PD', 'weight'=>3],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 10,
                'indikator'   => 'Sosialisasi Inovasi Daerah',
                'keterangan'  => 'Penyebarluasan informasi/advokasi (2 tahun)',
                'jenis_file'  => 'Dokumen/Foto/Gambar',
                'hint'        => 'Dokumentasi kegiatan; konten medsos/website; berita media.',
                'params'      => [
                    ['label'=>'Media massa + media sosial/website', 'weight'=>10],
                    ['label'=>'Hanya media sosial/website', 'weight'=>6],
                    ['label'=>'Dokumentasi internal saja', 'weight'=>3],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 11,
                'indikator'   => 'Pedoman Teknis',
                'keterangan'  => 'Manual book/buku petunjuk',
                'jenis_file'  => 'Dokumen/Foto/Gambar',
                'hint'        => 'Manual book (pdf) atau screenshot penggunaan.',
                'params'      => [
                    ['label'=>'Manual book lengkap (pdf)', 'weight'=>10],
                    ['label'=>'Panduan ringkas/screenshot', 'weight'=>6],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 12,
                'indikator'   => 'Kemudahan Informasi Layanan',
                'keterangan'  => 'Manual/Hotline/Media Sosial/Layanan Online',
                'jenis_file'  => 'Dokumen/Foto/Gambar',
                'hint'        => 'No telp, email, akun medsos, nama & tampilan aplikasi online.',
                'params'      => [
                    ['label'=>'Ada layanan online (web/app/AI) & hotline', 'weight'=>10],
                    ['label'=>'Hanya hotline/medsos', 'weight'=>6],
                    ['label'=>'Manual saja', 'weight'=>3],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 13,
                'indikator'   => 'Kemudahan Proses Inovasi',
                'keterangan'  => 'Kecepatan layanan bagi pengguna',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'SOP berisi durasi waktu layanan.',
                'params'      => [
                    ['label'=>'SOP dengan SLA terukur', 'weight'=>10],
                    ['label'=>'SOP tanpa SLA jelas', 'weight'=>5],
                    ['label'=>'Tidak ada SOP', 'weight'=>0],
                ],
            ],
            [
                'no' => 14,
                'indikator'   => 'Penyelesaian Layanan Pengaduan',
                'keterangan'  => 'Rasio pengaduan tertangani (tahun terakhir)',
                'jenis_file'  => 'Dokumen/Foto/Gambar',
                'hint'        => 'Dokumentasi penyelesaian + rekap & persentase.',
                'params'      => [
                    ['label'=>'≥80% tertangani', 'weight'=>10],
                    ['label'=>'50–79% tertangani', 'weight'=>6],
                    ['label'=>'<50% / tak ada rekap', 'weight'=>2],
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
                'hint'        => 'PKS/MoU/Surat pernyataan replikasi.',
                'params'      => [
                    ['label'=>'PKS/MoU dengan bukti implementasi', 'weight'=>10],
                    ['label'=>'Surat Pernyataan/ket. replikasi', 'weight'=>6],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 17,
                'indikator'   => 'Kecepatan Penciptaan Inovasi *',
                'keterangan'  => 'Durasi penciptaan inovasi kompleks',
                'jenis_file'  => 'Dokumen PDF',
                'hint'        => 'Dokumen/laporan/proposal memuat tahapan & durasi.',
                'params'      => [
                    ['label'=>'Dokumen lengkap dgn timeline', 'weight'=>10],
                    ['label'=>'Dokumen parsial', 'weight'=>5],
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
                    ['label'=>'Data pengguna lengkap & valid', 'weight'=>10],
                    ['label'=>'Data sebagian/tanpa bukti kuat', 'weight'=>5],
                    ['label'=>'Tidak ada data', 'weight'=>0],
                ],
            ],
            [
                'no' => 19,
                'indikator'   => 'Monitoring & Evaluasi Inovasi Daerah',
                'keterangan'  => 'Kepuasan pengguna (SKM)',
                'jenis_file'  => 'Dokumen/Foto/Gambar',
                'hint'        => 'Screenshot testimoni atau laporan survei/penelitian.',
                'params'      => [
                    ['label'=>'Ada SKM/laporan resmi + bukti', 'weight'=>10],
                    ['label'=>'Testimoni/media sosial saja', 'weight'=>5],
                    ['label'=>'Tidak ada', 'weight'=>0],
                ],
            ],
            [
                'no' => 20,
                'indikator'   => 'Kualitas Inovasi Daerah *',
                'keterangan'  => 'Video penerapan (≤5 menit, ≤100MB) dengan 5 substansi',
                'jenis_file'  => 'Upload Video .mp4',
                'hint'        => 'Video mp4/MOV atau tautan. Sertakan cover & logo Kemendagri.',
                'params'      => [
                    ['label'=>'Video mp4/MOV sesuai ketentuan', 'weight'=>10],
                    ['label'=>'Hanya link tanpa bukti kelengkapan', 'weight'=>5],
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
