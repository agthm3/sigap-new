@extends('errors.layout')

@section('title', 'Kesalahan Server Internal')
@section('code', '500')
@section('heading', 'Internal Server Error')
@section('message')
    Terjadi kesalahan sistem di dalam server hosting saat memproses data atau saat mencoba melakukan kompilasi file PDF SPJ. Silakan laporkan kendala ini kepada tim teknis IT BRIDA Kota Makassar.
@endsection