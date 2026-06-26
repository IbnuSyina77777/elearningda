@extends('layouts.app')

@section('title', 'Edit Tahun Ajaran')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <a href="{{ route('admin.academic-years.index') }}">Tahun Ajaran</a>
    <span class="separator">/</span>
    <span class="current">Edit Data</span>
@endsection

@section('content')
<div class="page-header">
    <h1>Edit Tahun Ajaran</h1>
</div>

<div class="card" style="max-width: 500px;">
    <form action="{{ route('admin.academic-years.update', $academicYear->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label class="form-label" for="name">Nama Tahun Ajaran <span class="required">*</span></label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $academicYear->name) }}" required>
                @error('name')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            
            <div class="form-group">
                <label class="form-label" for="semester">Semester <span class="required">*</span></label>
                <select id="semester" name="semester" class="form-control @error('semester') is-invalid @enderror" required>
                    <option value="Ganjil" {{ old('semester', $academicYear->semester) == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="Genap" {{ old('semester', $academicYear->semester) == 'Genap' ? 'selected' : '' }}>Genap</option>
                </select>
                @error('semester')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="start_date">Tanggal Mulai <span class="required">*</span></label>
                <input type="date" id="start_date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $academicYear->start_date?->format('Y-m-d')) }}" required>
                @error('start_date')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="end_date">Tanggal Selesai <span class="required">*</span></label>
                <input type="date" id="end_date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $academicYear->end_date?->format('Y-m-d')) }}" required>
                @error('end_date')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group mb-0" style="display:flex;align-items:center;gap:8px;">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $academicYear->is_active) ? 'checked' : '' }}>
                <label for="is_active" style="cursor:pointer;margin:0;">Jadikan Tahun Ajaran Aktif</label>
            </div>
            <span class="form-hint d-block mt-2">Perhatian: Jika ditandai aktif, tahun ajaran lain akan otomatis dinonaktifkan.</span>
        </div>
        
        <div class="card-footer d-flex justify-between">
            <a href="{{ route('admin.academic-years.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="ri-save-3-line"></i> Update Data</button>
        </div>
    </form>
</div>
@endsection
