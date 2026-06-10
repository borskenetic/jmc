@extends('layouts.auth')

@section('title', 'New password')

@section('content')
    <a href="{{ route('login') }}" class="auth-back" aria-label="Back to sign in">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M10 12L6 8l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Back to sign in
    </a>

    <h1 class="auth-card__title">Choose a new password</h1>
    <p class="auth-card__subtitle">Enter your email and a new password below. Password must be at least 6 characters.</p>

    <form method="POST" action="{{ route('password.store') }}" novalidate>
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="auth-field">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                class="auth-input"
                value="{{ old('email', $request->email) }}"
                required
                autofocus
                autocomplete="username"
            >
        </div>

        @include('auth.partials.password-field', [
            'id' => 'password',
            'name' => 'password',
            'label' => 'New password',
            'autocomplete' => 'new-password',
            'placeholder' => 'At least 6 characters',
            'minlength' => 6,
        ])
        @error('password')
            <div class="auth-alert auth-alert--error" role="alert" style="margin-top: -0.5rem; margin-bottom: 1rem;">{{ $message }}</div>
        @enderror

        @include('auth.partials.password-field', [
            'id' => 'password_confirmation',
            'name' => 'password_confirmation',
            'label' => 'Confirm password',
            'autocomplete' => 'new-password',
            'minlength' => 6,
        ])

        @error('email')
            <div class="auth-alert auth-alert--error" role="alert">{{ $message }}</div>
        @enderror

        <button type="submit" class="auth-btn auth-btn--primary">Update password</button>
    </form>
@endsection
