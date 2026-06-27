@extends('layouts.app')
@section('title', 'Tambah Mata Pelajaran')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a><span class="separator">/</span>
    <a href="{{ route('admin.subjects.index') }}">Mata Pelajaran</a><span class="separator">/</span>
    <span class="current">Tambah Baru</span>
@endsection
@section('content')
<div class="page-header"><h1>Tambah Mata Pelajaran</h1><p>Isi data mapel, lalu hubungkan ke guru pengajar dan kelas tujuan.</p></div>
<div class="card" style="max-width: 700px;">
    <form action="{{ route('admin.subjects.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Nama Mata Pelajaran <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Contoh: Pemrograman Web">
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="code">Kode Mapel <span class="required">*</span></label>
                    <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" required placeholder="Contoh: PW-XII-RPL">
                    @error('code')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="level">Jenjang / Tingkat <span class="required">*</span></label>
                    <select id="level" name="level" class="form-control form-select @error('level') is-invalid @enderror" required>
                        <option value="">-- Pilih Jenjang --</option>
                        <option value="X" {{ old('level') == 'X' ? 'selected' : '' }}>Kelas X</option>
                        <option value="XI" {{ old('level') == 'XI' ? 'selected' : '' }}>Kelas XI</option>
                        <option value="XII" {{ old('level') == 'XII' ? 'selected' : '' }}>Kelas XII</option>
                    </select>
                    @error('level')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="major_id">Jurusan <span class="text-muted">(Opsional)</span></label>
                    <select id="major_id" name="major_id" class="form-control form-select @error('major_id') is-invalid @enderror">
                        <option value="">-- Umum / Semua Jurusan --</option>
                        @foreach($majors as $major)
                            <option value="{{ $major->id }}" {{ old('major_id') == $major->id ? 'selected' : '' }}>
                                {{ $major->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('major_id')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-group mb-0">
                <label class="form-label" for="description">Deskripsi (Opsional)</label>
                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                @error('description')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="card-footer d-flex justify-between">
            <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="ri-save-3-line"></i> Simpan Mata Pelajaran</button>
        </div>
    </form>
</div>
@endsection
