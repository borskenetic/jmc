@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/home/faq.css') }}">
@endsection

@section('banner')
    <img src="{{ \App\Support\VersionedAsset::url('images/Bannernew.jpg') }}" alt="Banner" class="banner-img">
@endsection
