@extends('layouts.app')

@section('title', 'Edit Kelas')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <a href="{{ route('admin.classrooms.index') }}">Data Kelas</a>
    <span class="separator">/</span>
    <span class="current">Edit Data</span>
@endsection

@section('content')
<div class="page-header">
    <h1>Edit Data Kelas</h1>
</div>

<div class="card" style="max-width: 600px;">
    <form action="{{ route('admin.classrooms.update', $classroom->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label class="form-label" for="major_id">Jurusan <span class="required">*</span></label>
                <select id="major_id" name="major_id" class="form-control form-select @error('major_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Jurusan --</option>
                    @foreach($majors as $major)
                        <option value="{{ $major->id }}" {{ old('major_id', $classroom->major_id) == $major->id ? 'selected' : '' }}>
                            {{ $major->name }} ({{ $major->code }})
                        </option>
                    @endforeach
                </select>
                @error('major_id')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="level">Tingkat <span class="required">*</span></label>
                <select id="level" name="level" class="form-control form-select @error('level') is-invalid @enderror" required>
                    <option value="X" {{ old('level', $classroom->level) == 'X' ? 'selected' : '' }}>X (Sepuluh)</option>
                    <option value="XI" {{ old('level', $classroom->level) == 'XI' ? 'selected' : '' }}>XI (Sebelas)</option>
                    <option value="XII" {{ old('level', $classroom->level) == 'XII' ? 'selected' : '' }}>XII (Dua Belas)</option>
                </select>
                @error('level')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="name">Nama Kelas <span class="required">*</span></label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $classroom->name) }}" required>
                @error('name')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group mb-0" style="display:flex;align-items:center;gap:8px;">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $classroom->is_active) ? 'checked' : '' }}>
                <label for="is_active" style="cursor:pointer;margin:0;">Kelas Aktif</label>
            </div>
        </div>
        
        <div class="card-footer d-flex justify-between">
            <a href="{{ route('admin.classrooms.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="ri-save-3-line"></i> Update Data</button>
        </div>
    </form>
</div>
@endsection
