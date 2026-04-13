@extends('layouts.app')

@section('content')
@php
    use App\Models\Inkubatorma;

    $status = $inkubatorma->status ?? Inkubatorma::STATUS_MENUNGGU ?? 'Menunggu';

    $isClosed = in_array($status, [
        Inkubatorma::STATUS_SELESAI ?? 'Selesai',
    ], true);

    $statusSesiKonsultasi = Inkubatorma::STATUS_SESI_KONSULTASI ?? 'Sesi Konsultasi';
    $statusSelesai = Inkubatorma::STATUS_SELESAI ?? 'Selesai';

    $fmtDateTime = function ($dt, $format = 'd M Y • H:i') {
        if (empty($dt)) return '—';
        try {
            return \Carbon\Carbon::parse($dt)->timezone('Asia/Makassar')->format($format) . ' WITA';
        } catch (\Throwable $e) {
            return '—';
        }
    };

    $layananIds = $inkubatorma->layanan_id ?? [];
    if (!is_array($layananIds)) {
        $layananIds = [$layananIds];
    }

    $layananLabel = collect($layananIds)
        ->map(function ($id) use ($inkubatorma) {
            $map = \App\Models\Inkubatorma::layananOptions();
            if ($id === 'lainnya' && !empty($inkubatorma->layanan_lainnya)) {
                return ($map[$id] ?? 'Lainnya') . ' • ' . $inkubatorma->layanan_lainnya;
            }
            return $map[$id] ?? $id;
        })
        ->implode(', ');

    // Record terakhir dari verifikator (ada/tidak ada revisi)
    $latestVerifikatorRecord = $inkubatorma->records
        ->whereIn('actor_role', ['admin', 'verifikator'])
        ->whereIn('record_type', ['sesi_konsultasi', 'review_revisi'])
        ->first();

    // Record terbaru secara keseluruhan
    $latestRecord = $inkubatorma->records->first();

    // Cek apakah review terakhir verifikator aman (tidak ada revision_note)
    $reviewAman = $latestVerifikatorRecord
        && $latestVerifikatorRecord->record_type === 'review_revisi'
        && empty($latestVerifikatorRecord->revision_note);

    // Cek apakah user sudah kirim konfirmasi selesai
    $sudahKonfirmasi = $latestRecord
        && $latestRecord->record_type === 'konfirmasi_selesai';

    // Upload revisi hanya muncul kalau ada revision_note, bukan review aman, belum konfirmasi
    $userCanUploadRevision = $isUser
        && !$isClosed
        && $latestVerifikatorRecord !== null
        && !empty($latestVerifikatorRecord->revision_note)
        && !$reviewAman
        && !$sudahKonfirmasi;

    $userCanConfirmFinish = $isUser && !$isClosed && !$sudahKonfirmasi;

    $recordTypeOptions = [
        'sesi_konsultasi' => 'Sesi Konsultasi',
        'review_revisi'   => 'Review Revisi',
    ];
@endphp

