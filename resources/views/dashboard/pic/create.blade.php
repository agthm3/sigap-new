@extends('layouts.app')

@section('content')
<div>
  <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
    Tambah <span class="text-maroon">SIGAP PIC</span>
  </h1>
  <p class="text-sm text-gray-600 mt-0.5">Tambahkan sistem baru beserta PIC dan akun terkait.</p>
</div>

<form action="{{ route('sigap-pic.store') }}" method="POST" class="mt-4">
  @include('dashboard.pic._form')
</form>
@endsection