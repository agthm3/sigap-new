<div class="border border-gray-200 rounded-xl overflow-hidden">

    <div class="px-5 py-3 bg-gray-50 text-sm font-semibold text-gray-700">
        Dokumen / Sertifikat Pegawai
    </div>

    <div class="overflow-x-auto">

        <table class="min-w-full text-sm">

            <thead class="bg-white">
                <tr class="text-left border-b">
                    <th class="px-5 py-3">Jenis</th>
                    <th class="px-5 py-3">Nama File</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Diunggah</th>
                    <th class="px-5 py-3">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y">

                @forelse($docs as $d)

                <tr>

                    {{-- Jenis --}}
                    <td class="px-5 py-3">
                        <span class="font-medium text-gray-900">
                            {{ $d['label'] ?? '-' }}
                        </span>
                    </td>

                    {{-- Nama file --}}
                    <td class="px-5 py-3 text-gray-700">
                        {{ $d['filename'] ?? '-' }}
                    </td>

                    {{-- Status --}}
                    <td class="px-5 py-3">

                        @if(($d['status'] ?? '') === 'Terverifikasi')
                            <span class="px-2 py-0.5 rounded text-xs bg-emerald-50 text-emerald-700">
                                {{ $d['status'] }}
                            </span>

                        @elseif(($d['status'] ?? '') === 'Menunggu verifikasi')
                            <span class="px-2 py-0.5 rounded text-xs bg-amber-50 text-amber-700">
                                {{ $d['status'] }}
                            </span>

                        @else
                            <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600">
                                {{ $d['status'] ?? '-' }}
                            </span>
                        @endif

                    </td>

                    {{-- Upload --}}
                    <td class="px-5 py-3 text-gray-700">
                        {{ $d['uploaded_at'] ?? '-' }}
                    </td>

                    {{-- Aksi --}}
                    <td class="px-5 py-3">

                        <div class="flex gap-2">

                            @if(isset($d['id']))
                                <a
                                    href="{{ route('pegawai.docs.show', $d['id']) }}"
                                    class="px-3 py-1.5 rounded-md border hover:bg-gray-50 text-sm"
                                >
                                    Lihat
                                </a>

                                <a
                                    href="{{ route('pegawai.docs.download', $d['id']) }}"
                                    class="px-3 py-1.5 rounded-md border hover:bg-gray-50 text-sm"
                                >
                                    Unduh
                                </a>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif

                        </div>

                    </td>

                </tr>

                @empty

                <tr>
                    <td colspan="5" class="px-5 py-8 text-center text-gray-500">
                        Belum ada dokumen / sertifikat yang diunggah.
                    </td>
                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>