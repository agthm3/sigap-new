@extends('layouts.app')

@push('head')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
  <style>
    /* Mengamankan agar Bootstrap tidak merusak Tailwind di Sidebar/Header SIGAP */
    body { font-size: 0.875rem; background-color: #f9fafb; }
    .table-responsive { background: white; border-radius: 1rem; padding: 1rem; border: 1px solid #e5e7eb; }
    .btn-primary { background-color: #7a2222 !important; border-color: #7a2222 !important; }
    .btn-primary:hover { background-color: #661b1b !important; border-color: #661b1b !important; }
    .badge { padding: 0.4em 0.6em; border-radius: 9999px; }
  </style>
@endpush

@section('content')
<section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between mb-4">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      SIGAP <span class="text-maroon">LOGS</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Pemantauan *error* sistem dan aktivitas laravel.log di server.
    </p>
  </div>
</section>

<div class="mt-4">
  <div class="row">
    <div class="col-md-3 mb-4">
      <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
        @if(count($files))
          <h5 class="text-sm font-bold text-gray-700 mb-3 px-2">Daftar File Log</h5>
          <div class="list-group">
            @foreach($files as $file)
              <a href="?l={{ \Illuminate\Support\Facades\Crypt::encrypt($file) }}"
                 class="list-group-item text-sm {{ $current_file == $file ? 'llv-active bg-maroon text-white border-maroon' : 'text-gray-600' }}">
                {{ $file }}
              </a>
            @endforeach
          </div>
        @else
          <p class="text-sm text-gray-500 text-center">Tidak ada log.</p>
        @endif
      </div>
    </div>

    <div class="col-md-9">
      @if ($logs === null)
        <div class="alert alert-danger">
          Log file >50M, silakan download file untuk melihatnya.
        </div>
      @else
        <div class="table-responsive shadow-sm">
          <div class="d-flex justify-content-between mb-3">
            <div>
               @if($current_file)
                <a href="?dl={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}" class="btn btn-sm btn-outline-secondary">
                  <span class="fa fa-download"></span> Download
                </a>
                <a href="?del={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}" class="btn btn-sm btn-danger ml-2" onclick="return confirm('Yakin hapus file log ini?');">
                  <span class="fa fa-trash"></span> Hapus File
                </a>
              @endif
            </div>
          </div>
          
          <table id="table-log" class="table table-striped table-sm text-sm" data-ordering-index="{{ $standardFormat ? 2 : 0 }}">
            <thead>
              <tr>
                @if ($standardFormat)
                  <th>Level</th>
                  <th>Konteks</th>
                  <th>Tanggal</th>
                @else
                  <th>Baris</th>
                @endif
                <th>Konten</th>
              </tr>
            </thead>
            <tbody>
              @foreach($logs as $key => $log)
                <tr>
                  @if ($standardFormat)
                    <td class="text-{{ $log['level_class'] }}">
                      <span class="fa fa-{{ $log['level_img'] }}" aria-hidden="true"></span> &nbsp;{{ $log['level'] }}
                    </td>
                    <td>{{ $log['context'] }}</td>
                    <td class="date">{{ $log['date'] }}</td>
                  @endif
                  <td class="text">
                    @if ($log['stack'])
                      <button type="button" class="btn btn-sm btn-light float-right"
                              data-toggle="collapse" data-target="#collapse-{{ $key }}">
                        Lihat Stack
                      </button>
                    @endif
                    {{ $log['text'] }}
                    @if ($log['in_file'])
                      <br/>{{ $log['in_file'] }}
                    @endif
                    @if ($log['stack'])
                      <div class="collapse mt-2" id="collapse-{{ $key }}">
                        <pre class="bg-gray-100 p-2 rounded text-xs overflow-x-auto">{{ trim($log['stack']) }}</pre>
                      </div>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection

@push('scripts')
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush