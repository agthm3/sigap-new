@extends('layouts.app')

@section('content')
<div>
  <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
    Edit <span class="text-maroon">SIGAP PIC</span>
  </h1>
  <p class="text-sm text-gray-600 mt-0.5">Perbarui data sistem, PIC, dan credential.</p>
</div>

<form action="{{ route('sigap-pic.update', $system->id) }}" method="POST" class="mt-4">
  @method('PUT')
  @include('dashboard.pic._form')
</form>
@endsection