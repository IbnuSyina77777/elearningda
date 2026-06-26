@extends('layouts.guest')

@section('title', 'E-Learning SMK — Sistem Pembayaran Digital Sekolah')
@section('description', 'Platform pembayaran digital terintegrasi untuk Sekolah Menengah Kejuruan. Bayar PTS, PAS, Ujikom, dan Kunjungan Industri secara online.')

@section('content')
<div class="landing-page">
    {{-- Navigation --}}
    <nav class="landing-nav" id="landingNav">
        <a href="/" class="landing-nav-brand">
            @if(setting('app_logo'))
                <img src="{{ asset('storage/' . setting('app_logo')) }}" alt="Logo" style="height: 24px; border-radius: 4px; background: #fff;">
            @else
                <span class="icon">🎓</span>
            @endif
            <span>{{ setting('app_name', 'EduPay') }}</span>
        </a>
        <a href="{{ route('login') }}" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);backdrop-filter:blur(8px);">
            <i class="ri-login-box-line"></i> Masuk
        </a>
    </nav>

    {{-- Hero Section --}}
    <section class="landing-hero">
        <div>
            <h1>
                Pembayaran Sekolah<br>
                <span class="accent">Lebih Mudah & Transparan</span>
            </h1>
            <p>
                Platform pembayaran digital untuk siswa SMK. Bayar tagihan PTS, PAS, Ujikom,
                dan Kunjungan Industri secara online dengan mudah dan aman.
            </p>
            <div class="d-flex gap-md justify-center flex-wrap">
                <a href="{{ route('login') }}" class="btn btn-white">
                    <i class="ri-login-box-line"></i> Masuk Sekarang
                </a>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="landing-features">
        <h2>Fitur Unggulan</h2>
        <p class="subtitle">Sistem pembayaran sekolah yang modern, aman, dan mudah digunakan</p>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-card-icon">
                    <i class="ri-secure-payment-line"></i>
                </div>
                <h3>Pembayaran Online</h3>
                <p>Bayar tagihan sekolah kapan saja dan di mana saja melalui berbagai metode pembayaran digital.</p>
            </div>

            <div class="feature-card">
                <div class="feature-card-icon">
                    <i class="ri-split-cells-horizontal"></i>
                </div>
                <h3>Cicilan Fleksibel</h3>
                <p>Dukung pembayaran bertahap/cicilan sehingga orang tua tidak perlu membayar sekaligus.</p>
            </div>

            <div class="feature-card">
                <div class="feature-card-icon">
                    <i class="ri-bar-chart-grouped-line"></i>
                </div>
                <h3>Laporan Real-time</h3>
                <p>Pantau status pembayaran secara real-time. Riwayat transaksi lengkap dan transparan.</p>
            </div>

            <div class="feature-card">
                <div class="feature-card-icon">
                    <i class="ri-notification-3-line"></i>
                </div>
                <h3>Notifikasi Otomatis</h3>
                <p>Dapatkan pengingat otomatis untuk tagihan yang mendekati jatuh tempo.</p>
            </div>

            <div class="feature-card">
                <div class="feature-card-icon">
                    <i class="ri-shield-check-line"></i>
                </div>
                <h3>Aman & Terpercaya</h3>
                <p>Terintegrasi dengan payment gateway terpercaya untuk keamanan transaksi maksimal.</p>
            </div>

            <div class="feature-card">
                <div class="feature-card-icon">
                    <i class="ri-smartphone-line"></i>
                </div>
                <h3>Responsive Design</h3>
                <p>Akses dari perangkat apa pun — smartphone, tablet, atau komputer dengan tampilan optimal.</p>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="landing-footer">
        <p><strong>{{ setting('app_name', 'EduPay') }}</strong> — Sistem Pembayaran Digital Sekolah</p>
        <p style="margin-top: 8px;">{{ setting('app_copyright', '&copy; ' . date('Y') . ' SMK E-Learning. Semua hak dilindungi.') }}</p>
    </footer>
</div>
@endsection
