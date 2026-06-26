@extends('layouts.app')
@section('title', 'Materi - ' . $subject->name)
@section('breadcrumb')
    <a href="{{ route('teacher.dashboard') }}"><i class="ri-home-4-line"></i></a><span class="separator">/</span>
    <span class="current">Materi · {{ $subject->code }}</span>
@endsection
@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-md">
    <div><h1>Materi: {{ $subject->name }}</h1><p>Kelas {{ $subject->level }}</p></div>
    <div class="d-flex gap-sm align-center">
        <form action="{{ route('teacher.materials.index', $subject->id) }}" method="GET">
            <select name="academic_year_id" class="form-control form-select" onchange="this.form.submit()">
                @foreach($academicYears as $y)
                    <option value="{{ $y->id }}" {{ $selectedYearId == $y->id ? 'selected' : '' }}>Semester: {{ $y->full_label }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('teacher.materials.create', $subject->id) }}" class="btn btn-primary" style="white-space:nowrap;"><i class="ri-add-line"></i> Tambah Materi</a>
    </div>
</div>
<div class="card">
    <div class="card-body p-0">
        <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
            <table class="table">
                <thead><tr><th>#</th><th>Judul Materi</th><th>Kelas</th><th>File Lampiran</th><th>Dibuat</th><th class="text-center">Aksi</th></tr></thead>
                <tbody>
                    @forelse($materials as $m)
                        <tr>
                            <td>{{ $m->order }}</td>
                            <td><strong>{{ $m->title }}</strong></td>
                            <td><span class="badge badge-primary">{{ $m->classroom->name ?? '-' }}</span></td>
                            <td>
                                @if($m->file_path)
                                    <a href="{{ asset('storage/' . $m->file_path) }}" target="_blank" class="btn btn-sm btn-outline"><i class="ri-download-line"></i> {{ $m->file_name }}</a>
                                @else
                                    <span class="text-muted">Teks saja</span>
                                @endif
                            </td>
                            <td class="text-sm text-muted">{{ $m->created_at->format('d M Y') }}</td>
                            <td class="text-center">
                                <div class="btn-group justify-center">
                                    <a href="{{ route('teacher.materials.edit', [$subject->id, $m->id]) }}" class="btn btn-sm btn-secondary"><i class="ri-pencil-line"></i></a>
                                    <form action="{{ route('teacher.materials.destroy', [$subject->id, $m->id]) }}" method="POST" style="display:inline-block;">@csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-secondary text-danger" data-confirm="Hapus materi ini?"><i class="ri-delete-bin-line"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada materi untuk mapel ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
