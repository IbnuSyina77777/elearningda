@extends('layouts.app')
@section('title', 'Tambah Materi')
@section('breadcrumb')
    <a href="{{ route('teacher.dashboard') }}"><i class="ri-home-4-line"></i></a><span class="separator">/</span>
    <a href="{{ route('teacher.materials.index', $subject->id) }}">Materi · {{ $subject->code }}</a><span class="separator">/</span>
    <span class="current">Tambah</span>
@endsection
@section('content')
<div class="page-header"><h1>Tambah Materi Baru</h1><p>Untuk {{ $subject->name }} (Kelas {{ $subject->level }})</p></div>
<div class="card" style="max-width: 700px;">
    <form action="{{ route('teacher.materials.store', $subject->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="form-row">
                <div class="form-group" style="flex:2;"><label class="form-label">Judul Materi <span class="required">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                    @error('title')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group" style="flex:1;">
                    <label class="form-label" style="display:flex;justify-content:space-between;">
                        <span>Kelas Tujuan <span class="required">*</span></span>
                        <a href="#" onclick="event.preventDefault(); Array.from(document.getElementById('classrooms_select').options).forEach(o => o.selected = true);" style="font-size:0.75rem; color:var(--primary-600);">Pilih Semua</a>
                    </label>
                    <select id="classrooms_select" name="classroom_id[]" class="form-control form-select @error('classroom_id') is-invalid @enderror" required multiple style="height: 100px;">
                        @foreach($classrooms as $room)
                            <option value="{{ $room->id }}" {{ in_array($room->id, old('classroom_id', [])) ? 'selected' : '' }}>{{ $room->name }}</option>
                        @endforeach
                    </select>
                    <span class="form-hint">Tahan Ctrl/Command untuk memilih lebih dari satu.</span>
                    @error('classroom_id')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group" style="flex:1;"><label class="form-label">Urutan</label>
                    <input type="number" name="order" class="form-control" value="{{ old('order', 0) }}" min="0">
                </div>
            </div>
            <div class="form-group"><label class="form-label">Konten / Penjelasan</label>
                <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="6" placeholder="Tulis penjelasan materi di sini...">{{ old('content') }}</textarea>
                @error('content')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group mb-0"><label class="form-label">File Lampiran (PDF, DOC, PPT, ZIP, Gambar)</label>
                <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.jpeg,.png,.zip">
                <span class="form-hint">Maks 10MB.</span>
                @error('file')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="card-footer d-flex justify-between">
            <a href="{{ route('teacher.materials.index', $subject->id) }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="ri-save-3-line"></i> Simpan Materi</button>
        </div>
    </form>
</div>
@endsection
