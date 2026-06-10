<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sign in') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ \App\Support\Branding::stylesheetUrl() }}">
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/auth/auth.css') }}">
    @stack('head')
</head>
<body class="auth-page">
    <div class="auth-shell">
        <aside class="auth-brand" aria-hidden="true">
            <div class="auth-brand__inner">
                <img src="{{ asset('images/pantasLogo.png') }}" alt="" class="auth-brand__logo">
                <p class="auth-brand__school">{{ config('app.name') }}</p>
                <p class="auth-brand__tagline">Attendance &amp; records portal</p>
            </div>
            <div class="auth-brand__glow"></div>
        </aside>

        <main class="auth-main">
            <div class="auth-card">
                @yield('content')
            </div>
            <p class="auth-footer">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
        </main>
    </div>
    <script>
        document.querySelectorAll('[data-password-target]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(btn.getAttribute('data-password-target'));
                if (!input) return;

                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.classList.toggle('is-visible', show);
                btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                btn.setAttribute('title', show ? 'Hide password' : 'Show password');
            });
        });
    </script>
</body>
</html>
