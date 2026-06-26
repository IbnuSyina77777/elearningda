@extends('layouts.app')
@section('title', 'Dashboard Guru')
@section('breadcrumb')
    <a href="{{ route('teacher.dashboard') }}"><i class="ri-home-4-line"></i></a><span class="separator">/</span>
    <span class="current">Dashboard</span>
@endsection
@section('content')
<div class="page-header">
    <h1>Halo, {{ explode(' ', auth()->user()->name)[0] }}! 👋</h1>
    <p>Selamat datang di portal E-Learning guru.</p>
</div>

@if($announcements->count() > 0)
<div class="card mb-4" style="border-left: 4px solid var(--primary-500); background: #f0f7ff;">
    <div class="card-body p-3">
        <h3 class="mb-2" style="font-size: 1.1rem; color: var(--primary-700);"><i class="ri-notification-3-line"></i> Pengumuman Terbaru</h3>
        <div class="d-flex flex-column gap-sm">
            @foreach($announcements as $ann)
                <div style="background: #fff; padding: 12px 16px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,.05);">
                    <div class="d-flex justify-between align-center mb-1">
                        <strong style="font-size: 1.05rem;">{{ $ann->title }}</strong>
                        <span class="text-xs text-muted">{{ $ann->created_at->diffForHumans() }}</span>
                    </div>
                    <div style="font-size: 0.95rem; color: #4b5563;">
                        {!! nl2br($ann->content) !!}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
    <div class="stat-card" style="background: linear-gradient(135deg, var(--primary-600), var(--primary-800)); color: #fff; border: none;">
        <div class="stat-card-icon" style="background: rgba(255,255,255,.2); color: #fff;"><i class="ri-book-open-line"></i></div>
        <div class="stat-card-info">
            <div class="stat-card-label" style="color: rgba(255,255,255,.7);">Mata Pelajaran</div>
            <div class="stat-card-value" style="color: #fff;">{{ $totalSubjects }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon"><i class="ri-file-text-line"></i></div>
        <div class="stat-card-info">
            <div class="stat-card-label">Total Materi</div>
            <div class="stat-card-value">{{ $totalMaterials }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon"><i class="ri-task-line"></i></div>
        <div class="stat-card-info">
            <div class="stat-card-label">Total Tugas</div>
            <div class="stat-card-value">{{ $totalAssignments }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon" style="background: rgba(245,158,11,.1); color: rgb(245,158,11);"><i class="ri-time-line"></i></div>
        <div class="stat-card-info">
            <div class="stat-card-label">Belum Dinilai</div>
            <div class="stat-card-value">{{ $pendingSubmissions }}</div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header"><h3>Mata Pelajaran Saya</h3></div>
    <div class="card-body">
        <div class="d-grid gap-md" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
            @forelse($subjects as $subject)
                <div class="card" style="border: 1px solid var(--border-color); box-shadow: none;">
                    <div class="card-body">
                        <h3 style="margin: 0 0 4px;">{{ $subject->name }}</h3>
                        <p class="text-sm text-muted mb-2">{{ $subject->code }} · Kelas {{ $subject->level }}</p>
                        <div class="d-flex gap-sm mt-2 flex-wrap">
                            <a href="{{ route('teacher.materials.index', $subject->id) }}" class="btn btn-sm btn-secondary"><i class="ri-file-text-line"></i> Materi</a>
                            <a href="{{ route('teacher.assignments.index', $subject->id) }}" class="btn btn-sm btn-primary"><i class="ri-task-line"></i> Tugas</a>
                            <a href="{{ route('teacher.attendances.index', $subject->id) }}" class="btn btn-sm btn-warning" style="color:#fff;"><i class="ri-calendar-check-line"></i> Absensi</a>
                            <a href="{{ route('teacher.grades.index', $subject->id) }}" class="btn btn-sm btn-success"><i class="ri-bar-chart-line"></i> Rekap Nilai</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4" style="grid-column: 1 / -1;">
                    <i class="ri-book-open-line" style="font-size:3rem;opacity:.3;display:block;margin-bottom:8px;"></i>
                    Belum ada mata pelajaran yang ditugaskan kepada Anda. Hubungi Admin.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
