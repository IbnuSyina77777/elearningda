@extends('layouts.guest')

@section('title', 'E-Learning SMK — Sistem Pembayaran Digital Sekolah')
@section('description', 'Platform pembayaran digital terintegrasi untuk Sekolah Menengah Kejuruan. Bayar PTS, PAS, Ujikom, dan Kunjungan Industri secara online.')

@section('content')
<div class="landing-page">
    {{-- Navigation --}}
    <nav class="landing-nav" id="landingNav">
        <a href="/" class="landing-nav-brand">
            @if(setting('app_logo'))
                <img src="{{ asset('storage/' . setting('app_logo')) }}" alt="Logo" style="height: 32px; border-radius: 4px; background: #fff; padding: 2px;">
            @else
                <span class="icon" style="background: rgba(255,255,255,0.2);"><i class="ri-government-line"></i></span>
            @endif
            <span>{{ setting('school_name', setting('app_name', 'SMK BISA')) }}</span>
        </a>
        <a href="{{ route('login') }}" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);backdrop-filter:blur(8px);">
            <i class="ri-login-box-line"></i> Masuk Portal
        </a>
    </nav>

    {{-- Hero Section --}}
    <section class="landing-hero">
        <div class="hero-content">
            <h1>
                {!! nl2br(e(setting('landing_title', 'Selamat Datang di SMK Bisa Hebat'))) !!}
            </h1>
            <p>
                {{ setting('landing_tagline', 'Mewujudkan Generasi Kompeten, Inovatif, dan Siap Kerja di Era Digital.') }}
            </p>
            <div class="d-flex gap-md justify-center flex-wrap">
                <a href="{{ route('login') }}" class="btn btn-white">
                    <i class="ri-macbook-line"></i> Masuk E-Learning
                </a>
            </div>
        </div>
    </section>

    {{-- About Section --}}
    <section class="landing-about">
        <div class="container text-center">
            <h2>Tentang Sekolah Kami</h2>
            <div class="divider"></div>
            <p class="about-text">
                {{ setting('landing_about', 'Kami adalah institusi pendidikan kejuruan yang berkomitmen mencetak lulusan unggul dan berkarakter, didukung oleh fasilitas modern dan tenaga pengajar profesional.') }}
            </p>
        </div>
    </section>

    {{-- Majors Section --}}
    @php
        $majorsString = setting('landing_majors', 'Rekayasa Perangkat Lunak, Teknik Komputer Jaringan, Akuntansi dan Keuangan Lembaga, Otomatisasi Tata Kelola Perkantoran');
        $majors = array_filter(array_map('trim', explode(',', $majorsString)));
        $icons = ['ri-code-box-line', 'ri-router-line', 'ri-wallet-3-line', 'ri-building-4-line', 'ri-camera-lens-line', 'ri-tools-line'];
    @endphp
    @if(count($majors) > 0)
    <section class="landing-majors">
        <div class="container">
            <h2 class="text-center">Program Keahlian</h2>
            <p class="subtitle text-center">Pilihan jurusan untuk menyiapkan masa depan karir gemilang</p>
            
            <div class="majors-grid">
                @foreach($majors as $index => $major)
                    <div class="major-card">
                        <div class="major-icon">
                            <i class="{{ $icons[$index % count($icons)] }}"></i>
                        </div>
                        <h3>{{ $major }}</h3>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Portals / Digital Features Section --}}
    <section class="landing-features" style="background: #fff;">
        <div class="container">
            <h2>Layanan Digital Sekolah</h2>
            <p class="subtitle">Terintegrasi dalam satu portal untuk kemudahan Civitas Akademika</p>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-card-icon" style="background: var(--primary-100); color: var(--primary-600);">
                        <i class="ri-book-open-line"></i>
                    </div>
                    <h3>E-Learning Interaktif</h3>
                    <p>Akses materi pembelajaran, modul digital, dan penugasan langsung dari genggaman siswa.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-card-icon" style="background: var(--success-100); color: var(--success-600);">
                        <i class="ri-secure-payment-line"></i>
                    </div>
                    <h3>Pembayaran Online</h3>
                    <p>Orang tua dapat mengecek tagihan dan membayar secara online langsung lewat Virtual Account & E-Wallet.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-card-icon" style="background: var(--warning-100); color: var(--warning-600);">
                        <i class="ri-bar-chart-box-line"></i>
                    </div>
                    <h3>Rekapitulasi Nilai & Absen</h3>
                    <p>Pantau perkembangan akademik siswa secara transparan, lengkap dengan kehadiran harian.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="landing-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3 class="footer-brand">
                        <i class="ri-government-line"></i> {{ setting('school_name', setting('app_name', 'SMK BISA')) }}
                    </h3>
                    <p class="footer-desc">{{ setting('landing_about', 'Pendidikan kejuruan terdepan dengan fasilitas modern.') }}</p>
                </div>
                <div class="footer-col">
                    <h4>Kontak Kami</h4>
                    <ul class="footer-contact">
                        <li><i class="ri-map-pin-2-line"></i> {{ setting('school_address', 'Jl. Pendidikan No. 123, Kota Pelajar') }}</li>
                        <li><i class="ri-mail-line"></i> {{ setting('school_email', 'info@smkbisa.sch.id') }}</li>
                        <li><i class="ri-phone-line"></i> {{ setting('school_phone', '(021) 1234567') }}</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>{{ setting('app_copyright', '&copy; ' . date('Y') . ' ' . setting('school_name', 'SMK BISA') . '. Semua hak dilindungi.') }}</p>
            </div>
        </div>
    </footer>
</div>
@endsection
