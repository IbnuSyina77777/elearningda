@extends('layouts.app')

@section('title', 'Pantau Administrasi Guru')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <span class="current">Administrasi Guru</span>
@endsection

@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-md">
    <div>
        <h1>Pantau Administrasi Guru</h1>
        <p>Lihat tingkat kelengkapan dokumen mengajar tiap guru (Kurikulum Merdeka).</p>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <form action="{{ route('admin.teacher-administrations.index') }}" method="GET" class="filter-bar m-0">
            <div class="search-input" style="width: 300px;">
                <i class="ri-search-line search-icon"></i>
                <input type="text" name="search" class="form-control" placeholder="Cari Nama Guru / Mata Pelajaran..." value="{{ request('search') }}">
            </div>
            @if(request('search'))
                <a href="{{ route('admin.teacher-administrations.index') }}" class="btn btn-outline">Reset</a>
            @endif
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Guru</th>
                        <th>Mata Pelajaran</th>
                        <th>Status Kelengkapan</th>
                        <th>Kekurangan Dokumen Wajib</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekap as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->teacher_name }}</strong>
                            </td>
                            <td>{{ $item->subject_name }}</td>
                            <td style="min-width: 150px;">
                                <div class="d-flex align-center justify-between mb-1">
                                    <span class="text-sm" style="font-weight: 600;">{{ $item->percentage }}%</span>
                                    @if($item->percentage === 100)
                                        <span class="badge badge-success badge-dot">Lengkap</span>
                                    @elseif($item->percentage > 0)
                                        <span class="badge badge-warning badge-dot">Sebagian</span>
                                    @else
                                        <span class="badge badge-danger badge-dot">Kosong</span>
                                    @endif
                                </div>
                                <div style="width: 100%; height: 6px; background-color: #eee; border-radius: 4px; overflow: hidden;">
                                    <div style="width: {{ $item->percentage }}%; height: 100%; background-color: {{ $item->percentage === 100 ? '#10b981' : ($item->percentage > 0 ? '#f59e0b' : '#ef4444') }}; border-radius: 4px;"></div>
                                </div>
                            </td>
                            <td>
                                @if(count($item->missing) === 0)
                                    <span class="text-success text-sm"><i class="ri-check-double-line"></i> Semua dokumen wajib telah diunggah</span>
                                @else
                                    <span class="text-danger text-sm">
                                        {{ implode(', ', $item->missing) }}
                                    </span>
                                @endif
                                <div class="text-xs text-muted mt-1">Total Unggahan: {{ $item->total_docs }} file</div>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('admin.teacher-administrations.show', [$item->teacher_id, $item->subject_id]) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="ri-eye-line"></i> Lihat File
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada data guru / mata pelajaran yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
