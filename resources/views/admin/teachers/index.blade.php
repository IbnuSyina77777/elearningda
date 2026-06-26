@extends('layouts.app')
@section('title', 'Data Guru')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <span class="current">Data Guru</span>
@endsection

@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-md">
    <div>
        <h1>Data Guru</h1>
        <p>Kelola seluruh data pengajar di sekolah.</p>
    </div>
    <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">
        <i class="ri-add-line"></i> Tambah Guru
    </a>
</div>

<div class="card">
    <div class="card-header">
        <form action="{{ route('admin.teachers.index') }}" method="GET" class="filter-bar m-0">
            <input type="text" name="search" class="form-control" placeholder="Cari nama atau NIP..." value="{{ request('search') }}" style="min-width:250px;">
            <button type="submit" class="btn btn-secondary"><i class="ri-search-line"></i></button>
            @if(request('search'))
                <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline">Reset</a>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Guru</th>
                        <th>NIP</th>
                        <th>Jabatan & Spesialisasi</th>
                        <th>No. HP</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $teacher)
                        <tr>
                            <td>
                                <div class="d-flex align-center gap-sm">
                                    @if($teacher->photo)
                                        <img src="{{ asset('storage/' . $teacher->photo) }}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid var(--border-color);">
                                    @else
                                        <div style="width:40px;height:40px;border-radius:50%;background:var(--primary-100);color:var(--primary-700);display:flex;align-items:center;justify-content:center;font-weight:700;">{{ strtoupper(substr($teacher->name, 0, 1)) }}</div>
                                    @endif
                                    <div>
                                        <strong>{{ $teacher->name }}</strong>
                                        <div class="text-sm text-muted">{{ $teacher->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><code>{{ $teacher->nip }}</code></td>
                            <td>
                                @if($teacher->position)
                                    <span class="badge badge-primary">
                                        {{ $teacher->position }}
                                        @if($teacher->position == 'Wali Kelas' && $teacher->homeroomClass)
                                            - {{ $teacher->homeroomClass->name }}
                                        @endif
                                    </span>
                                @endif
                                <div class="text-sm mt-1">{{ $teacher->specialization ?? '-' }}</div>
                                @if(in_array($teacher->position, ['Guru Mata Pelajaran', 'Wali Kelas']))
                                    @if($teacher->taughtClassrooms->count() > 0)
                                    <div class="text-xs text-muted mt-1">
                                        Mengajar Kelas: {{ $teacher->taughtClassrooms->pluck('name')->implode(', ') }}
                                    </div>
                                    @endif
                                    @if($teacher->taughtSubjects->count() > 0)
                                    <div class="text-xs text-muted mt-1">
                                        Mapel: {{ $teacher->taughtSubjects->pluck('name')->implode(', ') }}
                                    </div>
                                    @endif
                                @endif
                            </td>
                            <td>{{ $teacher->phone ?? '-' }}</td>
                            <td class="text-center">
                                <div class="btn-group justify-center">
                                    <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="btn btn-sm btn-secondary" data-tooltip="Edit"><i class="ri-pencil-line"></i></a>
                                    <form action="{{ route('admin.teachers.destroy', $teacher->id) }}" method="POST" style="display:inline-block;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-secondary text-danger" data-confirm="Hapus data guru ini?" data-tooltip="Hapus"><i class="ri-delete-bin-line"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data guru.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($teachers->hasPages())
        <div class="card-footer" style="background:#fff;">{{ $teachers->links('pagination::bootstrap-4') }}</div>
    @endif
</div>
@endsection
