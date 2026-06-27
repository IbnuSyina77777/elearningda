@extends('layouts.app')

@section('title', 'Jadwal Pelajaran')

@section('content')
<div class="page-header d-flex justify-between align-center">
    <div>
        <h1>Jadwal Pelajaran</h1>
        <p>Kelas: <strong>{{ $student->classroom->name ?? 'Alumni / Lulus' }}</strong> | Tahun Ajaran: <strong>{{ $academicYear->name ?? 'Belum Diatur' }}</strong></p>
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
                                <th>Mata Pelajaran</th>
                                <th>Guru</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedules as $schedule)
                                <tr>
                                    <td style="white-space: nowrap;">
                                        <strong>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</strong> - 
                                        <strong>{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</strong>
                                    </td>
                                    <td><strong>{{ $schedule->subject->name }}</strong></td>
                                    <td>{{ $schedule->teacher->user->name }}</td>
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
