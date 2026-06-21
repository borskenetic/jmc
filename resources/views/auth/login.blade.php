@extends('layouts.auth')

@section('title', 'Sign in')

@section('content')
    <a href="{{ route('home') }}" class="auth-back" aria-label="Back to home">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M10 12L6 8l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Back to home
    </a>

    <h1 class="auth-card__title">Welcome back</h1>
    <p class="auth-card__subtitle">Sign in to your account to continue.</p>

    @if (session('status'))
        <div class="auth-alert auth-alert--success" role="status">{{ session('status') }}</div>
    @endif

    @if (session('error'))
        <div class="auth-alert auth-alert--error" role="alert">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <div class="auth-field">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                class="auth-input"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
            >
        </div>

        @include('auth.partials.password-field', [
            'id' => 'password',
            'name' => 'password',
            'label' => 'Password',
            'autocomplete' => 'current-password',
        ])

        <div class="auth-row">
            <label class="auth-check" for="remember">
                <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                Remember me
            </label>
            <a href="{{ route('password.request') }}" class="auth-link">Forgot password?</a>
        </div>

        @error('email')
            <div class="auth-alert auth-alert--error" role="alert">{{ $message }}</div>
        @enderror

        <button type="submit" class="auth-btn auth-btn--primary">Sign in</button>
    </form>
@endsection
