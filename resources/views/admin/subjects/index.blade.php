@extends('layouts.app')
@section('title', 'Mata Pelajaran')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a><span class="separator">/</span>
    <span class="current">Mata Pelajaran</span>
@endsection
@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-md">
    <div><h1>Mata Pelajaran</h1><p>Kelola seluruh data mata pelajaran dan penugasan guru.</p></div>
    <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary"><i class="ri-add-line"></i> Tambah Mapel</a>
</div>
<div class="card">
    <div class="card-header">
        <form action="{{ route('admin.subjects.index') }}" method="GET" class="filter-bar m-0">
            <input type="text" name="search" class="form-control" placeholder="Cari mapel..." value="{{ request('search') }}" style="min-width:200px;">
            <select name="level" class="form-control form-select" onchange="this.form.submit()" style="min-width:180px;">
                <option value="">Semua Jenjang</option>
                <option value="X" {{ request('level') == 'X' ? 'selected' : '' }}>Kelas X</option>
                <option value="XI" {{ request('level') == 'XI' ? 'selected' : '' }}>Kelas XI</option>
                <option value="XII" {{ request('level') == 'XII' ? 'selected' : '' }}>Kelas XII</option>
            </select>
            @if(request('search') || request('level'))
                <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline">Reset</a>
            @endif
        </form>
    </div>
    </div>
</div>

<div class="dashboard-grid">
    @foreach(['X', 'XI', 'XII'] as $level)
        <div class="card">
            <div class="card-header" style="background: var(--primary-50); border-bottom: 2px solid var(--primary-500);">
                <h3 style="color: var(--primary-700);"><i class="ri-book-3-line"></i> Jenjang Kelas {{ $level }}</h3>
            </div>
            <div class="card-body p-0">
                @php
                    $levelSubjects = $groupedSubjects[$level] ?? collect();
                @endphp
                
                @if($levelSubjects->count() > 0)
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Mata Pelajaran</th>
                                <th>Kode</th>
                                <th>Jurusan</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($levelSubjects as $subject)
                                <tr>
                                    <td><strong>{{ $subject->name }}</strong></td>
                                    <td><code>{{ $subject->code }}</code></td>
                                    <td>
                                        @if($subject->major)
                                            <span class="badge badge-info">{{ $subject->major->code }}</span>
                                        @else
                                            <span class="badge badge-secondary">Umum</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($subject->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group justify-center">
                                            <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="btn btn-sm btn-secondary" data-tooltip="Edit"><i class="ri-pencil-line"></i></a>
                                            <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST" style="display:inline-block;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-secondary text-danger" data-confirm="Hapus mapel ini?" data-tooltip="Hapus"><i class="ri-delete-bin-line"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-4 text-center text-muted">
                        <em>Belum ada mata pelajaran untuk Kelas {{ $level }}.</em>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection
