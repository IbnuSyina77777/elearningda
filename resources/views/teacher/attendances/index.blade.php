@extends('layouts.app')
@section('title', 'Absensi - ' . $subject->name)
@section('breadcrumb')
    <a href="{{ route('teacher.dashboard') }}"><i class="ri-home-4-line"></i></a><span class="separator">/</span>
    <span class="current">Absensi · {{ $subject->code }}</span>
@endsection

@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-md">
    <div>
        <h1>Absensi: {{ $subject->name }}</h1>
        <p>Input kehadiran siswa untuk tanggal tertentu.</p>
    </div>
</div>

<div class="d-flex gap-md flex-wrap" style="align-items: flex-start;">
    <div class="card" style="flex: 1; min-width: 300px;">
        <div class="card-header">
            <h3>Pilih Kelas & Tanggal</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('teacher.attendances.index', $subject->id) }}" method="GET" class="d-flex flex-column gap-sm">
                <div>
                    <label class="form-label">Kelas</label>
                    <select name="classroom_id" class="form-control form-select" onchange="this.form.submit()">
                        @foreach($classrooms as $c)
                            <option value="{{ $c->id }}" {{ $selectedClassroom->id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Tanggal Absensi</label>
                    <input type="date" name="date" class="form-control" value="{{ $date }}" max="{{ now()->format('Y-m-d') }}" onchange="this.form.submit()">
                </div>
            </form>
            
            <hr style="margin: 1.5rem 0; border:0; border-top: 1px solid var(--border-color);">
            
            <h4 style="margin-top:0; margin-bottom:1rem;">Riwayat Tanggal Absensi ({{ $selectedClassroom->name }})</h4>
            <div style="max-height: 250px; overflow-y: auto;">
                @forelse($pastDates as $pd)
                    <a href="{{ route('teacher.attendances.index', ['subject' => $subject->id, 'classroom_id' => $selectedClassroom->id, 'date' => $pd]) }}" class="d-block py-2 px-3 mb-2 rounded {{ $date == $pd ? 'bg-primary-50 text-primary-700 font-bold' : 'bg-surface hover-bg-gray' }}" style="text-decoration: none; border: 1px solid var(--border-color);">
                        <i class="ri-calendar-event-line mr-1"></i> {{ \Carbon\Carbon::parse($pd)->format('d M Y') }}
                    </a>
                @empty
                    <div class="text-muted text-sm">Belum ada riwayat absensi.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="card" style="flex: 3; min-width: 500px;">
        <div class="card-header d-flex justify-between align-center">
            <h3 style="margin:0;">Input Absensi: {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h3>
            <span class="badge badge-primary">{{ $selectedClassroom->name }}</span>
        </div>
        <div class="card-body p-0">
            <form action="{{ route('teacher.attendances.store', $subject->id) }}" method="POST">
                @csrf
                <input type="hidden" name="classroom_id" value="{{ $selectedClassroom->id }}">
                <input type="hidden" name="date" value="{{ $date }}">
                
                <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama Siswa</th>
                                <th class="text-center" style="width: 100px;">Hadir (H)</th>
                                <th class="text-center" style="width: 100px;">Sakit (S)</th>
                                <th class="text-center" style="width: 100px;">Izin (I)</th>
                                <th class="text-center" style="width: 100px;">Alpa (A)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                @php
                                    $status = isset($attendances[$student->id]) ? $attendances[$student->id]->status : 'hadir';
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $student->name }}</strong>
                                        <div class="text-xs text-muted">{{ $student->nisn }}</div>
                                    </td>
                                    <td class="text-center">
                                        <input type="radio" name="status[{{ $student->id }}]" value="hadir" {{ $status == 'hadir' ? 'checked' : '' }} style="transform: scale(1.2); cursor:pointer;">
                                    </td>
                                    <td class="text-center">
                                        <input type="radio" name="status[{{ $student->id }}]" value="sakit" {{ $status == 'sakit' ? 'checked' : '' }} style="transform: scale(1.2); cursor:pointer;">
                                    </td>
                                    <td class="text-center">
                                        <input type="radio" name="status[{{ $student->id }}]" value="izin" {{ $status == 'izin' ? 'checked' : '' }} style="transform: scale(1.2); cursor:pointer;">
                                    </td>
                                    <td class="text-center">
                                        <input type="radio" name="status[{{ $student->id }}]" value="alpa" {{ $status == 'alpa' ? 'checked' : '' }} style="transform: scale(1.2); cursor:pointer;">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada siswa di kelas ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($students->count() > 0)
                    <div class="p-3" style="border-top: 1px solid var(--border-color); text-align: right; background: var(--surface);">
                        <button type="submit" class="btn btn-primary"><i class="ri-save-3-line"></i> Simpan Absensi</button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
