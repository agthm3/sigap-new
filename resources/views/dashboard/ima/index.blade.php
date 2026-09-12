@extends('layouts.app')

@section('content')
<section class="max-w-7xl mx-auto px-4 py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 bg-gradient-to-r from-gray-900 via-gray-800 to-black p-6 sm:p-8 rounded-3xl shadow-xl relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-48 h-48 bg-amber-400 rounded-full blur-3xl opacity-20"></div>
        
        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-400/20 text-amber-400 text-xs font-bold uppercase tracking-wider border border-amber-400/30 mb-3">
                🏆 Innovative Mayor Award
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Dashboard SIGAP IMA</h1>
            <p class="text-sm text-gray-300 mt-1 max-w-xl leading-relaxed">
                Pantau linimasa agenda lomba, progres pengisian 20 indikator, estimasi perolehan poin sementara, dan catatan hasil verifikasi.
            </p>
        </div>
        
        <div class="relative z-10 flex flex-wrap gap-3">
            @php
                $isVerifikator = auth()->user()->hasAnyRole(['admin', 'superadmin', 'verif_inovasi']);
            @endphp

            @if(!$isLocked || $isVerifikator)
                <a href="{{ route('sigap-ima.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 text-gray-900 font-bold hover:bg-amber-400 transition shadow-lg shadow-amber-500/30 hover:-translate-y-0.5 text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Inovasi IMA
                </a>
            @else
                <div class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-800 text-gray-400 text-xs font-bold border border-gray-700 cursor-not-allowed">
                    🔒 Pendaftaran Ditutup
                </div>
            @endif
        </div>
    </div>

    <!-- Alert Sukses / Galat -->
    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 border border-emerald-200 p-4 rounded-2xl font-medium text-sm flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 text-rose-700 border border-rose-200 p-4 rounded-2xl font-medium text-sm flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- BANNER PEMBERITAHUAN KETIKA DIKUNCI -->
    @if($isLocked)
        <div class="bg-gradient-to-r from-rose-500/10 via-amber-500/10 to-rose-500/10 border-2 border-rose-400/60 p-5 rounded-3xl flex items-start gap-4 shadow-sm">
            <div class="w-10 h-10 rounded-2xl bg-rose-500 text-white flex items-center justify-center font-bold text-lg flex-shrink-0 shadow-md">
                🔒
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <h3 class="font-extrabold text-gray-900 text-base">Pengisian Inovasi & Evidence Ditutup</h3>
                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-rose-600 text-white">Terkunci</span>
                </div>
                <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                    {{ $lockNotice }}
                </p>
                @if($isVerifikator)
                    <p class="text-[11px] text-amber-800 font-semibold mt-2">
                        💡 Anda login sebagai <strong>Admin/Verifikator</strong>, sehingga tetap dapat meninjau dan menilai data.
                    </p>
                @endif
            </div>
        </div>
    @endif

    <!-- LINIMASA / TIMELINE JADWAL TAHAPAN LOMBA -->
    @if($schedules->isNotEmpty())
        <div class="bg-white border border-gray-200 rounded-3xl p-6 sm:p-7 shadow-sm overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-100 pb-4 mb-6">
                <div>
                    <h3 class="font-bold text-gray-900 text-base">🗓️ Linimasa Agenda Pelaksanaan IMA</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Perhatikan batas tanggal tiap tahapan lomba untuk mempersiapkan dokumen inovasi.</p>
                </div>
                <span class="text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1 rounded-full w-fit">
                    Total {{ $schedules->count() }} Tahapan
                </span>
            </div>

            <!-- Visual Stepper Timeline Horizontal / Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 relative">
                @foreach($schedules as $sch)
                    @php
                        $now = now();
                        $isOngoing = $now->between($sch->tanggal_mulai->startOfDay(), $sch->tanggal_selesai->endOfDay());
                        $isPassed  = $now->isAfter($sch->tanggal_selesai->endOfDay());
                    @endphp
                    <div class="relative p-4 rounded-2xl border transition {{ $isOngoing ? 'border-amber-400 bg-amber-50/50 shadow-md ring-2 ring-amber-400/30' : ($isPassed ? 'border-gray-200 bg-gray-50/70 opacity-75' : 'border-gray-200 bg-white hover:border-gray-300') }}">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="w-6 h-6 rounded-lg text-xs font-extrabold flex items-center justify-center {{ $isOngoing ? 'bg-amber-500 text-white' : ($isPassed ? 'bg-gray-400 text-white' : 'bg-gray-800 text-white') }}">
                                {{ $sch->urutan }}
                            </span>
                            @if($isOngoing)
                                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-amber-200 text-amber-900 animate-pulse">BERLANGSUNG</span>
                            @elseif($isPassed)
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-200 text-gray-600">SELESAI</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-100 text-blue-700">MENDATANG</span>
                            @endif
                        </div>

                        <h4 class="font-bold text-gray-900 text-sm leading-tight line-clamp-2">{{ $sch->fase_nama }}</h4>
                        
                        <p class="text-[11px] font-semibold text-amber-800 mt-2 flex items-center gap-1">
                            <span>📅</span> {{ $sch->tanggal_mulai->format('d M') }} – {{ $sch->tanggal_selesai->format('d M Y') }}
                        </p>

                        @if($sch->deskripsi)
                            <p class="text-[11px] text-gray-500 mt-2 line-clamp-2 leading-relaxed">{{ $sch->deskripsi }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</section>

<!-- Filter & Table Section -->
<section class="max-w-7xl mx-auto px-4 py-2">
    <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">
        
        <!-- Filter Bar -->
        <form method="GET" action="{{ route('sigap-ima.index') }}" class="p-4 border-b border-gray-100 bg-gray-50/70 flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <select name="kategori" onchange="this.form.submit()" class="rounded-xl border-gray-300 text-sm focus:ring-amber-500 focus:border-amber-500 w-full sm:w-40">
                    <option value="">Semua Kategori</option>
                    <option value="PRO IMA" @selected(request('kategori') == 'PRO IMA')>PRO IMA</option>
                    <option value="PEMULA IMA" @selected(request('kategori') == 'PEMULA IMA')>PEMULA IMA</option>
                </select>
                <select name="status" onchange="this.form.submit()" class="rounded-xl border-gray-300 text-sm focus:ring-amber-500 focus:border-amber-500 w-full sm:w-48">
                    <option value="">Semua Status Asistensi</option>
                    <option value="Menunggu Verifikasi" @selected(request('status') == 'Menunggu Verifikasi')>Menunggu Verifikasi</option>
                    <option value="Revisi" @selected(request('status') == 'Revisi')>Revisi</option>
                    <option value="Disetujui" @selected(request('status') == 'Disetujui')>Disetujui</option>
                    <option value="Ditolak" @selected(request('status') == 'Ditolak')>Ditolak</option>
                </select>
            </div>
            <div class="relative w-full sm:w-72 flex gap-2">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari judul inovasi atau PIC..." class="w-full pl-9 pr-4 py-2 rounded-xl border-gray-300 text-sm focus:ring-amber-500 focus:border-amber-500">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <button type="submit" class="hidden">Cari</button>
            </div>
        </form>

        <!-- Table Responsive -->
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left whitespace-nowrap">
                <thead class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider border-b">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Inovasi & Kategori</th>
                        <th class="px-6 py-4 font-semibold">Operator (PIC)</th>
                        <th class="px-6 py-4 font-semibold text-center">Poin Sementara</th>
                        <th class="px-6 py-4 font-semibold text-center">Status Evidence</th>
                        <th class="px-6 py-4 font-semibold">Status Profil</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        @php
                            $poinSementara = 0;
                            $evDisetujui = 0;
                            $evRevisi = 0;
                            $evDitolak = 0;

                            foreach ($item->evidences as $ev) {
                                $pengali = $ev->indicator->pengali ?? 1;
                                $weight = (int) ($ev->parameter_weight ?? 0);
                                
                                if ($ev->review_status !== 'Ditolak') {
                                    $poinSementara += ($weight * $pengali);
                                }

                                if ($ev->review_status === 'Disetujui') $evDisetujui++;
                                elseif ($ev->review_status === 'Revisi') $evRevisi++;
                                elseif ($ev->review_status === 'Ditolak') $evDitolak++;
                            }

                            $filledCount = $item->evidences->unique('ima_indicator_id')->count();

                            $cssStatus = match($item->asistensi_status) {
                                'Disetujui'              => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'Revisi', 'Dikembalikan' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'Ditolak'                => 'bg-rose-50 text-rose-700 border-rose-200',
                                default                  => 'bg-gray-50 text-gray-600 border-gray-200',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-900 leading-snug">{{ $item->judul }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wide {{ $item->kategori_ima == 'PRO IMA' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $item->kategori_ima }}
                                    </span>
                                    <span class="text-xs text-gray-500">{{ $item->opd_unit ?? 'OPD Belum Diisi' }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-800">{{ $item->operator_nama }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">📱 {{ $item->operator_wa }}</p>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex flex-col items-center justify-center bg-amber-50 border border-amber-200 rounded-xl px-4 py-1.5 shadow-sm">
                                    <span class="text-lg font-black text-amber-600 tabular-nums leading-none">{{ $poinSementara }}</span>
                                    <span class="text-[9px] uppercase font-bold text-amber-700/80 tracking-wider mt-0.5">Estimasi Poin</span>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center justify-center bg-gray-100 rounded-full px-2.5 py-0.5 font-bold text-gray-700 text-xs mb-1.5">
                                    {{ $filledCount }} / 20 Terisi
                                </div>

                                @if($evDisetujui > 0 || $evRevisi > 0 || $evDitolak > 0)
                                    <div class="flex items-center justify-center gap-1">
                                        @if($evDisetujui > 0)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200" title="{{ $evDisetujui }} Indikator Disetujui">
                                                ✅ {{ $evDisetujui }}
                                            </span>
                                        @endif
                                        @if($evRevisi > 0)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200" title="{{ $evRevisi }} Indikator Perlu Revisi">
                                                ⚠️ {{ $evRevisi }}
                                            </span>
                                        @endif
                                        @if($evDitolak > 0)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200" title="{{ $evDitolak }} Indikator Ditolak">
                                                ❌ {{ $evDitolak }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="block text-[10px] text-gray-400">Belum direview</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-block px-2.5 py-1 rounded-md text-xs font-bold border {{ $cssStatus }}">
                                    {{ $item->asistensi_status }}
                                </span>
                                @if(!empty($item->asistensi_note))
                                    <p class="text-[11px] text-gray-500 mt-1 max-w-xs truncate" title="{{ $item->asistensi_note }}">
                                        💬 {{ $item->asistensi_note }}
                                    </p>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('sigap-ima.show', $item->id) }}" class="px-3 py-1.5 rounded-xl border border-amber-300 text-amber-700 bg-amber-50 hover:bg-amber-100 transition text-xs font-bold shadow-sm">
                                        Lihat / Review
                                    </a>
                                    
                                    @if(!$isVerifikator)
                                        @if(!$isLocked)
                                            <a href="{{ route('sigap-ima.evidence', $item->id) }}" class="px-3 py-1.5 rounded-xl bg-gray-900 text-white hover:bg-black transition text-xs font-medium shadow-sm">
                                                Isi Evidence
                                            </a>
                                        @else
                                            <span class="px-2.5 py-1 rounded-xl bg-gray-100 text-gray-400 text-xs font-semibold cursor-not-allowed" title="Pengisian evidence sedang dikunci panitia">
                                                🔒 Terkunci
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3 border border-gray-100">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    </div>
                                    <p class="text-gray-500 font-medium">Belum ada pendaftaran inovasi IMA.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($items->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</section>
@endsection