@extends('layouts.auth')

@section('title', 'Forgot password')

@section('content')
    <a href="{{ route('login') }}" class="auth-back" aria-label="Back to sign in">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M10 12L6 8l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Back to sign in
    </a>

    <h1 class="auth-card__title">Reset your password</h1>
    <p class="auth-card__subtitle">
        Enter the email address on your account and we&rsquo;ll send you a link to choose a new password.
    </p>

    @if (session('status'))
        <div class="auth-alert auth-alert--success" role="status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf

        <div class="auth-field">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                class="auth-input"
                value="{{ old('email') }}"
                placeholder="you@school.edu"
                required
                autofocus
                autocomplete="username"
            >
        </div>

        @error('email')
            <div class="auth-alert auth-alert--error" role="alert">{{ $message }}</div>
        @enderror

        <button type="submit" class="auth-btn auth-btn--primary">Send reset link</button>
    </form>
@endsection
