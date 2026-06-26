@extends('layouts.guest')

@section('title', 'Masuk — E-Learning SMK')

@section('content')
<div class="guest-layout">
    <div class="login-card">
        <div class="login-logo">
            @if(setting('app_logo'))
                <img src="{{ asset('storage/' . setting('app_logo')) }}" alt="Logo" style="height: 48px; border-radius: 8px; margin-bottom: 12px; display: inline-block;">
            @else
                <div class="login-logo-icon">🎓</div>
            @endif
            <h2>{{ setting('app_name', 'EduPay') }}</h2>
            <p>Masuk ke akun Anda</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger" style="margin-bottom: 20px;">
                <span class="alert-icon"><i class="ri-error-warning-line"></i></span>
                <div class="alert-content">{{ session('error') }}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email <span class="required">*</span></label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="nama@email.com"
                    value="{{ old('email') }}"
                    required
                    autofocus
                >
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password <span class="required">*</span></label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="••••••••"
                    required
                >
                @error('password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" style="display:flex;align-items:center;gap:8px;">
                <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember" style="font-size:.85rem;color:var(--text-secondary);cursor:pointer;margin:0;">Ingat saya</label>
            </div>

            <button type="submit" class="btn btn-primary btn-lg">
                <i class="ri-login-box-line"></i> Masuk
            </button>
        </form>

        <div style="text-align:center;margin-top:24px;">
            <a href="/" style="font-size:.85rem;color:var(--text-secondary);">
                <i class="ri-arrow-left-line"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
