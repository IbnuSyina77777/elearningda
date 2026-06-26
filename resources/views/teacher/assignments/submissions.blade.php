@extends('layouts.app')
@section('title', 'Pengumpulan Tugas')
@section('breadcrumb')
    <a href="{{ route('teacher.dashboard') }}"><i class="ri-home-4-line"></i></a><span class="separator">/</span>
    <a href="{{ route('teacher.assignments.index', $subject->id) }}">Tugas · {{ $subject->code }}</a><span class="separator">/</span>
    <span class="current">Pengumpulan</span>
@endsection
@section('content')
<div class="page-header">
    <h1>Pengumpulan: {{ $assignment->title }}</h1>
    <p>{{ $subject->name }} · Deadline: {{ $assignment->due_date->format('d M Y, H:i') }}</p>
</div>
<div class="card">
    <div class="card-body p-0">
        <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
            <table class="table">
                <thead><tr><th>Siswa</th><th>File Jawaban</th><th>Waktu Kirim</th><th>Catatan</th><th>Nilai</th><th class="text-center">Aksi</th></tr></thead>
                <tbody>
                    @forelse($submissions as $sub)
                        <tr>
                            <td><strong>{{ $sub->student->name ?? '-' }}</strong><div class="text-sm text-muted">{{ $sub->student->nis ?? '' }}</div></td>
                            <td>
                                @if($sub->file_path)
                                    <a href="{{ asset('storage/' . $sub->file_path) }}" target="_blank" class="btn btn-sm btn-outline"><i class="ri-download-line"></i> {{ $sub->file_name }}</a>
                                @else <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                {{ $sub->submitted_at ? $sub->submitted_at->format('d M Y, H:i') : '-' }}
                                @if($sub->is_late)
                                    <span class="badge badge-danger" style="font-size:.65rem;">Terlambat</span>
                                @endif
                            </td>
                            <td class="text-sm">{{ \Illuminate\Support\Str::limit($sub->notes, 50) ?: '-' }}</td>
                            <td>
                                @if($sub->grade !== null)
                                    <span class="badge {{ $sub->grade >= 75 ? 'badge-success' : ($sub->grade >= 50 ? 'badge-warning' : 'badge-danger') }}" style="font-size:.9rem;">{{ $sub->grade }}</span>
                                @else
                                    <span class="badge badge-secondary">Belum</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('teacher.submissions.grade', [$subject->id, $assignment->id, $sub->id]) }}" method="POST" class="d-flex gap-sm align-center" style="justify-content:center;">
                                    @csrf
                                    <input type="number" name="grade" min="0" max="100" class="form-control" style="width:70px;padding:4px 8px;font-size:.85rem;" value="{{ $sub->grade }}" placeholder="0-100" required>
                                    <input type="text" name="feedback" class="form-control" style="width:120px;padding:4px 8px;font-size:.85rem;" value="{{ $sub->feedback }}" placeholder="Feedback">
                                    <button type="submit" class="btn btn-sm btn-primary" data-tooltip="Simpan Nilai"><i class="ri-check-line"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada siswa yang mengumpulkan tugas ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
