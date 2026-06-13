@extends('errors.layout')

@section('title', 'Metode Tidak Diizinkan')
@section('code', '405')
@section('heading', 'Method Not Allowed')
@section('message')
    Sistem menolak permintaan tindakan ini. Metode pengiriman data tidak sesuai dengan protokol keamanan sistem SIGAP SPJ. Silakan kembali ke form awal dan muat ulang halaman.
@endsection