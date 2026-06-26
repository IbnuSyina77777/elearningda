@extends('layouts.app')
@section('title', 'Penilaian Ujian (PTS & PAS) - ' . $subject->name)
@section('breadcrumb')
    <a href="{{ route('teacher.dashboard') }}"><i class="ri-home-4-line"></i></a><span class="separator">/</span>
    <span class="current">Ujian · {{ $subject->code }}</span>
@endsection

@push('styles')
<style>
    .grade-matrix th, .grade-matrix td {
        vertical-align: middle;
        text-align: center;
        white-space: nowrap;
    }
    .grade-matrix th:first-child, .grade-matrix td:first-child {
        text-align: left;
        position: sticky;
        left: 0;
        background: var(--surface);
        z-index: 2;
        border-right: 2px solid var(--border-color);
    }
    .grade-matrix .weight-input {
        width: 70px;
        text-align: center;
        padding: 4px;
        font-weight: bold;
    }
</style>
@endpush

@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-md">
    <div>
        <h1>Penilaian Ujian (PTS & PAS)</h1>
        <p>Mata Pelajaran: {{ $subject->name }} | Kelas {{ $subject->level }}</p>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-between align-center flex-wrap gap-md">
        <div>
            <h3 style="margin:0;">Matriks Nilai Ujian ({{ $selectedClassroom->name }})</h3>
        </div>
        <div class="d-flex gap-sm align-center">
            <form action="{{ route('teacher.exams.index', $subject->id) }}" method="GET" class="d-flex gap-sm align-center">
                <select name="academic_year_id" class="form-control form-select" onchange="this.form.submit()">
                    @foreach($academicYears as $y)
                        <option value="{{ $y->id }}" {{ $selectedYearId == $y->id ? 'selected' : '' }}>Semester: {{ $y->full_label }}</option>
                    @endforeach
                </select>
                <select name="classroom_id" class="form-control form-select" style="min-width:150px;" onchange="this.form.submit()">
                    @foreach($classrooms as $c)
                        <option value="{{ $c->id }}" {{ $selectedClassroom->id == $c->id ? 'selected' : '' }}>Kelas: {{ $c->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>
    
    <div class="card-body p-0">
        <form action="{{ route('teacher.exams.store', $subject->id) }}" method="POST">
            @csrf
            <input type="hidden" name="classroom_id" value="{{ $selectedClassroom->id }}">
            <input type="hidden" name="academic_year_id" value="{{ $selectedYearId }}">
            
            <div class="table-container" style="border:none;box-shadow:none;border-radius:0;max-height:65vh;overflow:auto;">
                <table class="table grade-matrix">
                    <thead>
                        <tr>
                            <th style="min-width: 250px;">Nama Siswa</th>
                            <th style="min-width: 140px; background: var(--primary-50);">
                                <div class="text-truncate" style="font-weight: 600;">NILAI PTS</div>
                            </th>
                            <th style="min-width: 140px; background: var(--primary-50);">
                                <div class="text-truncate" style="font-weight: 600;">NILAI PAS</div>
                            </th>
                        </tr>
                        <tr style="background: var(--background);">
                            <th><strong>Bobot Persentase (%)</strong></th>
                            <th>
                                <div class="d-flex justify-center align-center gap-xs">
                                    <input type="number" name="pts_weight" class="form-control weight-input" value="{{ $ptsWeight }}" min="0" max="100">
                                    <span class="text-muted text-sm">%</span>
                                </div>
                            </th>
                            <th>
                                <div class="d-flex justify-center align-center gap-xs">
                                    <input type="number" name="pas_weight" class="form-control weight-input" value="{{ $pasWeight }}" min="0" max="100">
                                    <span class="text-muted text-sm">%</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            @php
                                $exam = $examScores->get($student->id);
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-center gap-sm">
                                        <div class="avatar-sm" style="width:32px;height:32px;border-radius:50%;background:var(--primary-100);color:var(--primary-700);display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:.8rem;">
                                            {{ strtoupper(substr($student->name, 0, 2)) }}
                                        </div>
                                        <div style="text-align:left;">
                                            <strong>{{ $student->name }}</strong>
                                            <div class="text-xs text-muted">NISN: {{ $student->nisn }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="background: var(--primary-50);">
                                    <input type="number" name="pts[{{ $student->id }}]" class="form-control text-center mx-auto" style="width: 80px; padding: 6px;" value="{{ $exam ? $exam->pts_score : '' }}" min="0" max="100" placeholder="-">
                                </td>
                                <td style="background: var(--primary-50);">
                                    <input type="number" name="pas[{{ $student->id }}]" class="form-control text-center mx-auto" style="width: 80px; padding: 6px;" value="{{ $exam ? $exam->pas_score : '' }}" min="0" max="100" placeholder="-">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Belum ada data siswa di kelas ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="p-3" style="background: var(--background); border-top: 1px solid var(--border-color); text-align: right;">
                <button type="submit" class="btn btn-primary"><i class="ri-save-3-line"></i> Simpan Nilai Ujian</button>
            </div>
        </form>
    </div>
</div>
@endsection
