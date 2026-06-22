@extends('layouts.public')

@section('title', 'Visitor pass')

@push('styles')
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/auth/register.css') }}">
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/visitors/register.css') }}">
@endpush

@section('content')
<div class="reg-page visitor-pass-page">
    <div class="reg-wrap">
        <div class="visitor-pass-card">
            <div class="visitor-pass-card__badge">Visitor pass</div>
            <h1 class="visitor-pass-card__name">{{ $visitor->fullName() }}</h1>
            @if($visitor->organization)
                <p class="visitor-pass-card__org">{{ $visitor->organization }}</p>
            @endif
            @if($visitor->purpose)
                <p class="visitor-pass-card__purpose">{{ $visitor->purpose }}</p>
            @endif

            <div class="visitor-pass-card__qr">
                <img src="data:image/png;base64,{{ $qrBase64 }}" alt="Visitor QR code" width="280" height="280">
            </div>
            <p class="visitor-pass-card__code">{{ $visitor->qrcode }}</p>
            <p class="visitor-pass-card__hint">Scan this code at the gate terminal to check in and out.</p>

            <div class="visitor-pass-card__actions">
                <button type="button" class="btn btn-primary" onclick="window.print()">Print pass</button>
                <a href="{{ route('attendance.scan') }}" target="_blank" rel="noopener" class="btn btn-outline-secondary">Open gate terminal</a>
                <a href="{{ route('visitors.register') }}" class="btn btn-link">Register another visitor</a>
            </div>
        </div>
    </div>
</div>
@endsection
