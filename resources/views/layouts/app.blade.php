<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ setting('app_name', 'EduPay') }}</title>
    <meta name="description" content="@yield('description', 'Sistem pembayaran digital untuk Sekolah Menengah Kejuruan')">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    @php $themeColor = setting('theme_color'); @endphp
    @if($themeColor)
    <style>
        :root {
            --primary-500: {{ $themeColor }};
            --primary-600: {{ $themeColor }};
        }
    </style>
    @endif
    @stack('styles')
</head>
<body>
    <div class="app-wrapper">
        {{-- Sidebar Overlay (Mobile) --}}
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        {{-- Sidebar --}}
        <aside class="sidebar" id="sidebar">
            {{-- Brand --}}
            <div class="sidebar-brand">
                @if(setting('app_logo'))
                    <div style="width: 32px; height: 32px; border-radius: 6px; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #fff; margin-right: 12px;">
                        <img src="{{ asset('storage/' . setting('app_logo')) }}" alt="Logo" style="max-width: 100%; max-height: 100%;">
                    </div>
                @else
                    <div class="sidebar-brand-icon">🎓</div>
                @endif
                <div class="sidebar-brand-text">
                    <strong>{{ setting('app_name', 'EduPay') }}</strong>
                    <span>SMK Dashboard</span>
                </div>
            </div>

            {{-- Menu --}}
            <nav class="sidebar-menu">
                @if(auth()->user()->isAdmin())
                    {{-- Admin Menu --}}
                    <span class="sidebar-menu-label">Menu Utama</span>
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-dashboard-3-line"></i></span>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.announcements.index') }}" class="sidebar-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-notification-3-line"></i></span>
                        Pengumuman
                    </a>

                    <span class="sidebar-menu-label">Data Master</span>
                    <a href="{{ route('admin.students.index') }}" class="sidebar-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-user-3-line"></i></span>
                        Data Siswa
                    </a>
                    <a href="{{ route('admin.alumni.index') }}" class="sidebar-link {{ request()->routeIs('admin.alumni.*') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-graduation-cap-line"></i></span>
                        Data Alumni
                    </a>
                    <a href="{{ route('admin.teachers.index') }}" class="sidebar-link {{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-user-star-line"></i></span>
                        Data Guru
                    </a>
                    <a href="{{ route('admin.classrooms.index') }}" class="sidebar-link {{ request()->routeIs('admin.classrooms.*') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-building-4-line"></i></span>
                        Data Kelas
                    </a>
                    <a href="{{ route('admin.subjects.index') }}" class="sidebar-link {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-book-open-line"></i></span>
                        Mata Pelajaran
                    </a>

                    <a href="{{ route('admin.schedules.index') }}" class="sidebar-link {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-calendar-todo-line"></i></span>
                        Jadwal Pelajaran
                    </a>
                    <a href="{{ route('admin.academic-years.index') }}" class="sidebar-link {{ request()->routeIs('admin.academic-years.*') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-calendar-event-line"></i></span>
                        Tahun Ajaran
                    </a>

                    <span class="sidebar-menu-label">Akademik & E-Learning</span>
                    <a href="{{ route('admin.attendances.index') }}" class="sidebar-link {{ request()->routeIs('admin.attendances.*') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-calendar-check-line"></i></span>
                        Absensi
                    </a>
                    <a href="{{ route('admin.grades.index') }}" class="sidebar-link {{ request()->routeIs('admin.grades.*') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-bar-chart-box-line"></i></span>
                        Rekap Nilai
                    </a>
                    <a href="{{ route('admin.teacher-administrations.index') }}" class="sidebar-link {{ request()->routeIs('admin.teacher-administrations.*') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-folder-user-line"></i></span>
                        Pantau Administrasi
                    </a>

                    <span class="sidebar-menu-label">Keuangan</span>
                    <a href="{{ route('admin.payment-categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.payment-categories.*') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-price-tag-3-line"></i></span>
                        Kategori Pembayaran
                    </a>
                    <a href="{{ route('admin.bills.index') }}" class="sidebar-link {{ request()->routeIs('admin.bills.*') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-bill-line"></i></span>
                        Tagihan
                    </a>
                    <a href="{{ route('admin.transactions.index') }}" class="sidebar-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-exchange-funds-line"></i></span>
                        Transaksi
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-file-chart-line"></i></span>
                        Laporan Keuangan
                    </a>

                    <span class="sidebar-menu-label">Sistem</span>
                    <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-settings-3-line"></i></span>
                        Pengaturan Aplikasi
                    </a>

                @elseif(auth()->user()->isTeacher())
                    {{-- Teacher Menu --}}
                    <span class="sidebar-menu-label">Menu Utama</span>

                    <a href="{{ route('teacher.dashboard') }}" class="sidebar-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-dashboard-3-line"></i></span>
                        Dashboard
                    </a>

                    <a href="{{ route('teacher.administrations.index') }}" class="sidebar-link {{ request()->routeIs('teacher.administrations.*') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-folder-chart-line"></i></span>
                        Administrasi Mengajar
                    </a>

                    <a href="{{ route('teacher.schedules.index') }}" class="sidebar-link {{ request()->routeIs('teacher.schedules.*') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-calendar-todo-line"></i></span>
                        Jadwal Mengajar
                    </a>

                    <span class="sidebar-menu-label">E-Learning</span>

                    @php
                        $teacherSubjects = auth()->user()->teacher ? auth()->user()->teacher->taughtSubjects()->where('is_active', true)->get() : collect();
                    @endphp

                    <style>
                        .sidebar-menu-details summary::-webkit-details-marker {
                            display: none;
                        }
                        .sidebar-menu-details summary {
                            list-style: none;
                        }
                        .sidebar-menu-details[open] summary .ri-arrow-down-s-line {
                            transform: rotate(180deg);
                        }
                    </style>

                    @foreach($teacherSubjects as $ts)
                        <details class="sidebar-menu-details" {{ request()->segment(3) == $ts->id ? 'open' : '' }}>
                            <summary class="sidebar-link" style="cursor: pointer; padding-right: 12px;">
                                <span class="sidebar-link-icon"><i class="ri-book-2-line"></i></span>
                                {{ Str::limit($ts->name, 18) }}
                                <span class="text-sm text-muted" style="margin-left:auto;font-size:.7rem;margin-right:8px;">Kls {{ $ts->level }}</span>
                                <i class="ri-arrow-down-s-line" style="transition: transform 0.2s;"></i>
                            </summary>
                            <div class="sidebar-submenu" style="padding-left: 1.5rem;">
                                <a href="{{ route('teacher.materials.index', $ts->id) }}" class="sidebar-link {{ request()->routeIs('teacher.materials.*') && request()->segment(3) == $ts->id ? 'active' : '' }}" style="font-size: 0.9rem; padding: 8px 16px;">
                                    <span class="sidebar-link-icon"><i class="ri-file-text-line"></i></span> Materi
                                </a>
                                <a href="{{ route('teacher.assignments.index', $ts->id) }}" class="sidebar-link {{ (request()->routeIs('teacher.assignments.*') || request()->routeIs('teacher.submissions.*')) && request()->segment(3) == $ts->id ? 'active' : '' }}" style="font-size: 0.9rem; padding: 8px 16px;">
                                    <span class="sidebar-link-icon"><i class="ri-task-line"></i></span> Tugas
                                </a>
                                <a href="{{ route('teacher.attendances.index', $ts->id) }}" class="sidebar-link {{ request()->routeIs('teacher.attendances.*') && request()->segment(3) == $ts->id ? 'active' : '' }}" style="font-size: 0.9rem; padding: 8px 16px;">
                                    <span class="sidebar-link-icon"><i class="ri-calendar-check-line"></i></span> Absensi
                                </a>
                                <a href="{{ route('teacher.exams.index', $ts->id) }}" class="sidebar-link {{ request()->routeIs('teacher.exams.*') && request()->segment(3) == $ts->id ? 'active' : '' }}" style="font-size: 0.9rem; padding: 8px 16px;">
                                    <span class="sidebar-link-icon"><i class="ri-test-tube-line"></i></span> Ujian
                                </a>
                                <a href="{{ route('teacher.grades.index', $ts->id) }}" class="sidebar-link {{ request()->routeIs('teacher.grades.*') && request()->segment(3) == $ts->id ? 'active' : '' }}" style="font-size: 0.9rem; padding: 8px 16px;">
                                    <span class="sidebar-link-icon"><i class="ri-bar-chart-line"></i></span> Rekap Nilai
                                </a>
                            </div>
                        </details>
                    @endforeach

                @else
                    {{-- Student Menu --}}
                    <span class="sidebar-menu-label">Menu Utama</span>

                    <a href="{{ route('student.dashboard') }}" class="sidebar-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-dashboard-3-line"></i></span>
                        Dashboard
                    </a>

                    <span class="sidebar-menu-label">E-Learning</span>

                    <a href="{{ route('student.subjects.index') }}" class="sidebar-link {{ request()->routeIs('student.subjects.*') || request()->routeIs('student.assignments.*') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-book-open-line"></i></span>
                        Mata Pelajaran
                    </a>
                    
                    <a href="{{ route('student.transcript.index') }}" class="sidebar-link {{ request()->routeIs('student.transcript.*') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-file-list-3-line"></i></span>
                        Rekap Nilai
                    </a>
                    <a href="{{ route('student.schedules.index') }}" class="sidebar-link {{ request()->routeIs('student.schedules.*') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-calendar-todo-line"></i></span>
                        Jadwal Pelajaran
                    </a>

                    <span class="sidebar-menu-label">Pembayaran</span>

                    <a href="{{ route('student.bills.index') }}" class="sidebar-link {{ request()->routeIs('student.bills.*') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-bill-line"></i></span>
                        Tagihan Saya
                    </a>

                    <a href="{{ route('student.transactions.index') }}" class="sidebar-link {{ request()->routeIs('student.transactions.*') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-exchange-funds-line"></i></span>
                        Riwayat Pembayaran
                    </a>

                    <span class="sidebar-menu-label">Akun</span>

                    <a href="{{ route('student.profile') }}" class="sidebar-link {{ request()->routeIs('student.profile') ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="ri-user-settings-line"></i></span>
                        Profil Saya
                    </a>
                @endif
            </nav>

            {{-- User Footer --}}
            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <div class="sidebar-user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="sidebar-user-info">
                        <strong>{{ auth()->user()->name }}</strong>
                        <span>{{ auth()->user()->role }}</span>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="main-content">
            {{-- Topbar --}}
            <header class="topbar">
                <div class="topbar-left">
                    <button class="topbar-toggle" id="sidebarToggle">
                        <i class="ri-menu-line"></i>
                    </button>
                    <div class="breadcrumb">
                        @yield('breadcrumb')
                    </div>
                </div>

                <div class="topbar-right">
                    <button class="topbar-action" data-tooltip="Notifikasi">
                        <i class="ri-notification-3-line"></i>
                        @if(isset($pendingCount) && $pendingCount > 0)
                            <span class="badge-dot"></span>
                        @endif
                    </button>

                    <div class="dropdown">
                        <button class="topbar-user" id="userDropdownToggle">
                            <div class="topbar-user-avatar">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                            <span class="topbar-user-name">{{ auth()->user()->name }}</span>
                            <i class="ri-arrow-down-s-line" style="color: var(--text-secondary); font-size: .85rem;"></i>
                        </button>
                        <div class="dropdown-menu" id="userDropdownMenu">
                            <a href="{{ auth()->user()->isAdmin() ? '#' : route('student.profile') }}" class="dropdown-item">
                                <i class="ri-user-line"></i> Profil
                            </a>
                            <div class="dropdown-divider"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item" style="width:100%;border:none;background:none;cursor:pointer;text-align:left;">
                                    <i class="ri-logout-box-r-line"></i> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <div class="page-content">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="alert alert-success animate-slide-down" id="flashAlert">
                        <span class="alert-icon"><i class="ri-check-line"></i></span>
                        <div class="alert-content">{{ session('success') }}</div>
                        <button class="alert-close" onclick="this.parentElement.remove()"><i class="ri-close-line"></i></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger animate-slide-down" id="flashAlert">
                        <span class="alert-icon"><i class="ri-error-warning-line"></i></span>
                        <div class="alert-content">
                            <ul style="margin:0; padding-left:20px;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button class="alert-close" onclick="this.parentElement.remove()"><i class="ri-close-line"></i></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger animate-slide-down" id="flashAlert">
                        <span class="alert-icon"><i class="ri-error-warning-line"></i></span>
                        <div class="alert-content">{{ session('error') }}</div>
                        <button class="alert-close" onclick="this.parentElement.remove()"><i class="ri-close-line"></i></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning animate-slide-down" id="flashAlert">
                        <span class="alert-icon"><i class="ri-alert-line"></i></span>
                        <div class="alert-content">{{ session('warning') }}</div>
                        <button class="alert-close" onclick="this.parentElement.remove()"><i class="ri-close-line"></i></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    {{-- Scroll to Top --}}
    <button class="scroll-to-top" id="scrollToTop" title="Kembali ke atas">
        <i class="ri-arrow-up-line"></i>
    </button>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        // Scroll to top button
        (function() {
            const btn = document.getElementById('scrollToTop');
            if (!btn) return;
            window.addEventListener('scroll', function() {
                if (window.scrollY > 300) {
                    btn.classList.add('visible');
                } else {
                    btn.classList.remove('visible');
                }
            });
            btn.addEventListener('click', function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
