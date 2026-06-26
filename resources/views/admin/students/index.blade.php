@extends('layouts.app')

@section('title', 'Data Siswa')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <span class="current">Data Siswa</span>
@endsection

@section('content')
@if(session('import_errors'))
<div class="alert alert-warning animate-slide-down mb-3" style="max-width: 100%;">
    <span class="alert-icon"><i class="ri-alert-line"></i></span>
    <div class="alert-content">
        <strong>Detail Baris yang Gagal / Dilewati:</strong>
        <ul style="margin: 5px 0 0 20px; padding: 0;">
            @foreach(session('import_errors') as $err)
                <li>{{ $err }}</li>
            @endforeach
            @if(session('import_errors') && count(session('import_errors')) == 15)
                <li><em>... (dan mungkin ada lainnya)</em></li>
            @endif
        </ul>
        <p class="mt-2 text-sm text-muted m-0">Catatan: Baris yang benar telah berhasil diimpor.</p>
    </div>
    <button class="alert-close" onclick="this.parentElement.remove()"><i class="ri-close-line"></i></button>
</div>
@endif

<div class="page-header d-flex justify-between align-center flex-wrap gap-md">
    <div>
        <h1>Data Siswa</h1>
        <p>Kelola data siswa dan akun login untuk portal pembayaran.</p>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('admin.students.export', request()->all()) }}" class="btn btn-outline">
            <i class="ri-download-2-line"></i> Ekspor Excel
        </a>
        <button type="button" class="btn btn-outline" onclick="openModal('importModal')">
            <i class="ri-upload-2-line"></i> Impor Data
        </button>
        <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
            <i class="ri-add-line"></i> Tambah Siswa Baru
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form action="{{ route('admin.students.index') }}" method="GET" class="filter-bar m-0">
            <div class="search-input">
                <i class="ri-search-line search-icon"></i>
                <input type="text" name="search" class="form-control" placeholder="Cari nama, NISN, atau NIS..." value="{{ request('search') }}">
            </div>
            
            <div style="min-width: 200px;">
                <select name="classroom_id" class="form-control form-select" onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    @foreach($classrooms as $room)
                        <option value="{{ $room->id }}" {{ request('classroom_id') == $room->id ? 'selected' : '' }}>
                            {{ $room->name }} — {{ $room->major->code }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            @if(request('search') || request('classroom_id'))
                <a href="{{ route('admin.students.index') }}" class="btn btn-outline">Reset</a>
            @endif
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>NISN / NIS</th>
                        <th>Kelas & Jurusan</th>
                        <th>Kontak</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
                                    <div class="user-info">
                                        <strong>{{ $student->name }}</strong>
                                        <span>{{ $student->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight:600;">{{ $student->nisn }}</div>
                                <span class="text-muted text-sm">{{ $student->nis }}</span>
                            </td>
                            <td>
                                <span class="badge badge-primary">{{ $student->classroom->name ?? '-' }}</span>
                                <div class="text-muted text-sm mt-1">{{ $student->classroom->major->name ?? '-' }}</div>
                            </td>
                            <td>
                                <div>{{ $student->phone ?? '-' }}</div>
                                <span class="text-muted text-sm">{{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group justify-center">
                                    <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-sm btn-secondary" data-tooltip="Edit">
                                        <i class="ri-edit-line"></i>
                                    </a>
                                    <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-secondary text-danger" data-confirm="Hapus data siswa ini?" data-tooltip="Hapus">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="ri-user-search-line empty-state-icon"></i>
                                    <h3>Data tidak ditemukan</h3>
                                    <p>Tidak ada siswa yang sesuai dengan filter/pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($students->hasPages())
        <div class="card-footer" style="background:#fff;">
            {{ $students->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>

{{-- Modal Import Excel --}}
<div id="importModal" class="modal-overlay">
    <div class="card modal" style="width: 100%; max-width: 500px; margin: auto;">
        <div class="card-header d-flex justify-between align-center">
            <h3 class="m-0">Impor Data Siswa</h3>
            <button type="button" class="btn btn-sm btn-outline" style="border:none;" onclick="closeModal('importModal')"><i class="ri-close-line"></i></button>
        </div>
        <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data" onsubmit="document.getElementById('submitBtn').disabled = true; document.getElementById('submitBtn').innerHTML = '<i class=\'ri-loader-4-line ri-spin\'></i> Memproses...';">
            @csrf
            <div class="card-body">
                <div class="alert alert-info">
                    <span class="alert-icon"><i class="ri-information-line"></i></span>
                    <div class="alert-content">
                        <strong>Perhatian:</strong>
                        <ul style="margin: 5px 0 0 20px; padding: 0;">
                            <li>Gunakan berkas berformat Excel (<code>.xlsx</code> atau <code>.xls</code>).</li>
                            <li>Pastikan nama kelas (misal: <code>X-TKJ-1</code>) persis sama dengan data di sistem.</li>
                            <li>Password default akun adalah <strong>NISN</strong> siswa.</li>
                        </ul>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <label class="form-label" for="file">Berkas Excel <span class="required">*</span></label>
                    <input type="file" id="file" name="file" class="form-control" accept=".xlsx, .xls, .csv" required>
                </div>
            </div>
            <div class="card-footer d-flex justify-between">
                <button type="button" class="btn btn-secondary" onclick="closeModal('importModal')">Batal</button>
                <button type="submit" id="submitBtn" class="btn btn-primary"><i class="ri-upload-2-line"></i> Proses Impor</button>
            </div>
        </form>
    </div>
</div>

{{-- Add custom style for Laravel Pagination to match our design system --}}
@push('styles')
<style>
    .pagination { margin: 0; }
    .page-item.active .page-link { background-color: var(--primary-600); border-color: var(--primary-600); }
    .page-link { color: var(--text-secondary); border-color: var(--border-color); }
    .page-link:hover { color: var(--primary-600); background-color: var(--primary-50); }
</style>
@endpush
@endsection
