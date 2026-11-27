@extends('errors.layout')

@section('code', '404')
@section('title', 'Halaman Tidak Ditemukan')
@section('heading', 'Halaman Tidak Ditemukan')
@section('message', 'Maaf, alamat yang Anda tuju tidak tersedia atau sudah dipindahkan. Silakan kembali ke beranda atau langsung buka kalkulator racikan.')

@section('actions')
    <a href="{{ url('/') }}" class="btn-primary">Kembali ke Beranda</a>
    <a href="{{ url('/kalkulator') }}" class="btn-secondary">Buka Kalkulator</a>
    <a href="{{ url('/daftar-harga') }}" class="btn-secondary">Daftar Harga</a>
@endsection
