@extends('layouts.app')

@section('title', 'Data Kelas')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <span class="current">Data Kelas</span>
@endsection

@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-md">
    <div>
        <h1>Data Kelas</h1>
        <p>Manajemen data kelas dan jurusan.</p>
    </div>
    <a href="{{ route('admin.classrooms.create') }}" class="btn btn-primary">
        <i class="ri-add-line"></i> Tambah Kelas
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
            <table class="table" id="classroomsTable">
                <thead>
                    <tr>
                        <th>Nama Kelas</th>
                        <th>Tingkat</th>
                        <th>Jurusan</th>
                        <th>Wali Kelas</th>
                        <th>Jumlah Siswa</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classrooms as $room)
                        <tr>
                            <td><strong>{{ $room->name }}</strong></td>
                            <td><span class="badge badge-secondary">{{ $room->level }}</span></td>
                            <td>{{ $room->major->name ?? '-' }} ({{ $room->major->code ?? '-' }})</td>
                            <td>
                                @if($room->homeroomTeacher)
                                    <div class="d-flex align-center gap-sm">
                                        <i class="ri-user-star-line text-primary"></i>
                                        <span>{{ $room->homeroomTeacher->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $room->students_count }} Siswa</td>
                            <td>
                                @if($room->is_active)
                                    <span class="badge badge-success badge-dot">Aktif</span>
                                @else
                                    <span class="badge badge-danger badge-dot">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group justify-center">
                                    <a href="{{ route('admin.classrooms.edit', $room->id) }}" class="btn btn-sm btn-secondary" data-tooltip="Edit">
                                        <i class="ri-edit-line"></i>
                                    </a>
                                    <form action="{{ route('admin.classrooms.destroy', $room->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-secondary text-danger" data-confirm="Hapus data kelas ini?" data-tooltip="Hapus">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data kelas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