<main class="max-w-7xl mx-auto px-4 py-6 space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-bold text-gray-800">Record Konsultasi</h1>
                <span class="px-2 py-1 rounded bg-maroon/10 text-maroon text-xs font-semibold">
                    SIGAP Inkubatorma
                </span>
            </div>
            <p class="text-sm text-gray-500">Catatan sesi, revisi, upload hasil revisi, dan konfirmasi selesai</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('sigap-inkubatorma.dashboard') }}"
               class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold hover:bg-gray-50">
                ← Kembali
            </a>

            @if($isVerifikator || $isAdmin)
                <a href="{{ route('sigap-inkubatorma.verifikasi', $inkubatorma->id) }}"
                   class="px-4 py-2 rounded-lg bg-maroon text-white text-sm font-semibold hover:opacity-90">
                    Panel Verifikasi
                </a>
            @endif
        </div>
    </div>

    {{-- STATUS --}}
    <div>
        @include('dashboard.inkubatorma.partials.status-widget', [
            'status' => $status,
            'inkubatorma' => $inkubatorma
        ])
    </div>

    {{-- INFO SINGKAT --}}
    {{-- Mobile only --}}
    <div class="sm:hidden bg-white rounded-xl border border-gray-200 px-4 py-3 space-y-2 text-sm">
        <div class="flex items-center justify-between">
            <span class="text-gray-500">Kode Pengajuan</span>
            <span class="font-extrabold text-maroon">{{ $inkubatorma->kode ?? '—' }}</span>
        </div>
        <div class="h-px bg-gray-100"></div>
        <div class="flex items-center justify-between">
            <span class="text-gray-500">Judul</span>
            <span class="font-semibold text-gray-800 text-right max-w-[60%] truncate">{{ $inkubatorma->judul_konsultasi ?? '—' }}</span>
        </div>
        <div class="h-px bg-gray-100"></div>
        <div class="flex items-center justify-between">
            <span class="text-gray-500">Pengaju</span>
            <span class="font-semibold text-gray-800 text-right max-w-[60%] truncate">{{ $inkubatorma->nama_pengaju ?? '—' }}</span>
        </div>
        <div class="h-px bg-gray-100"></div>
        <div class="flex items-center justify-between">
            <span class="text-gray-500">Layanan</span>
            <span class="font-semibold text-gray-800 text-right max-w-[60%] truncate">{{ $layananLabel ?: '—' }}</span>
        </div>
    </div>

    {{-- Desktop only --}}
    <div class="hidden sm:grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Kode Pengajuan</p>
            <p class="mt-1 text-xl font-extrabold text-maroon">{{ $inkubatorma->kode ?? '—' }}</p>
            <p class="mt-1 text-xs text-gray-500">Identitas pengajuan</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Judul</p>
            <p class="mt-1 text-base font-extrabold text-maroon">{{ $inkubatorma->judul_konsultasi ?? '—' }}</p>
            <p class="mt-1 text-xs text-gray-500">Topik konsultasi</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Pengaju</p>
            <p class="mt-1 text-base font-extrabold text-maroon">{{ $inkubatorma->nama_pengaju ?? '—' }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ $inkubatorma->opd_unit ?? '—' }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Layanan</p>
            <p class="mt-1 text-sm font-semibold text-gray-800">{{ $layananLabel ?: '—' }}</p>
            <p class="mt-1 text-xs text-gray-500">Layanan terpilih</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT --}}
        <section class="lg:col-span-2 space-y-6">

            {{-- BANNER SELESAI --}}
            @if($isClosed)
                <div class="rounded-xl border border-green-200 bg-green-50 p-5 flex items-start gap-3">
                    <div class="mt-0.5 h-5 w-5 shrink-0 text-green-600">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-green-800">Konsultasi Telah Selesai</p>
                        <p class="text-sm text-green-700 mt-0.5">
                            Sesi konsultasi ini sudah ditutup. Riwayat record tetap bisa dilihat di bawah.
                        </p>
                    </div>
                </div>
            @endif

            {{-- FORM VERIFIKATOR --}}
            @if(($isVerifikator || $isAdmin) && !$isClosed)
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b">
                        <h3 class="font-semibold text-gray-800">Tambah Record</h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Digunakan untuk mencatat hasil sesi konsultasi atau review revisi dari pengaju.
                        </p>
                    </div>

                    <div class="p-5">
                        @if($status !== $statusSesiKonsultasi && !$isClosed)
                            <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                                Status saat ini <b>{{ $status }}</b>. Idealnya record aktif ditambahkan saat status sudah
                                <b>{{ $statusSesiKonsultasi }}</b>.
                            </div>
                        @endif

                        <form method="POST"
                              action="{{ route('sigap-inkubatorma.records.store', $inkubatorma->id) }}"
                              enctype="multipart/form-data"
                              class="space-y-4">
                            @csrf

                            <div>
                                <label class="text-xs font-semibold text-gray-600">Jenis Record</label>
                                <select name="record_type"
                                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-maroon focus:border-maroon">
                                    @foreach($recordTypeOptions as $value => $label)
                                        <option value="{{ $value }}" @selected(old('record_type') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-gray-600">Judul Record <span class="text-red-600">*</span></label>
                                <input type="text" name="title" value="{{ old('title') }}"
                                       placeholder="Contoh: Hasil sesi konsultasi tahap 1"
                                       class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-maroon focus:border-maroon"
                                       required>
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-gray-600">Isi Catatan / Record <span class="text-red-600">*</span></label>
                                <textarea name="content" rows="6"
                                          placeholder="Tulis hasil diskusi, arahan, keputusan, atau progres konsultasi..."
                                          class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-maroon focus:border-maroon"
                                          required>{{ old('content') }}</textarea>
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-gray-600">Catatan Revisi (Opsional)</label>
                                <textarea name="revision_note" rows="4"
                                          placeholder="Isi jika ada revisi yang perlu dikerjakan user."
                                          class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-maroon focus:border-maroon">{{ old('revision_note') }}</textarea>
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-gray-600">Lampiran Pendukung (Opsional)</label>
                                <input type="file" name="attachment"
                                       class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white">
                                <p class="mt-1 text-xs text-gray-500">
                                    Boleh PDF/DOC/DOCX/XLS/XLSX/PPT/PPTX/ZIP/RAR/JPG/JPEG/PNG.
                                </p>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                        class="px-4 py-2 rounded-lg bg-maroon text-white text-sm font-semibold hover:opacity-90">
                                    Simpan Record
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- FORM USER: UPLOAD REVISI / STATUS --}}
            @if($isUser && !$isClosed)

                @if($sudahKonfirmasi)
                    {{-- Sudah konfirmasi selesai --}}
                    <div class="bg-white rounded-xl border border-blue-200 overflow-hidden">
                        <div class="px-5 py-4 bg-blue-50">
                            <h3 class="font-semibold text-blue-800">Konfirmasi Selesai Sudah Dikirim</h3>
                            <p class="text-xs text-blue-700 mt-0.5">
                                Menunggu verifikator menutup sesi konsultasi ini.
                            </p>
                        </div>
                    </div>

                @elseif($reviewAman)
                    {{-- Review aman, minta konfirmasi selesai --}}
                    <div class="bg-white rounded-xl border border-green-200 overflow-hidden">
                        <div class="px-5 py-4 border-b border-green-200 bg-green-50">
                            <h3 class="font-semibold text-green-800">Revisi Sudah Dinyatakan Aman</h3>
                            <p class="text-xs text-green-700 mt-0.5">
                                Verifikator telah mereview dan tidak ada perbaikan lagi.
                            </p>
                        </div>
                        <div class="p-5 text-sm text-green-800">
                            Jika tidak ada pertanyaan lagi, silakan kirim <strong>Konfirmasi Selesai</strong> di bawah untuk menutup sesi konsultasi ini.
                        </div>
                    </div>

                @elseif($userCanUploadRevision)
                    {{-- Ada revisi yang perlu dikerjakan --}}
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <div class="px-5 py-4 border-b">
                            <h3 class="font-semibold text-gray-800">Upload Hasil Revisi</h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Upload dokumen revisi sesuai catatan terbaru dari verifikator.
                            </p>
                        </div>
                        <div class="p-5">
                            <form method="POST"
                                action="{{ route('sigap-inkubatorma.records.upload-revision', [$inkubatorma->id, $latestVerifikatorRecord->id]) }}"
                                enctype="multipart/form-data"
                                class="space-y-4">
                                @csrf

                                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                                    <p class="font-semibold">Catatan revisi terbaru:</p>
                                    <p class="mt-1 whitespace-pre-line">{{ $latestVerifikatorRecord->revision_note }}</p>
                                </div>

                                <div>
                                    <label class="text-xs font-semibold text-gray-600">Catatan dari User (Opsional)</label>
                                    <textarea name="user_revision_note" rows="4"
                                              placeholder="Contoh: Dokumen sudah diperbaiki sesuai arahan, mohon dicek kembali."
                                              class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-maroon focus:border-maroon">{{ old('user_revision_note') }}</textarea>
                                </div>

                                <div>
                                    <label class="text-xs font-semibold text-gray-600">File Hasil Revisi <span class="text-red-600">*</span></label>
                                    <input type="file" name="user_revision_file"
                                           class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white"
                                           required>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit"
                                            class="px-4 py-2 rounded-lg bg-maroon text-white text-sm font-semibold hover:opacity-90">
                                        Upload Revisi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

            @endif

            {{-- FORM USER: KONFIRMASI SELESAI --}}
            @if($userCanConfirmFinish)
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b">
                        <h3 class="font-semibold text-gray-800">Konfirmasi Selesai</h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Isi jika konsultasi sudah benar-benar aman dan siap ditutup.
                        </p>
                    </div>
                    <div class="p-5">
                        <form method="POST"
                              action="{{ route('sigap-inkubatorma.records.confirm-finish', $inkubatorma->id) }}"
                              class="space-y-4">
                            @csrf

                            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">
                                Ketik <b>SELESAI</b> jika Anda yakin konsultasi ini sudah tidak memerlukan tindak lanjut lagi.
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-gray-600">Kode Konfirmasi</label>
                                <input type="text" name="finish_confirm_code"
                                       placeholder="Ketik SELESAI"
                                       class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-maroon focus:border-maroon"
                                       required>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                        class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-semibold hover:opacity-90">
                                    Kirim Konfirmasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- LIST RECORD --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b">
                    <h3 class="font-semibold text-gray-800">Riwayat Record Konsultasi</h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Semua catatan sesi, revisi, upload revisi, dan konfirmasi selesai.
                    </p>
                </div>

                <div class="p-5 space-y-4">
                    @forelse($inkubatorma->records as $record)
                        @php
                            $typeColor = match($record->record_type) {
                                'sesi_konsultasi'    => 'bg-blue-100 text-blue-700',
                                'upload_revisi'      => 'bg-amber-100 text-amber-700',
                                'review_revisi'      => 'bg-indigo-100 text-indigo-700',
                                'konfirmasi_selesai' => 'bg-green-100 text-green-700',
                                default              => 'bg-gray-100 text-gray-700',
                            };

                            $roleColor = match($record->actor_role) {
                                'admin'       => 'bg-purple-100 text-purple-700',
                                'verifikator' => 'bg-maroon/10 text-maroon',
                                'user'        => 'bg-emerald-100 text-emerald-700',
                                default       => 'bg-gray-100 text-gray-700',
                            };
                        @endphp

                        <div class="rounded-xl border border-gray-200 p-5">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $typeColor }}">
                                            {{ $record->record_type_label }}
                                        </span>
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $roleColor }}">
                                            {{ $record->actor_role_label }}
                                        </span>
                                    </div>
                                    <h4 class="mt-3 text-base font-bold text-gray-900">
                                        {{ $record->title ?: 'Tanpa Judul' }}
                                    </h4>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Oleh {{ $record->actor->name ?? '—' }} • {{ $fmtDateTime($record->created_at) }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4">
                                <p class="text-xs font-semibold text-gray-500">Isi Record</p>
                                <div class="mt-1 text-sm text-gray-800 leading-relaxed whitespace-pre-line">
                                    {{ $record->content ?: '—' }}
                                </div>
                            </div>

                            @if(!empty($record->revision_note))
                                <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-4">
                                    <p class="text-xs font-semibold text-amber-800">Catatan Revisi</p>
                                    <div class="mt-1 text-sm text-amber-900 whitespace-pre-line">
                                        {{ $record->revision_note }}
                                    </div>
                                </div>
                            @endif

                            @if(!empty($record->file_path))
                                <div class="mt-4">
                                    <p class="text-xs font-semibold text-gray-500">Lampiran</p>
                                    <a href="{{ asset('storage/' . $record->file_path) }}"
                                       target="_blank"
                                       class="mt-1 inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold hover:bg-gray-50">
                                        {{ $record->file_name ?: 'Lihat Lampiran' }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-300 p-8 text-center text-gray-500">
                            Belum ada record konsultasi.
                        </div>
                    @endforelse
                </div>
            </div>

        </section>

        {{-- RIGHT --}}
        <aside class="space-y-6">

            {{-- INFO ALUR --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b">
                    <h3 class="font-semibold text-gray-800">Panduan Singkat</h3>
                </div>
                <div class="p-5 space-y-3 text-sm text-gray-700">
                    @if($isVerifikator || $isAdmin)
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                            <p class="font-semibold text-gray-800">Untuk Verifikator</p>
                            <ul class="mt-2 list-disc ml-5 space-y-1">
                                <li>Isi record setelah sesi konsultasi berlangsung.</li>
                                <li>Kalau ada revisi, isi di bagian catatan revisi.</li>
                                <li>Kalau dokumen user sudah aman, buat record review revisi tanpa catatan revisi.</li>
                                <li>Jika user sudah konfirmasi SELESAI, baru status utama bisa ditutup ke Selesai.</li>
                            </ul>
                        </div>
                    @endif

                    @if($isUser)
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                            <p class="font-semibold text-gray-800">Untuk User</p>
                            <ul class="mt-2 list-disc ml-5 space-y-1">
                                <li>Baca record sesi dan catatan revisi dari verifikator.</li>
                                <li>Jika ada revisi, upload dokumen hasil revisi.</li>
                                <li>Jika masih ada pertanyaan, hubungi verifikator via WhatsApp.</li>
                                <li>Jika semua sudah aman, kirim konfirmasi SELESAI.</li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            {{-- STATUS AKHIR --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b">
                    <h3 class="font-semibold text-gray-800">Status Saat Ini</h3>
                </div>
                <div class="p-5">
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <p class="text-xs font-semibold text-gray-500">Status</p>
                        <p class="mt-1 text-base font-bold text-gray-900">{{ $status }}</p>
                    </div>
                </div>
            </div>

        </aside>
    </div>
</main>
@endsection