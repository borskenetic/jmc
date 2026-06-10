@props([
    'id',
    'name',
    'label',
    'autocomplete' => 'current-password',
    'placeholder' => '••••••••',
    'required' => true,
    'minlength' => null,
])

<div {{ $attributes->merge(['class' => 'auth-field']) }}>
    <label for="{{ $id }}">{{ $label }}</label>
    <div class="auth-password-wrap">
        <input
            type="password"
            id="{{ $id }}"
            name="{{ $name }}"
            class="auth-input auth-input--password"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($minlength) minlength="{{ $minlength }}" @endif
            autocomplete="{{ $autocomplete }}"
        >
        <button
            type="button"
            class="auth-password-toggle"
            data-password-target="{{ $id }}"
            aria-label="Show password"
            title="Show password"
        >
            <svg class="auth-password-toggle__icon auth-password-toggle__icon--show" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.75"/>
            </svg>
            <svg class="auth-password-toggle__icon auth-password-toggle__icon--hide" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M10.7 10.7a3 3 0 0 0 4.2 4.2M9.9 5.1A10.8 10.8 0 0 1 12 5c6.5 0 10 7 10 7a17.2 17.2 0 0 1-3.1 4.2M6.2 6.2C3.6 8 2 12 2 12a17.2 17.2 0 0 0 6.5 5.8M3 3l18 18" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
</div>
