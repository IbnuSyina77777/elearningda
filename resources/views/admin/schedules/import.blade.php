@extends('layouts.app')

@section('title', 'Import Jadwal Pelajaran')

@section('content')
<div class="page-header d-flex justify-between align-center">
    <div>
        <h1>Import Jadwal Pelajaran</h1>
        <p>Tahun Ajaran Aktif: <strong>{{ $academicYear->name ?? 'Belum Diatur' }}</strong></p>
    </div>
    <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary">
        <i class="ri-arrow-left-line"></i> Kembali
    </a>
</div>

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

<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <p class="mb-4">Untuk melakukan import massal jadwal pelajaran, silakan unduh template Excel yang telah disediakan, isi sesuai format, lalu unggah kembali di bawah ini.</p>
        
        <div class="mb-4">
            <a href="{{ route('admin.schedules.template') }}" class="btn btn-outline text-primary" style="border-color: var(--primary-500);">
                <i class="ri-download-2-line"></i> Download Template Excel
            </a>
        </div>

        <form action="{{ route('admin.schedules.import.process') }}" method="POST" enctype="multipart/form-data" style="border-top: 1px solid var(--gray-200); padding-top: 1.5rem;">
            @csrf
            <div class="form-group mb-4">
                <label class="form-label" style="font-weight: bold;">Unggah File Excel (.xlsx, .xls, .csv)</label>
                <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required style="padding: 0.5rem;">
            </div>
            
            <button type="submit" class="btn btn-primary w-100">
                <i class="ri-upload-2-line"></i> Import Data Sekarang
            </button>
        </form>
    </div>
</div>
@endsection
