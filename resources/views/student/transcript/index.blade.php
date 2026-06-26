@extends('layouts.app')
@section('title', 'Transkrip Nilai - ' . auth()->user()->name)
@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}"><i class="ri-home-4-line"></i></a><span class="separator">/</span>
    <span class="current">Transkrip Nilai</span>
@endsection

@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-md">
    <div>
        <h1>Transkrip Nilai</h1>
        <p>Rapor capaian akhir per mata pelajaran</p>
    </div>
    <div>
        <form action="{{ route('student.transcript.index') }}" method="GET">
            <select name="academic_year_id" class="form-control form-select" onchange="this.form.submit()" style="min-width:200px;">
                @foreach($academicYears as $y)
                    <option value="{{ $y->id }}" {{ $selectedYearId == $y->id ? 'selected' : '' }}>Semester: {{ $y->full_label }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

<div class="card mb-4" style="border-top: 4px solid var(--primary-600);">
    <div class="card-header">
        <h3 style="margin:0;">Rapor Semester: {{ $selectedYear ? $selectedYear->full_label : '-' }}</h3>
    </div>
    <div class="card-body">
        <div class="d-flex align-center gap-md">
            <div class="avatar-lg" style="width: 64px; height: 64px; border-radius: 50%; background: var(--primary-100); color: var(--primary-700); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold;">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div>
                <h3 style="margin:0 0 4px 0;">{{ auth()->user()->name }}</h3>
                <div class="text-muted">
                    NISN: <strong>{{ auth()->user()->student->nisn }}</strong> &nbsp;|&nbsp; 
                    Kelas: <strong>{{ auth()->user()->student->classroom->name ?? '-' }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Daftar Nilai Akhir</h3>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr style="background: var(--surface);">
                    <th>Mata Pelajaran</th>
                    <th>Guru Pengampu</th>
                    <th class="text-center">Absensi</th>
                    <th class="text-center">PTS</th>
                    <th class="text-center">PAS</th>
                    <th class="text-center">Rata Tugas</th>
                    <th class="text-center" style="background: var(--primary-50); color: var(--primary-800);">NILAI AKHIR</th>
                    <th class="text-center">Predikat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transcripts as $t)
                    <tr>
                        <td>
                            <strong>{{ $t['subject_name'] }}</strong>
                            <div class="text-xs text-muted">{{ $t['subject_code'] }}</div>
                        </td>
                        <td>{{ $t['teacher_name'] }}</td>
                        <td class="text-center">
                            @if($t['attendance_weight'] > 0)
                                <div class="font-bold">{{ $t['attendance_score'] }}</div>
                                <div class="text-xs text-muted">Bbt: {{ $t['attendance_weight'] }}%</div>
                            @else
                                <span class="text-muted text-sm">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($t['pts_weight'] > 0)
                                <div class="font-bold">{{ $t['pts_score'] }}</div>
                                <div class="text-xs text-muted">Bbt: {{ $t['pts_weight'] }}%</div>
                            @else
                                <span class="text-muted text-sm">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($t['pas_weight'] > 0)
                                <div class="font-bold">{{ $t['pas_score'] }}</div>
                                <div class="text-xs text-muted">Bbt: {{ $t['pas_weight'] }}%</div>
                            @else
                                <span class="text-muted text-sm">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($t['assignments_total_weight'] > 0)
                                @php
                                    $avgTugas = round(($t['assignments_sum'] / ($t['assignments_total_weight'] / 100)), 1);
                                @endphp
                                <div class="font-bold">{{ $avgTugas }}</div>
                                <div class="text-xs text-muted">Bbt: {{ $t['assignments_total_weight'] }}%</div>
                            @else
                                <span class="text-muted text-sm">-</span>
                            @endif
                        </td>
                        <td class="text-center" style="background: var(--primary-50); font-size: 1.2rem; font-weight: bold; color: {{ $t['final_score'] >= 75 ? 'var(--success-700)' : 'var(--danger-700)' }};">
                            {{ $t['final_score'] }}
                        </td>
                        <td class="text-center">
                            @php
                                $predClass = 'badge-primary';
                                if($t['predicate'] == 'A' || $t['predicate'] == 'B') $predClass = 'badge-success';
                                elseif($t['predicate'] == 'C') $predClass = 'badge-warning';
                                elseif($t['predicate'] == 'D' || $t['predicate'] == 'E') $predClass = 'badge-danger';
                            @endphp
                            <span class="badge {{ $predClass }}" style="font-size: 1rem; padding: 4px 12px;">{{ $t['predicate'] }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada mata pelajaran atau nilai untuk kelas Anda.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
