@extends('layouts.app')
@section('title', 'Edit Mata Pelajaran')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a><span class="separator">/</span>
    <a href="{{ route('admin.subjects.index') }}">Mata Pelajaran</a><span class="separator">/</span>
    <span class="current">Edit</span>
@endsection
@section('content')
<div class="page-header"><h1>Edit Mata Pelajaran</h1><p>Ubah data untuk <strong>{{ $subject->name }}</strong>.</p></div>
<div class="card" style="max-width: 700px;">
    <form action="{{ route('admin.subjects.update', $subject->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Nama Mata Pelajaran <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $subject->name) }}" required>
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="code">Kode Mapel <span class="required">*</span></label>
                    <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $subject->code) }}" required>
                    @error('code')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="level">Jenjang / Tingkat <span class="required">*</span></label>
                <select id="level" name="level" class="form-control form-select @error('level') is-invalid @enderror" required>
                    <option value="">-- Pilih Jenjang --</option>
                    <option value="X" {{ old('level', $subject->level) == 'X' ? 'selected' : '' }}>Kelas X</option>
                    <option value="XI" {{ old('level', $subject->level) == 'XI' ? 'selected' : '' }}>Kelas XI</option>
                    <option value="XII" {{ old('level', $subject->level) == 'XII' ? 'selected' : '' }}>Kelas XII</option>
                </select>
                @error('level')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="description">Deskripsi</label>
                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $subject->description) }}</textarea>
                @error('description')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group mb-0">
                <label class="d-flex align-center gap-sm" style="cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $subject->is_active) ? 'checked' : '' }}>
                    <span>Mata pelajaran ini aktif</span>
                </label>
            </div>
        </div>
        <div class="card-footer d-flex justify-between">
            <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="ri-save-3-line"></i> Update Mata Pelajaran</button>
        </div>
    </form>
</div>
@endsection
