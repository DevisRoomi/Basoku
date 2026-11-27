@extends('errors.layout')

@section('code', '500')
@section('title', 'Kendala Teknis Sementara')
@section('heading', 'Terjadi Kendala Teknis Sementara')
@section('message', 'Sistem BASOKU sedang mengalami gangguan singkat. Silakan coba muat ulang beberapa saat lagi atau hubungi kios langsung melalui WhatsApp.')

@section('actions')
    <button type="button" onclick="window.location.reload()" class="btn-primary">Coba Muat Ulang</button>
    <a href="{{ url('/') }}" class="btn-secondary">Kembali ke Beranda</a>
@endsection
