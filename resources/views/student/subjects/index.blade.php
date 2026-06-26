@extends('layouts.app')
@section('title', 'Mata Pelajaran')
@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}"><i class="ri-home-4-line"></i></a><span class="separator">/</span>
    <span class="current">Mata Pelajaran</span>
@endsection
@section('content')
<div class="page-header"><h1>Mata Pelajaran Saya</h1><p>Daftar mapel yang tersedia untuk kelas Anda.</p></div>
<div class="d-grid gap-md" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));">
    @forelse($subjects as $subject)
        <div class="card" style="transition: transform .2s, box-shadow .2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 25px rgba(0,0,0,.1)'" onmouseout="this.style.transform='none';this.style.boxShadow='none'">
            <div class="card-body">
                <div class="d-flex align-center gap-sm mb-2">
                    <div style="width:48px;height:48px;border-radius:var(--radius-md);background:linear-gradient(135deg,var(--primary-500),var(--primary-700));display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.4rem;">
                        <i class="ri-book-open-line"></i>
                    </div>
                    <div>
                        <h3 style="margin:0;font-size:1.1rem;">{{ $subject->name }}</h3>
                        <span class="text-sm text-muted">{{ $subject->code }}</span>
                    </div>
                </div>
                <div class="text-sm text-muted mb-2">
                    <i class="ri-user-line"></i> {{ $subject->teacher_name ?? '-' }}
                    · <span class="badge badge-primary" style="font-size:.7rem;">{{ auth()->user()->student->classroom->name ?? '-' }}</span>
                </div>
                @if($subject->description)
                    <p class="text-sm" style="color:var(--text-secondary);margin-bottom:12px;">{{ \Illuminate\Support\Str::limit($subject->description, 100) }}</p>
                @endif
                <a href="{{ route('student.subjects.show', $subject->id) }}" class="btn btn-sm btn-primary w-full"><i class="ri-arrow-right-line"></i> Masuk Kelas</a>
            </div>
        </div>
    @empty
        <div class="text-center text-muted py-4" style="grid-column: 1 / -1;">
            <i class="ri-book-open-line" style="font-size:3rem;opacity:.3;display:block;margin-bottom:8px;"></i>
            Belum ada mata pelajaran yang ditugaskan untuk kelas Anda.
        </div>
    @endforelse
</div>
@endsection
