@extends('layouts.app')

@section('content')
@php
  $dateStr = \Carbon\Carbon::parse($agenda->date)->locale('id')->translatedFormat('l, d F Y');
@endphp

<section class="max-w-3xl mx-auto px-4 py-6">
  <div class="flex items-start justify-between gap-3">
    <div>
      <h1 class="text-2xl font-extrabold text-gray-900">SIGAP Agenda</h1>
      <div class="text-sm text-gray-600 mt-1">
        <div class="font-semibold text-gray-800">{{ $agenda->unit_title }}</div>
        <div>{{ $dateStr }}</div>
      </div>
      @if($agenda->is_public)
        <span class="inline-flex items-center mt-2 gap-2 text-xs px-2.5 py-1 rounded-full bg-green-50 text-green-700 ring-1 ring-green-200">
          Publik
        </span>
      @endif
    </div>
    @php
    $showUrl = route('sigap-agenda.show', [], false).'?id='.$agenda->id;
    @endphp
    <div class="flex gap-2">
      <a href="{{ route('sigap-agenda.index') }}"
         class="px-3 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Kembali</a>
    </div>
      <button type="button" onclick="copyCurrentShowLink('{{ $showUrl }}')"
          class="px-3 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">
    Salin Link
  </button>
  </div>

  <div class="mt-5 bg-white border border-gray-200 rounded-2xl p-4 sm:p-6">
    @if($agenda->items->isEmpty())
      <div class="text-gray-600 text-center py-10">Belum ada kegiatan.</div>
    @else
      <ol class="space-y-5 list-decimal list-inside">
        @foreach($agenda->items as $idx => $it)
        @php
          $assignees = [];
          if ($it->assignees) {
            try {
              $json = json_decode($it->assignees, true);
              if (is_array($json)) {
                foreach ($json['users'] ?? [] as $u) {
                  $assignees[] = $u['name'];
                }
                foreach ($json['manual'] ?? [] as $m) {
                  $assignees[] = $m;
                }
              }
            } catch (\Throwable $e) {}
          }
        @endphp

          <li>
            <div class="pl-1">
              {{-- Badge assignees (opsional) --}}
              @if(!empty($assignees))
                <div class="flex flex-wrap gap-1 mb-2">
                  @foreach($assignees as $name)
                    <span class="inline-flex items-center px-2 py-1 rounded-full
                                text-xs bg-maroon/10 text-maroon ring-1 ring-maroon/30">
                      {{ $name }}
                    </span>
                  @endforeach
                </div>
              @endif


              {{-- Deskripsi dengan mode kalimat --}}
              <div class="text-gray-900">
                @php
                  $desc = $it->description;

                  if (($it->mode ?? 'kepala') === 'kepala') {
                    $desc = "Kepala Brida, ".$it->description;
                  }
                  elseif (($it->mode ?? 'kepala') === 'menugaskan') {
                    if (!empty($assignees)) {
                      $desc = "Menugaskan:\n- ".implode("\n- ", $assignees)."\n\n".$it->description;
                    } else {
                      $desc = "Menugaskan, ".$it->description;
                    }
                  }
                @endphp
                {!! nl2br(e($desc)) !!}
              </div>

              <div class="mt-2 grid sm:grid-cols-2 gap-3 text-sm">
                <div>
                  <div class="font-semibold text-gray-700">Waktu</div>
                  <div class="text-gray-700">{{ $it->time_text }}</div>
                </div>
                <div>
                  <div class="font-semibold text-gray-700">Tempat</div>
                  <div class="text-gray-700">{{ $it->place }}</div>
                </div>
              </div>

              {{-- Dokumen --}}
              @if($it->file_path)
                @php $url = asset('storage/'.$it->file_path); @endphp
                <div class="mt-3 flex items-center gap-3">
                  <a href="{{ $url }}" target="_blank"
                     class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-width="2" d="M12 5v14M5 12h14"/>
                    </svg>
                    Lihat Dokumen
                  </a>
                  <a href="{{ $url }}" download
                     class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">
                    Download
                  </a>
                </div>
              @else
                <div class="mt-3 text-xs text-gray-500 italic">Tidak ada dokumen terlampir.</div>
              @endif
            </div>

            @if(!$loop->last)
              <div class="mt-4 border-t border-gray-200"></div>
            @endif
          </li>
        @endforeach
      </ol>
    @endif

    <div class="mt-8 text-xs text-gray-500">
      Diverifikasi melalui SIGAP AGENDA — {{ config('app.url') }}/sigap-agenda/show?id={{ $agenda->id }}
    </div>
  </div>
</section>
@endsection
@push('scripts')
<script>
  function copyCurrentShowLink(url){
    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(url).then(()=>{
        if (window.Swal) {
          Swal.fire({ icon:'success', title:'Tautan disalin', text:url, timer:1500, showConfirmButton:false });
        } else {
          alert('Tautan disalin:\n' + url);
        }
      }).catch(()=>manualCopy(url));
    } else {
      manualCopy(url);
    }
  }
  function manualCopy(text){
    const ta = document.createElement('textarea');
    ta.value = text;
    ta.setAttribute('readonly','');
    ta.style.position = 'absolute';
    ta.style.left = '-9999px';
    document.body.appendChild(ta);
    ta.select();
    try { document.execCommand('copy'); } catch(e) {}
    document.body.removeChild(ta);
    if (window.Swal) {
      Swal.fire({ icon:'success', title:'Tautan disalin', text:text, timer:1500, showConfirmButton:false });
    } else {
      alert('Tautan disalin:\n' + text);
    }
  }
</script>
@endpush

