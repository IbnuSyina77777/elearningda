@extends('layouts.app')
@section('title', 'Rekap Penilaian Keseluruhan')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a><span class="separator">/</span>
    <span class="current">Rekap Penilaian</span>
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
    .grade-excellent { color: var(--success-600); font-weight: bold; }
    .grade-good { color: var(--text-primary); font-weight: bold; }
    .grade-warning { color: var(--warning-600); font-weight: bold; }
    .grade-danger { color: var(--danger-600); font-weight: bold; }
    .avg-row td {
        background-color: var(--primary-50);
        font-weight: bold;
    }
</style>
@endpush

@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-md">
    <div>
        <h1>Rekap Penilaian Keseluruhan</h1>
        <p>Pantau seluruh hasil belajar siswa berdasarkan kelas dan mata pelajaran.</p>
    </div>
    @if($selectedClassroom && $selectedSubject)
        <a href="{{ route('admin.grades.export', ['classroom_id' => $selectedClassroom->id, 'subject_id' => $selectedSubject->id, 'academic_year_id' => $selectedYearId]) }}" class="btn btn-outline">
            <i class="ri-file-excel-2-line text-success"></i> Export Excel
        </a>
    @endif
</div>

<div class="card mb-lg">
    <div class="card-body">
        <form action="{{ route('admin.grades.index') }}" method="GET" class="d-flex flex-wrap gap-md align-end">
            <div style="flex:1; min-width: 200px;">
                <label class="form-label">Tahun Ajaran / Semester</label>
                <select name="academic_year_id" class="form-control form-select" onchange="this.form.submit()">
                    @foreach($academicYears as $y)
                        <option value="{{ $y->id }}" {{ $selectedYearId == $y->id ? 'selected' : '' }}>{{ $y->full_label }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1; min-width: 200px;">
                <label class="form-label">Kelas</label>
                <select name="classroom_id" class="form-control form-select" onchange="this.form.submit()">
                    @foreach($classrooms as $c)
                        <option value="{{ $c->id }}" {{ ($selectedClassroom && $selectedClassroom->id == $c->id) ? 'selected' : '' }}>{{ $c->name }} (Level {{ $c->level }})</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1; min-width: 200px;">
                <label class="form-label">Mata Pelajaran</label>
                <select name="subject_id" class="form-control form-select" onchange="this.form.submit()">
                    @foreach($subjects as $s)
                        <option value="{{ $s->id }}" {{ ($selectedSubject && $selectedSubject->id == $s->id) ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

@if($selectedClassroom && $selectedSubject)
<div class="card">
    <div class="card-header d-flex justify-between align-center flex-wrap gap-md">
        <div>
            <h3 style="margin:0;">Matriks Nilai: {{ $selectedClassroom->name }} - {{ $selectedSubject->name }}</h3>
            <p class="text-sm text-muted mb-0">Total bobot yang diinput: <strong class="{{ ($assignments->sum('weight') + $attendanceWeight) > 100 ? 'text-danger' : 'text-success' }}">{{ $assignments->sum('weight') + $attendanceWeight }}%</strong> dari Maks 100%</p>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-container" style="border:none;box-shadow:none;border-radius:0;max-height:65vh;overflow:auto;">
            <table class="table grade-matrix">
                <thead>
                    <tr>
                        <th style="min-width: 250px;">Nama Siswa</th>
                        <th style="min-width: 140px;">
                            <div class="text-truncate" style="max-width: 140px; font-weight: 600;" title="Absensi/Kehadiran">NILAI ABSENSI</div>
                            <div class="text-xs text-muted" style="font-weight:normal;">{{ $totalSessions }} Pertemuan</div>
                        </th>
                        <th style="min-width: 110px;">
                            <div class="text-truncate" style="max-width: 110px; font-weight: 600;">NILAI PTS</div>
                            <div class="text-xs text-muted" style="font-weight:normal;">(Bobot: {{ $ptsWeight }}%)</div>
                        </th>
                        <th style="min-width: 110px;">
                            <div class="text-truncate" style="max-width: 110px; font-weight: 600;">NILAI PAS</div>
                            <div class="text-xs text-muted" style="font-weight:normal;">(Bobot: {{ $pasWeight }}%)</div>
                        </th>
                        @foreach($assignments as $a)
                            <th style="min-width: 140px;">
                                <div class="text-truncate" style="max-width: 140px; font-weight: 600;" title="{{ $a->title }}">{{ Str::limit($a->title, 15) }}</div>
                                <div class="text-xs text-muted" style="font-weight:normal;" title="Tenggat Waktu: {{ $a->due_date->format('d M Y, H:i') }}">
                                    {{ $a->due_date->format('d/m/Y') }}
                                </div>
                            </th>
                        @endforeach
                        <th style="min-width: 120px; background-color: var(--primary-100); color: var(--primary-800);">NILAI AKHIR</th>
                    </tr>
                    <tr style="background: var(--background);">
                        <th><strong>Bobot Persentase (%)</strong></th>
                        <th>
                            <div class="font-bold">{{ $attendanceWeight }}%</div>
                        </th>
                        <th colspan="2" class="text-center text-muted text-sm">
                            <em>Diatur via menu Ujian</em>
                        </th>
                        @foreach($assignments as $a)
                            <th>
                                <div class="font-bold">{{ $a->weight }}%</div>
                            </th>
                        @endforeach
                        <th style="background-color: var(--primary-50);">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
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
                            
                            <td class="grade-cell" style="background: var(--surface);">
                                @php
                                    $attScore = $finalGrades[$student->id]['attendance_score'] ?? 0;
                                    $aClass = $attScore >= 90 ? 'grade-excellent' : ($attScore >= 75 ? 'grade-good' : ($attScore >= 60 ? 'grade-warning' : 'grade-danger'));
                                @endphp
                                <span class="{{ $aClass }}">{{ $attScore }}</span>
                            </td>

                            <td class="grade-cell">
                                @php
                                    $ptsScore = $finalGrades[$student->id]['pts_score'] ?? 0;
                                    $pClass = $ptsScore >= 90 ? 'grade-excellent' : ($ptsScore >= 75 ? 'grade-good' : ($ptsScore >= 60 ? 'grade-warning' : 'grade-danger'));
                                @endphp
                                <span class="{{ $ptsScore > 0 ? $pClass : 'text-muted' }}">{{ $ptsScore > 0 ? $ptsScore : '-' }}</span>
                            </td>

                            <td class="grade-cell">
                                @php
                                    $pasScore = $finalGrades[$student->id]['pas_score'] ?? 0;
                                    $psClass = $pasScore >= 90 ? 'grade-excellent' : ($pasScore >= 75 ? 'grade-good' : ($pasScore >= 60 ? 'grade-warning' : 'grade-danger'));
                                @endphp
                                <span class="{{ $pasScore > 0 ? $psClass : 'text-muted' }}">{{ $pasScore > 0 ? $pasScore : '-' }}</span>
                            </td>

                            @foreach($assignments as $a)
                                @php
                                    $sub = $a->submissions->where('student_id', $student->id)->first();
                                @endphp
                                <td class="grade-cell">
                                    @if($sub)
                                        @if(is_null($sub->grade))
                                            <span class="badge badge-warning" style="font-size:0.7rem;" title="Sudah kumpul, belum dinilai">Periksa</span>
                                        @else
                                            @php
                                                $g = $sub->grade;
                                                $gClass = $g >= 90 ? 'grade-excellent' : ($g >= 75 ? 'grade-good' : ($g >= 60 ? 'grade-warning' : 'grade-danger'));
                                            @endphp
                                            <span class="{{ $gClass }}">{{ $g }}</span>
                                        @endif
                                    @else
                                        <span class="badge badge-danger badge-dot" title="Belum Mengumpulkan"></span>
                                        <span class="text-xs text-muted ml-1">Kosong</span>
                                    @endif
                                </td>
                            @endforeach
                            <td style="background-color: var(--primary-50); font-size: 1.2rem;">
                                @php
                                    $finalScore = $finalGrades[$student->id]['score'] ?? 0;
                                    $fClass = $finalScore >= 90 ? 'grade-excellent' : ($finalScore >= 75 ? 'grade-good' : ($finalScore >= 60 ? 'grade-warning' : 'grade-danger'));
                                @endphp
                                <span class="{{ $fClass }}">{{ $finalScore }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($assignments) + 5 }}" class="text-center py-4 text-muted">Belum ada data siswa di kelas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    @if($students->count() > 0)
                    <tr class="avg-row">
                        <td style="text-align: right; padding-right:1rem;"><strong>RATA-RATA KELAS:</strong></td>
                        
                        @php
                            $attSum = collect($finalGrades)->sum('attendance_score');
                            $attAvg = $students->count() > 0 ? round($attSum / $students->count(), 1) : 0;
                        @endphp
                        <td class="{{ $attAvg >= 75 ? 'text-success' : 'text-danger' }}" style="background: var(--surface);">{{ $attAvg }}</td>

                        @php
                            $ptsSum = collect($finalGrades)->sum('pts_score');
                            $pasSum = collect($finalGrades)->sum('pas_score');
                            
                            $ptsCount = collect($finalGrades)->where('pts_score', '>', 0)->count();
                            $pasCount = collect($finalGrades)->where('pas_score', '>', 0)->count();
                            
                            $ptsAvg = $ptsCount > 0 ? round($ptsSum / $ptsCount, 1) : 0;
                            $pasAvg = $pasCount > 0 ? round($pasSum / $pasCount, 1) : 0;
                        @endphp
                        <td class="{{ $ptsAvg >= 75 ? 'text-success' : 'text-danger' }}">{{ $ptsAvg > 0 ? $ptsAvg : '-' }}</td>
                        <td class="{{ $pasAvg >= 75 ? 'text-success' : 'text-danger' }}">{{ $pasAvg > 0 ? $pasAvg : '-' }}</td>

                        @foreach($assignments as $a)
                            @php
                                $sum = 0; $count = 0;
                                foreach($students as $s) {
                                    $sub = $a->submissions->where('student_id', $s->id)->first();
                                    if($sub && !is_null($sub->grade)) {
                                        $sum += $sub->grade;
                                        $count++;
                                    }
                                }
                                $avg = $count > 0 ? round($sum / $count, 1) : 0;
                            @endphp
                            <td class="{{ $avg >= 75 ? 'text-success' : 'text-danger' }}">{{ $avg }}</td>
                        @endforeach
                        @php
                            $finalSum = collect($finalGrades)->sum('score');
                            $finalAvg = $students->count() > 0 ? round($finalSum / $students->count(), 1) : 0;
                        @endphp
                        <td style="background-color: var(--primary-100); color: var(--primary-800); font-size:1.1rem;">
                            {{ $finalAvg }}
                        </td>
                    </tr>
                    @endif
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
