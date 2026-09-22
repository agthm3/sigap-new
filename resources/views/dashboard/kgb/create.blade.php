@extends('layouts.app')

@section('content')
<section class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      Input SK <span class="text-maroon">KGB Pegawai</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Ketik nama atau NIP pegawai untuk mencari cepat. Data golongan dan status otomatis ditarik dari profil.
    </p>
  </div>

  <a href="{{ route('sigap-kgb.index') }}"
     class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 shadow-sm transition">
    &larr; Kembali ke Monitoring
  </a>
</section>

<div x-data="kgbFormHandler()" class="mt-4">
  <form action="{{ route('sigap-kgb.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
    @csrf

    {{-- CARD 1: IDENTITAS & PENCARIAN SEARCHABLE PEGAWAI --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
      <div class="flex items-center justify-between border-b border-gray-100 pb-2 mb-4">
        <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
          <span class="w-6 h-6 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center text-xs font-extrabold">1</span>
          Identitas & Data Kepegawaian
        </h2>
        <span x-show="loading" x-cloak class="text-[11px] font-semibold text-maroon animate-pulse">
          Mengambil data pegawai...
        </span>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        
        {{-- SEARCHABLE SELECT COMPONENT --}}
        <div class="md:col-span-2 relative" @click.away="openDropdown = false">
          <label class="block text-xs font-semibold text-gray-700 mb-1">
            Pilih Pegawai (Cari Nama / NIP) <span class="text-red-500">*</span>
          </label>

          {{-- Hidden Input untuk dikirim ke Request Controller --}}
          <input type="hidden" name="user_id" :value="selectedUserId" required>

          {{-- Input Trigger & Search Field --}}
          <div class="relative">
            <input type="text"
                   x-model="searchQuery"
                   @focus="openDropdown = true"
                   @input="openDropdown = true"
                   placeholder="Ketik nama atau NIP pegawai..."
                   class="w-full rounded-xl text-xs px-3.5 py-2.5 pl-9 border border-gray-300 focus:border-maroon focus:ring-maroon bg-white placeholder-gray-400">
            
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
              </svg>
            </div>

            <button type="button" 
                    @click="openDropdown = !openDropdown"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
              <svg class="w-4 h-4 transition-transform duration-200" :class="openDropdown ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-width="2" d="M6 9l6 6 6-6"/>
              </svg>
            </button>
          </div>

          {{-- Dropdown Items List --}}
          <div x-show="openDropdown"
               x-cloak
               x-transition
               class="absolute z-50 mt-1.5 w-full bg-white border border-gray-200 rounded-xl shadow-xl max-h-60 overflow-y-auto p-1.5 space-y-1">
            
            <template x-for="item in filteredPegawai" :key="item.id">
              <div @click="selectPegawai(item)"
                   class="px-3 py-2 rounded-lg cursor-pointer hover:bg-maroon-50 hover:text-maroon transition-colors flex items-center justify-between text-xs"
                   :class="selectedUserId == item.id ? 'bg-maroon/10 text-maroon font-bold' : 'text-gray-800'">
                <div>
                  <p class="font-semibold" x-text="item.name"></p>
                  <p class="text-[11px] text-gray-400 font-mono" x-text="'NIP: ' + item.nip"></p>
                </div>
                <div class="text-right">
                  <span class="inline-flex px-2 py-0.5 rounded text-[10px] uppercase font-bold border"
                        :class="item.status.includes('PPPK') ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-blue-50 text-blue-700 border-blue-200'"
                        x-text="item.status"></span>
                  <p class="text-[10px] text-gray-400 mt-0.5" x-text="'Gol: ' + item.golongan"></p>
                </div>
              </div>
            </template>

            <template x-if="filteredPegawai.length === 0">
              <div class="px-3 py-4 text-center text-gray-400 text-xs">
                Pegawai dengan kata kunci tersebut tidak ditemukan.
              </div>
            </template>
          </div>

          @error('user_id') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Status Pegawai <span class="text-red-500">*</span></label>
          <select name="jenis_pegawai" x-model="jenisPegawai" required
                  class="w-full rounded-xl text-xs px-3.5 py-2.5 border border-gray-300 focus:border-maroon focus:ring-maroon bg-white">
            <option value="pns">PNS (Pegawai Negeri Sipil)</option>
            <option value="pppk">PPPK (Pegawai Pemerintah Perjanjian Kerja)</option>
          </select>
          @error('jenis_pegawai') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Pangkat / Golongan <span class="text-red-500">*</span></label>
          <input type="text" name="pangkat_golongan" x-model="pangkatGolongan" required
                 placeholder="Contoh: III/a atau IX"
                 class="w-full rounded-xl text-xs px-3.5 py-2.5 border border-gray-300 focus:border-maroon focus:ring-maroon">
          <p class="text-[11px] text-gray-400 mt-0.5">Otomatis sinkron dengan profil kepegawaian.</p>
          @error('pangkat_golongan') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="md:col-span-2">
          <label class="block text-xs font-semibold text-gray-700 mb-1">Jabatan</label>
          <input type="text" name="jabatan" x-model="jabatan"
                 placeholder="Contoh: Peneliti Ahli Pertama / Analis Kebijakan"
                 class="w-full rounded-xl text-xs px-3.5 py-2.5 border border-gray-300 focus:border-maroon focus:ring-maroon">
          @error('jabatan') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
        </div>
      </div>
    </div>

    {{-- CARD 2: KETETAPAN SK TERAKHIR --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
      <h2 class="text-sm font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
        <span class="w-6 h-6 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center text-xs font-extrabold">2</span>
        Ketetapan SK KGB Terakhir (Lama)
      </h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor SK Terakhir <span class="text-red-500">*</span></label>
          <input type="text" name="nomor_sk_lama" x-model="nomorSkLama" required placeholder="Contoh: 822.2/123/BKPSDM"
                 class="w-full rounded-xl text-xs px-3.5 py-2.5 border border-gray-300 focus:border-maroon focus:ring-maroon">
          @error('nomor_sk_lama') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Penetapan SK <span class="text-red-500">*</span></label>
          <input type="date" name="tanggal_sk_lama" x-model="tanggalSkLama" required
                 class="w-full rounded-xl text-xs px-3.5 py-2.5 border border-gray-300 focus:border-maroon focus:ring-maroon">
          @error('tanggal_sk_lama') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">TMT SK Terakhir <span class="text-red-500">*</span></label>
          <input type="date" name="tmt_lama" x-model="tmtLama" required
                 class="w-full rounded-xl text-xs px-3.5 py-2.5 border border-gray-300 focus:border-maroon focus:ring-maroon">
          <p class="text-[11px] text-gray-400 mt-0.5">TMT berikutnya otomatis bertambah 2 tahun dari tanggal ini.</p>
          @error('tmt_lama') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Masa Kerja Golongan (Tahun) <span class="text-red-500">*</span></label>
          <input type="number" name="mkg_tahun_lama" x-model="mkgTahun" min="0" max="40" required
                 class="w-full rounded-xl text-xs px-3.5 py-2.5 border border-gray-300 focus:border-maroon focus:ring-maroon">
          <p class="text-[11px] text-gray-400 mt-0.5">KGB baru akan dihitung (+2 tahun) dari angka ini.</p>
          @error('mkg_tahun_lama') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Masa Kerja Golongan (Bulan)</label>
          <input type="number" name="mkg_bulan_lama" x-model="mkgBulan" min="0" max="11" required
                 class="w-full rounded-xl text-xs px-3.5 py-2.5 border border-gray-300 focus:border-maroon focus:ring-maroon">
          @error('mkg_bulan_lama') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Gaji Pokok Lama (Rp) <span class="text-red-500">*</span></label>
          <input type="number" name="gaji_pokok_lama" x-model="gajiPokokLama" placeholder="Contoh: 3057200" min="0" required
                 class="w-full rounded-xl text-xs px-3.5 py-2.5 border border-gray-300 focus:border-maroon focus:ring-maroon">
          @error('gaji_pokok_lama') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Pejabat Penetap SK Lama</label>
          <input type="text" name="pejabat_penetap" value="{{ old('pejabat_penetap', 'Walikota Makassar') }}"
                 class="w-full rounded-xl text-xs px-3.5 py-2.5 border border-gray-300 focus:border-maroon focus:ring-maroon">
        </div>

        <div class="md:col-span-2">
          <label class="block text-xs font-semibold text-gray-700 mb-1">Upload Berkas Scan SK Lama (Opsional)</label>
          <input type="file" name="file_sk_lama" accept=".pdf,.jpg,.jpeg,.png"
                 class="w-full rounded-xl text-xs px-3.5 py-2 border border-gray-300 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-maroon-50 file:text-maroon">
          <p class="text-[11px] text-gray-400 mt-0.5">Format: PDF/JPG/PNG, Maks. 3MB.</p>
        </div>
      </div>
    </div>

    <div class="flex justify-end gap-2">
      <a href="{{ route('sigap-kgb.index') }}"
         class="px-5 py-2.5 rounded-xl border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
        Batal
      </a>
      <button type="submit"
              class="px-5 py-2.5 rounded-xl bg-maroon text-white text-xs font-semibold hover:bg-maroon-800 shadow-sm transition">
        Simpan & Hitung Otomatis
      </button>
    </div>
  </form>
</div>

<script>
function kgbFormHandler() {
  return {
    loading: false,
    openDropdown: false,
    searchQuery: '',
    selectedUserId: '{{ old('user_id', '') }}',
    
    // Dataset Pegawai dari Controller
    pegawaiList: [
      @if(!empty($users))
        @php foreach($users as$p): @endphp
          {
            id: {{ $p->id }},
            name: @js($p->name),
            nip: @js($p->nip),
            status: @js($p->profile->status_pegawai ?? 'Non ASN'),
            golongan: @js($p->profile->golongan_ruang ?? ($p->profile->golongan ?? '-'))
          },
        @php endforeach; @endphp
      @endif
    ],

    jenisPegawai: '{{ old('jenis_pegawai', 'pns') }}',
    pangkatGolongan: '{{ old('pangkat_golongan', '') }}',
    jabatan: '{{ old('jabatan', '') }}',
    mkgTahun: '{{ old('mkg_tahun_lama', 0) }}',
    mkgBulan: '{{ old('mkg_bulan_lama', 0) }}',
    nomorSkLama: '{{ old('nomor_sk_lama', '') }}',
    tanggalSkLama: '{{ old('tanggal_sk_lama', '') }}',
    tmtLama: '{{ old('tmt_lama', '') }}',
    gajiPokokLama: '{{ old('gaji_pokok_lama', '') }}',

    init() {
      // Restore state jika ada old user_id pasca validasi gagal
      if (this.selectedUserId) {
        const selected = this.pegawaiList.find(p => p.id == this.selectedUserId);
        if (selected) {
          this.searchQuery = `${selected.name} (${selected.nip})`;
        }
      }
    },

    get filteredPegawai() {
      if (!this.searchQuery.trim()) return this.pegawaiList;
      const q = this.searchQuery.toLowerCase();
      return this.pegawaiList.filter(p => 
        p.name.toLowerCase().includes(q) || 
        p.nip.toLowerCase().includes(q) ||
        p.golongan.toLowerCase().includes(q)
      );
    },

    selectPegawai(pegawai) {
      this.selectedUserId = pegawai.id;
      this.searchQuery = `${pegawai.name} (${pegawai.nip})`;
      this.openDropdown = false;
      this.fetchPegawaiData(pegawai.id);
    },

    fetchPegawaiData(userId) {
      this.loading = true;
      fetch(`/sigap-kgb/pegawai-data/${userId}`)
        .then(res => res.json())
        .then(data => {
          this.jenisPegawai = data.jenis_pegawai || 'pns';
          this.pangkatGolongan = data.pangkat_golongan || '';
          this.jabatan = data.jabatan || '';
          this.mkgTahun = data.mkg_tahun || 0;
          this.mkgBulan = data.mkg_bulan || 0;
          if (data.nomor_sk_lama) this.nomorSkLama = data.nomor_sk_lama;
          if (data.tanggal_sk_lama) this.tanggalSkLama = data.tanggal_sk_lama;
          if (data.tmt_lama) this.tmtLama = data.tmt_lama;
          if (data.gaji_pokok_lama) this.gajiPokokLama = data.gaji_pokok_lama;
          this.loading = false;
        })
        .catch(err => {
          console.error(err);
          this.loading = false;
        });
    }
  };
}
</script>
@endsection