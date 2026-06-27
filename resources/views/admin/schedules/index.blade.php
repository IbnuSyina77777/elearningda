@extends('layouts.app')

@section('title', 'Data Jadwal Pelajaran')

@section('content')
<div class="page-header d-flex justify-between align-center">
    <div>
        <h1>Jadwal Pelajaran</h1>
        <p>Kelola jadwal pelajaran untuk Tahun Ajaran: <strong>{{ $academicYear->name ?? 'Belum Diatur' }}</strong></p>
    </div>
    <div class="d-flex gap-sm">
        <a href="{{ route('admin.schedules.export') }}" class="btn btn-outline text-success" style="border-color: var(--success-500);">
            <i class="ri-file-excel-2-line"></i> Export Excel
        </a>
        <a href="{{ route('admin.schedules.import.form') }}" class="btn btn-outline text-primary" style="border-color: var(--primary-500);">
            <i class="ri-file-upload-line"></i> Import Excel
        </a>
        <a href="{{ route('admin.schedules.create') }}" class="btn btn-primary">
            <i class="ri-add-line"></i> Tambah
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success mb-4" style="background: rgba(16, 185, 129, 0.1); color: var(--success-600); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: var(--radius-md); padding: 1rem;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger mb-4" style="background: rgba(239, 68, 68, 0.1); color: var(--danger-600); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: var(--radius-md); padding: 1rem;">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger mb-4" style="background: rgba(239, 68, 68, 0.1); color: var(--danger-600); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: var(--radius-md); padding: 1rem;">
        <ul style="margin: 0; padding-left: 1.5rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title"><i class="ri-filter-3-line"></i> Filter Kelas</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.schedules.index') }}" method="GET" class="d-flex gap-sm align-center">
            <select name="classroom_id" class="form-control" style="max-width: 300px;">
                <option value="">-- Tampilkan Semua Kelas --</option>
                @foreach($classrooms as $c)
                    <option value="{{ $c->id }}" {{ request('classroom_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
            @if(request('classroom_id'))
                <a href="{{ route('admin.schedules.index') }}" class="btn btn-outline text-danger" style="border:none;">Reset</a>
            @endif
        </form>
    </div>
</div>

<div class="dashboard-grid">
    @foreach($groupedSchedules as $day => $schedules)
        <div class="card">
            <div class="card-header" style="background: var(--primary-50); border-bottom: 2px solid var(--primary-500);">
                <h3 style="color: var(--primary-700);"><i class="ri-calendar-event-line"></i> {{ $day }}</h3>
            </div>
            <div class="card-body p-0">
                @if($schedules->count() > 0)
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Jam</th>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th>Guru</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedules as $schedule)
                                <tr>
                                    <td style="white-space: nowrap;">
                                        <strong>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</strong> - 
                                        <strong>{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</strong>
                                    </td>
                                    <td><span class="badge badge-info">{{ $schedule->classroom->name ?? '-' }}</span></td>
                                    <td><strong>{{ $schedule->subject->name }}</strong></td>
                                    <td>{{ $schedule->teacher->user->name }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.schedules.edit', $schedule->id) }}" class="btn btn-sm btn-outline text-primary">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            <form action="{{ route('admin.schedules.destroy', $schedule->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline text-danger">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-4 text-center text-muted">
                        <em>Tidak ada jadwal di hari ini.</em>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection
