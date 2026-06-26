@extends('layouts.app')
@section('title', 'Tugas - ' . $subject->name)
@section('breadcrumb')
    <a href="{{ route('teacher.dashboard') }}"><i class="ri-home-4-line"></i></a><span class="separator">/</span>
    <span class="current">Tugas · {{ $subject->code }}</span>
@endsection
@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-md">
    <div><h1>Tugas: {{ $subject->name }}</h1><p>Kelas {{ $subject->level }}</p></div>
    <div class="d-flex gap-sm align-center">
        <form action="{{ route('teacher.assignments.index', $subject->id) }}" method="GET">
            <select name="academic_year_id" class="form-control form-select" onchange="this.form.submit()">
                @foreach($academicYears as $y)
                    <option value="{{ $y->id }}" {{ $selectedYearId == $y->id ? 'selected' : '' }}>Semester: {{ $y->full_label }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('teacher.assignments.create', $subject->id) }}" class="btn btn-primary" style="white-space:nowrap;"><i class="ri-add-line"></i> Buat Tugas</a>
    </div>
</div>
<div class="card">
    <div class="card-body p-0">
        <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
            <table class="table">
                <thead><tr><th>Judul Tugas</th><th>Kelas</th><th>Deadline</th><th>Pengumpulan</th><th class="text-center">Aksi</th></tr></thead>
                <tbody>
                    @forelse($assignments as $a)
                        <tr>
                            <td>
                                <strong>{{ $a->title }}</strong>
                                @if($a->file_path)
                                    <div class="text-sm"><a href="{{ asset('storage/' . $a->file_path) }}" target="_blank"><i class="ri-attachment-line"></i> {{ $a->file_name }}</a></div>
                                @endif
                            </td>
                            <td><span class="badge badge-primary">{{ $a->classroom->name ?? '-' }}</span></td>
                            <td>
                                {{ $a->due_date->format('d M Y, H:i') }}
                                @if($a->is_overdue)
                                    <span class="badge badge-danger badge-dot ml-1" title="Melewati Deadline"></span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('teacher.submissions.index', [$subject->id, $a->id]) }}" class="btn btn-sm btn-outline">
                                    <i class="ri-team-line"></i> {{ $a->submissions_count }} Siswa
                                </a>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('teacher.assignments.destroy', [$subject->id, $a->id]) }}" method="POST" style="display:inline-block;">@csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-secondary text-danger" data-confirm="Hapus tugas ini beserta seluruh pengumpulannya?"><i class="ri-delete-bin-line"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada tugas untuk mapel ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
