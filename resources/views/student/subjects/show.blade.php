@extends('layouts.app')
@section('title', $subject->name)
@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}"><i class="ri-home-4-line"></i></a><span class="separator">/</span>
    <a href="{{ route('student.subjects.index') }}">Mata Pelajaran</a><span class="separator">/</span>
    <span class="current">{{ $subject->code }}</span>
@endsection
@section('content')
<div class="page-header">
    <h1>{{ $subject->name }}</h1>
    <p>Pengajar: <strong>{{ $subject->teacher_name ?? '-' }}</strong> · {{ auth()->user()->student->classroom->name ?? '-' }}</p>
</div>

<div class="dashboard-grid">
    {{-- Materi --}}
    <div>
        <div class="card">
            <div class="card-header"><h3><i class="ri-book-open-line" style="color:var(--primary-600);margin-right:6px;"></i> Materi Pelajaran</h3></div>
            <div class="card-body p-0">
                <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
                    <table class="table">
                        <tbody>
                            @forelse($subject->materials as $m)
                                <tr>
                                    <td style="width:40px;" class="text-center text-muted">{{ $m->order }}</td>
                                    <td>
                                        <strong>{{ $m->title }}</strong>
                                        @if($m->content)
                                            <div class="text-sm text-muted mt-1">{{ \Illuminate\Support\Str::limit(strip_tags($m->content), 100) }}</div>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if($m->file_path)
                                            <a href="{{ asset('storage/' . $m->file_path) }}" target="_blank" class="btn btn-sm btn-outline"><i class="ri-download-line"></i> Download</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center py-4 text-muted">Guru belum mengunggah materi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Tugas --}}
    <div>
        <div class="card">
            <div class="card-header"><h3><i class="ri-task-line" style="color:var(--primary-600);margin-right:6px;"></i> Daftar Tugas</h3></div>
            <div class="card-body p-0">
                <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
                    <table class="table">
                        <tbody>
                            @forelse($subject->assignments as $a)
                                <tr>
                                    <td>
                                        <strong>{{ $a->title }}</strong>
                                        <div class="text-sm text-muted">Deadline: {{ $a->due_date->format('d M Y, H:i') }}
                                            @if($a->is_overdue) <span class="badge badge-danger" style="font-size:.6rem;">Lewat</span> @endif
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('student.assignments.show', $a->id) }}" class="btn btn-sm btn-primary"><i class="ri-eye-line"></i> Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center py-4 text-muted">Belum ada tugas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
