@extends('layouts.app')
@section('title', 'Laporan Absensi')
@section('breadcrumb')
    <span class="separator">/</span>
    <span class="current">Laporan Absensi</span>
@endsection

@section('content')
<div class="page-header d-flex justify-between align-center">
    <div>
        <h1>Laporan Absensi Siswa</h1>
        <p>Pantau rekap kehadiran siswa per mata pelajaran.</p>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.attendances.index') }}" method="GET" class="d-flex gap-md align-center flex-wrap">
            <div style="flex:1; min-width: 200px;">
                <label class="form-label">Kelas</label>
                <select name="classroom_id" class="form-control form-select">
                    @foreach($classrooms as $c)
                        <option value="{{ $c->id }}" {{ $selectedClassroomId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1; min-width: 200px;">
                <label class="form-label">Mata Pelajaran</label>
                <select name="subject_id" class="form-control form-select">
                    @foreach($subjects as $s)
                        <option value="{{ $s->id }}" {{ $selectedSubjectId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1; min-width: 200px;">
                <label class="form-label">Tanggal</label>
                <input type="date" name="date" class="form-control" value="{{ $date }}" max="{{ now()->format('Y-m-d') }}">
            </div>
            <div style="margin-top: 28px;">
                <button type="submit" class="btn btn-primary"><i class="ri-search-line"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="stats-grid mb-4" style="grid-template-columns: repeat(4, 1fr);">
    <div class="stat-card" style="background:var(--success-50); border:1px solid var(--success-200);">
        <div class="stat-card-icon" style="color:var(--success-600); background:var(--success-100);"><i class="ri-check-line"></i></div>
        <div class="stat-card-info">
            <div class="stat-card-label" style="color:var(--success-700);">Hadir</div>
            <div class="stat-card-value" style="color:var(--success-800);">{{ $summary['hadir'] }}</div>
        </div>
    </div>
    <div class="stat-card" style="background:var(--warning-50); border:1px solid var(--warning-200);">
        <div class="stat-card-icon" style="color:var(--warning-600); background:var(--warning-100);"><i class="ri-hospital-line"></i></div>
        <div class="stat-card-info">
            <div class="stat-card-label" style="color:var(--warning-700);">Sakit</div>
            <div class="stat-card-value" style="color:var(--warning-800);">{{ $summary['sakit'] }}</div>
        </div>
    </div>
    <div class="stat-card" style="background:var(--primary-50); border:1px solid var(--primary-200);">
        <div class="stat-card-icon" style="color:var(--primary-600); background:var(--primary-100);"><i class="ri-mail-send-line"></i></div>
        <div class="stat-card-info">
            <div class="stat-card-label" style="color:var(--primary-700);">Izin</div>
            <div class="stat-card-value" style="color:var(--primary-800);">{{ $summary['izin'] }}</div>
        </div>
    </div>
    <div class="stat-card" style="background:var(--danger-50); border:1px solid var(--danger-200);">
        <div class="stat-card-icon" style="color:var(--danger-600); background:var(--danger-100);"><i class="ri-close-line"></i></div>
        <div class="stat-card-info">
            <div class="stat-card-label" style="color:var(--danger-700);">Alpa</div>
            <div class="stat-card-value" style="color:var(--danger-800);">{{ $summary['alpa'] }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Detail Kehadiran</h3>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Siswa</th>
                    <th>NISN</th>
                    <th>Status Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances->sortBy(function($a) { return $a->student->name ?? ''; }) as $index => $attendance)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $attendance->student->name }}</strong></td>
                        <td>{{ $attendance->student->nisn }}</td>
                        <td>
                            @if($attendance->status == 'hadir')
                                <span class="badge badge-success">Hadir</span>
                            @elseif($attendance->status == 'sakit')
                                <span class="badge badge-warning">Sakit</span>
                            @elseif($attendance->status == 'izin')
                                <span class="badge badge-primary">Izin</span>
                            @else
                                <span class="badge badge-danger">Alpa</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Belum ada data absensi untuk filter ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
