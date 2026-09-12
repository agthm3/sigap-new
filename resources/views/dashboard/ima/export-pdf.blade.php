<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Profil & Evidence Inovasi IMA ({{ $inovasiList->count() }} Inovasi)</title>
    <style>
        @page { margin: 15mm; size: A4; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11pt; color: #111; line-height: 1.5; margin: 0; padding: 0; background: #fff; }
        .page-break { page-break-before: always; }
        .no-break { page-break-inside: avoid; }
        
        .inovasi-container { margin-bottom: 40px; }
        .inovasi-container:not(:first-child) { page-break-before: always; }

        /* Header */
        .header { text-align: center; border-bottom: 2px solid #8B0000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18pt; color: #8B0000; }
        .header h2 { margin: 4px 0 0; font-size: 13pt; color: #333; }
        
        /* Banner & Skor */
        .summary-box { background: #fdf6e3; border: 1px solid #e0c487; padding: 14px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;}
        .score { font-size: 28pt; font-weight: bold; color: #b8860b; margin: 0; line-height: 1;}
        .score-label { font-size: 9pt; text-transform: uppercase; color: #8B0000; font-weight: bold; }
        
        /* Tabel Meta */
        table { border-collapse: collapse; margin-bottom: 18px; width: 100%; page-break-inside: avoid; }
        th, td { border: 1px solid #ccc; padding: 7px 10px; text-align: left; vertical-align: top; font-size: 10.5pt; }
        th { background-color: #f8f8f8; width: 32%; color: #333; }
        
        /* Subjudul */
        h3 { border-bottom: 1px solid #8B0000; color: #8B0000; padding-bottom: 4px; margin-top: 25px; font-size: 13pt; }
        
        /* Konten Paragraf */
        .text-content { background: #fafafa; border: 1px solid #eee; padding: 10px; border-radius: 6px; font-size: 10pt; text-align: justify; }
        
        /* Link Berkas */
        .file-link { color: #0056b3; text-decoration: none; word-break: break-all; }
        .file-link:hover { text-decoration: underline; }
        ul.file-list { margin: 0; padding-left: 18px; }
        ul.file-list li { margin-bottom: 4px; }

        @media print {
            .btn-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    <!-- Tombol Cetak Melayang -->
    <div class="btn-print" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;">
        <button onclick="window.print()" style="padding: 12px 24px; background: #8B0000; color: #fff; border: none; border-radius: 10px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.3); font-size: 13pt;">
            🖨️ Cetak / Simpan ke PDF ({{ $inovasiList->count() }} Inovasi)
        </button>
        <button onclick="window.close()" style="padding: 12px 20px; background: #444; color: #fff; border: none; border-radius: 10px; font-weight: bold; cursor: pointer; margin-left: 8px; font-size: 13pt;">
            Tutup
        </button>
    </div>

    @foreach($inovasiList as $inovasi)
        @php
            $evidences = $inovasi->evidences->sortBy(fn($ev) => $ev->indicator->no_urut ?? 99);
            $skorTotal = 0;
            foreach ($evidences as $ev) {
                if ($ev->review_status !== 'Ditolak') {
                    $skorTotal += ($ev->parameter_weight ?? 0) * ($ev->indicator->pengali ?? 1);
                }
            }
        @endphp

        <div class="inovasi-container">
            <!-- Header Profil -->
            <div class="header">
                <h1>Laporan Profil Inovasi Daerah</h1>
                <h2>Innovative Mayor Award (IMA) Makassar</h2>
            </div>

            <div class="summary-box">
                <div>
                    <div style="font-size: 9pt; font-weight: bold; padding: 2px 7px; background: #8B0000; color: white; display: inline-block; border-radius: 4px; margin-bottom: 6px;">{{ $inovasi->kategori_ima }}</div>
                    <div style="font-size: 16pt; font-weight: bold; color: #111;">{{ $inovasi->judul }}</div>
                    <div style="font-size: 10.5pt; color: #555;">{{ $inovasi->opd_unit ?? 'OPD/Unit Tidak Disebutkan' }}</div>
                </div>
                <div style="text-align: right;">
                    <div class="score-label">Estimasi Poin</div>
                    <div class="score">{{ $skorTotal }}</div>
                    <div style="font-size: 8.5pt; color: #666; margin-top: 4px;">Status: <strong>{{ $inovasi->asistensi_status }}</strong></div>
                </div>
            </div>

            <h3>1. Identitas & Kontak Operator (PIC)</h3>
            <table>
                <tr><th>Nama Inisiator</th><td>{{ $inovasi->inisiator_nama ?? '-' }} ({{ $inovasi->inisiator_daerah ?? '-' }})</td></tr>
                <tr><th>Nama Operator (PIC)</th><td>{{ $inovasi->operator_nama }}</td></tr>
                <tr><th>Jabatan PIC</th><td>{{ $inovasi->operator_jabatan ?? '-' }}</td></tr>
                <tr><th>WhatsApp / Email PIC</th><td>{{ $inovasi->operator_wa }} / {{ $inovasi->operator_email ?? '-' }}</td></tr>
                <tr><th>Koordinat Lokasi</th><td>{{ $inovasi->koordinat ?? '-' }}</td></tr>
            </table>

            <h3>2. Metadata Kebijakan & Tahapan</h3>
            <table>
                <tr><th>Urusan Pemerintah</th><td>{{ $inovasi->urusan_pemerintah ?? '-' }}</td></tr>
                <tr><th>Klasifikasi Inovasi</th><td>{{ $inovasi->klasifikasi ?? '-' }}</td></tr>
                <tr><th>Jenis & Bentuk Inovasi</th><td>{{ $inovasi->jenis_inovasi ?? '-' }} / {{ $inovasi->bentuk_inovasi_daerah ?? '-' }}</td></tr>
                <tr><th>Tahapan Inovasi</th><td>{{ $inovasi->tahap_inovasi ?? '-' }} (Perkembangan: {{ $inovasi->perkembangan_inovasi ?? '-' }})</td></tr>
                <tr><th>Waktu Uji Coba / Penerapan</th><td>{{ $inovasi->waktu_uji_coba ? $inovasi->waktu_uji_coba->format('d/m/Y') : '-' }} s.d. {{ $inovasi->waktu_penerapan ? $inovasi->waktu_penerapan->format('d/m/Y') : '-' }}</td></tr>
                <tr><th>Asta Cita</th><td>{{ $inovasi->asta_cipta ?? '-' }}</td></tr>
                <tr><th>Misi Walikota</th><td>{{ $inovasi->misi_walikota ?? '-' }}</td></tr>
            </table>

            <div class="page-break"></div>

            <!-- Uraian Ringkas -->
            <h3>3. Deskripsi & Rancang Bangun Inovasi</h3>
            <div style="margin-bottom: 12px;">
                <strong style="font-size: 10pt; color: #555; text-transform: uppercase;">Rancang Bangun:</strong>
                <div class="text-content">{!! nl2br(e($inovasi->rancang_bangun)) !!}</div>
            </div>
            <div style="margin-bottom: 12px;">
                <strong style="font-size: 10pt; color: #555; text-transform: uppercase;">Tujuan Inovasi:</strong>
                <div class="text-content">{!! nl2br(e($inovasi->tujuan ?? '-')) !!}</div>
            </div>
            <div style="margin-bottom: 12px;">
                <strong style="font-size: 10pt; color: #555; text-transform: uppercase;">Manfaat yang Diperoleh:</strong>
                <div class="text-content">{!! nl2br(e($inovasi->manfaat ?? '-')) !!}</div>
            </div>
            <div>
                <strong style="font-size: 10pt; color: #555; text-transform: uppercase;">Hasil Inovasi:</strong>
                <div class="text-content">{!! nl2br(e($inovasi->hasil_inovasi ?? '-')) !!}</div>
            </div>

            <div class="page-break"></div>

            <!-- 20 Indikator Evidence -->
            <div class="header">
                <h1 style="font-size: 16pt;">Buku Laporan Evidence</h1>
                <h2>{{ $inovasi->judul }} (20 Indikator)</h2>
            </div>

            @if($evidences->isEmpty())
                <p style="text-align: center; color: #888; margin-top: 40px; font-style: italic;">Inovator belum mengunggah dokumen bukti pada satupun indikator.</p>
            @else
                @foreach($evidences as $ev)
                    @php
                        $ind = $ev->indicator;
                        $poin = $ev->parameter_weight ?? 0;
                        $pengali = $ind->pengali ?? 1;
                        $skorAkhir = $poin * $pengali;
                    @endphp
                    <div class="no-break" style="border: 1px solid #ccc; border-radius: 6px; padding: 12px; margin-bottom: 14px;">
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 8px;">
                            <strong style="font-size: 11.5pt; color: #8B0000;">Indikator {{ $ind->no_urut ?? '?' }}: {{ $ind->nama_indikator ?? '-' }}</strong>
                            <strong style="background: #f4f4f4; padding: 2px 7px; border-radius: 4px; font-size: 10pt;">Skor: {{ $skorAkhir }}</strong>
                        </div>

                        <table style="margin-bottom: 0;">
                            <tr>
                                <th style="width: 25%; background: none; border: none; border-bottom: 1px dotted #ccc; padding: 3px 0;">Parameter Diklaim</th>
                                <td style="border: none; border-bottom: 1px dotted #ccc; padding: 3px 0;"><strong>{{ $ev->parameter_label ?? '-' }}</strong> (Poin: {{ $poin }})</td>
                            </tr>
                            <tr>
                                <th style="background: none; border: none; border-bottom: 1px dotted #ccc; padding: 3px 0;">Keterangan</th>
                                <td style="border: none; border-bottom: 1px dotted #ccc; padding: 3px 0;">{{ $ev->deskripsi ?? '-' }}</td>
                            </tr>
                            @if($ev->link_url)
                            <tr>
                                <th style="background: none; border: none; border-bottom: 1px dotted #ccc; padding: 3px 0;">Tautan Eksternal</th>
                                <td style="border: none; border-bottom: 1px dotted #ccc; padding: 3px 0;">
                                    <a href="{{ $ev->link_url }}" class="file-link" target="_blank">{{ $ev->link_url }}</a>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <th style="background: none; border: none; padding: 3px 0;">Berkas Terlampir</th>
                                <td style="border: none; padding: 3px 0;">
                                    @if($ev->files->isEmpty())
                                        <span style="color: #999; font-style: italic;">Tidak ada berkas unggahan fisik.</span>
                                    @else
                                        <ul class="file-list">
                                            @foreach($ev->files as $file)
                                                <li>
                                                    <!-- Klik langsung membuka file asli di server -->
                                                    <a href="{{ asset('storage/' . ltrim($file->file_path, '/')) }}" class="file-link" target="_blank">
                                                        📄 {{ $file->file_name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </td>
                            </tr>
                        </table>

                        @if($ev->review_note)
                            <div style="margin-top: 8px; padding: 8px; background: #fff8f8; border-left: 3px solid #dc3545; font-size: 9.5pt;">
                                <strong>Catatan Evaluasi ({{ $ev->review_status }}):</strong> {{ $ev->review_note }}
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    @endforeach

</body>
</html>