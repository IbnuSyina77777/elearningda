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
    </div>
</div>

<div class="dashboard-grid">
    @foreach(['X' => 'Kelas X', 'XI' => 'Kelas XI', 'XII' => 'Kelas XII', 'Lainnya' => 'Staff / Lainnya'] as $levelKey => $levelName)
        <div class="card">
            <div class="card-header" style="background: var(--primary-50); border-bottom: 2px solid var(--primary-500);">
                <h3 style="color: var(--primary-700);">
                    <i class="ri-user-star-line"></i> Guru {{ $levelName }}
                </h3>
            </div>
            <div class="card-body p-0">
                @php
                    $levelTeachers = $groupedTeachers[$levelKey] ?? collect();
                @endphp
                
                @if($levelTeachers->count() > 0)
                    <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Guru</th>
                                    <th>NIP</th>
                                    <th>Jabatan & Info</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($levelTeachers as $teacher)
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
                                            @if(!empty($teacher->position) && is_array($teacher->position))
                                                <div class="d-flex flex-wrap gap-xs">
                                                @foreach($teacher->position as $pos)
                                                    <span class="badge badge-info">
                                                        {{ $pos }}
                                                        @if($pos == 'Wali Kelas' && $teacher->homeroomClass)
                                                            - {{ $teacher->homeroomClass->name }}
                                                        @elseif(in_array($pos, ['Kepala Program Keahlian (Kajur)', 'Kepala Bengkel / Laboratorium']) && $teacher->major)
                                                            - {{ $teacher->major->name }}
                                                        @endif
                                                    </span>
                                                @endforeach
                                                </div>
                                            @endif
                                            <div class="text-sm mt-1">{{ $teacher->specialization ?? '-' }}</div>
                                            @php
                                                $hasTeaching = false;
                                                if (!empty($teacher->position) && is_array($teacher->position)) {
                                                    $nonTeaching = ['Kepala Sekolah', 'Wakasek Kurikulum', 'Wakasek Kesiswaan', 'Wakasek Hubin / Humas', 'Wakasek Sarana Prasarana', 'Staf Tata Usaha (TU)', 'Pustakawan', 'Kepala Bengkel / Laboratorium', 'Kepala Program Keahlian (Kajur)'];
                                                    $hasTeaching = count(array_diff($teacher->position, $nonTeaching)) > 0;
                                                }
                                            @endphp
                                            @if($hasTeaching)
                                                @if($teacher->taughtClassrooms->count() > 0)
                                                <div class="text-xs text-muted mt-1">
                                                    Kelas: {{ $teacher->taughtClassrooms->pluck('name')->implode(', ') }}
                                                </div>
                                                @endif
                                                @if($teacher->taughtSubjects->count() > 0)
                                                <div class="text-xs text-muted mt-1">
                                                    Mapel: {{ $teacher->taughtSubjects->pluck('name')->implode(', ') }}
                                                </div>
                                                @endif
                                            @endif
                                        </td>
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-4 text-center text-muted">
                        <em>Belum ada guru untuk {{ $levelName }}.</em>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection
