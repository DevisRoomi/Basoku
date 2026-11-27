@extends('errors.layout')

@section('code', '419')
@section('title', 'Sesi Kedaluwarsa')
@section('heading', 'Sesi Halaman Telah Kedaluwarsa')
@section('message', 'Halaman terlalu lama tidak aktif sehingga token keamanan perlu diperbarui. Muat ulang halaman lalu ulangi tindakan Anda.')

@section('actions')
    <button type="button" onclick="window.location.reload()" class="btn-primary">Muat Ulang Halaman</button>
    <a href="{{ url('/') }}" class="btn-secondary">Kembali ke Beranda</a>
@endsection
