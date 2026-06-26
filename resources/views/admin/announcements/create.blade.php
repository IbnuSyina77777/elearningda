@extends('layouts.app')

@section('title', 'Buat Pengumuman Baru')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <a href="{{ route('admin.announcements.index') }}">Pengumuman</a>
    <span class="separator">/</span>
    <span class="current">Buat Baru</span>
@endsection

@section('content')
<div class="page-header d-flex justify-between align-center">
    <div>
        <a href="{{ route('admin.announcements.index') }}" class="btn btn-sm btn-outline mb-2" style="padding: 2px 8px; font-size:12px;">
            <i class="ri-arrow-left-line"></i> Kembali
        </a>
        <h1>Buat Pengumuman</h1>
        <p>Tulis informasi yang ingin dibagikan.</p>
    </div>
</div>

<div class="card" style="max-width: 800px;">
    <form action="{{ route('admin.announcements.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group mb-3">
                <label class="form-label">Judul Pengumuman <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            
            <div class="form-group mb-3">
                <label class="form-label">Isi Pengumuman <span class="text-danger">*</span></label>
                <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="8" required>{{ old('content') }}</textarea>
                <div class="text-sm text-muted mt-1">Anda bisa menggunakan format teks biasa, atau tag HTML dasar untuk format tebal/miring.</div>
                @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="form-label">Target Audiens <span class="text-danger">*</span></label>
                        <select name="target_audience" class="form-control form-select @error('target_audience') is-invalid @enderror" required>
                            <option value="all" {{ old('target_audience') == 'all' ? 'selected' : '' }}>Semua (Guru & Siswa)</option>
                            <option value="teachers" {{ old('target_audience') == 'teachers' ? 'selected' : '' }}>Hanya Guru</option>
                            <option value="students" {{ old('target_audience') == 'students' ? 'selected' : '' }}>Hanya Siswa</option>
                        </select>
                        @error('target_audience')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6 d-flex align-center">
                    <div class="form-group mb-0" style="margin-top: 15px;">
                        <label class="d-flex align-center gap-sm cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="width:18px;height:18px;">
                            <span style="font-weight:600;">Terbitkan Langsung (Aktif)</span>
                        </label>
                        <div class="text-sm text-muted ml-4">Jika tidak dicentang, akan disimpan sebagai draf.</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex justify-between">
            <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <i class="ri-save-line"></i> Simpan Pengumuman
            </button>
        </div>
    </form>
</div>
@endsection
