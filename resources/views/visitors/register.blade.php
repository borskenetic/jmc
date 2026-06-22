@extends('layouts.public')

@section('title', 'Visitor registration')

@push('styles')
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/auth/register.css') }}">
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/visitors/register.css') }}">
@endpush

@section('content')
<div class="reg-page visitor-reg-page">
    <div class="reg-wrap">
        <header class="reg-hero">
            <img src="{{ asset('images/pantasLogo.png') }}" alt="{{ config('app.name') }}" class="reg-hero__logo">
            <div>
                <h1 class="reg-hero__title">Visitor registration</h1>
                <p class="reg-hero__subtitle">Register to receive your visitor QR pass. Scan it at the gate terminal to check in and out.</p>
            </div>
        </header>

        <div class="reg-sheet">
            @if($errors->any())
                <div class="reg-notice reg-notice--error" role="alert">
                    <strong>Please fix the following:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="reg-form" method="POST" action="{{ route('visitors.store') }}">
                @csrf

                <div class="reg-block">
                    <h2 class="reg-block__title">Your details</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="firstname">First name</label>
                            <input type="text" id="firstname" name="firstname" class="form-control" value="{{ old('firstname') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="lastname">Last name</label>
                            <input type="text" id="lastname" name="lastname" class="form-control" value="{{ old('lastname') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="organization">Organization / affiliation</label>
                            <input type="text" id="organization" name="organization" class="form-control" value="{{ old('organization') }}" placeholder="Optional">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="purpose">Purpose of visit</label>
                            <input type="text" id="purpose" name="purpose" class="form-control" value="{{ old('purpose') }}" placeholder="e.g. Research, meeting">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="mobile_number">Mobile number</label>
                            <input type="text" id="mobile_number" name="mobile_number" class="form-control" value="{{ old('mobile_number') }}" placeholder="Optional">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Optional">
                        </div>
                    </div>
                </div>

                <div class="visitor-reg-note">
                    After submitting, you will receive a <strong>visitor QR code</strong>. Show or scan it at the gate terminal.
                </div>

                <div class="reg-actions">
                    <button type="submit" class="btn btn-primary reg-submit">Register &amp; get QR pass</button>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary">Back to home</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
