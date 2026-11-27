@extends('errors.layout')

@section('code', '403')
@section('title', 'Akses Terbatas')
@section('heading', 'Akses Halaman Ini Terbatas')
@section('message', 'Anda tidak memiliki izin untuk membuka halaman ini. Pastikan Anda masuk menggunakan akun dengan hak akses yang sesuai.')

@section('actions')
    <a href="{{ url('/') }}" class="btn-primary">Kembali ke Beranda</a>
    <a href="{{ url('/masuk') }}" class="btn-secondary">Masuk ke Akun</a>
@endsection
