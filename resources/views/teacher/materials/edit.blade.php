@extends('layouts.app')
@section('title', 'Edit Materi')
@section('breadcrumb')
    <a href="{{ route('teacher.dashboard') }}"><i class="ri-home-4-line"></i></a><span class="separator">/</span>
    <a href="{{ route('teacher.materials.index', $subject->id) }}">Materi · {{ $subject->code }}</a><span class="separator">/</span>
    <span class="current">Edit</span>
@endsection
@section('content')
<div class="page-header"><h1>Edit Materi</h1><p>{{ $subject->name }} — {{ $material->title }}</p></div>
<div class="card" style="max-width: 700px;">
    <form action="{{ route('teacher.materials.update', [$subject->id, $material->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="form-row">
                <div class="form-group" style="flex:2;"><label class="form-label">Judul Materi <span class="required">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $material->title) }}" required>
                    @error('title')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group" style="flex:1;"><label class="form-label">Kelas Tujuan <span class="required">*</span></label>
                    <select name="classroom_id" class="form-control form-select @error('classroom_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classrooms as $room)
                            <option value="{{ $room->id }}" {{ old('classroom_id', $material->classroom_id) == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                        @endforeach
                    </select>
                    @error('classroom_id')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group" style="flex:1;"><label class="form-label">Urutan</label>
                    <input type="number" name="order" class="form-control" value="{{ old('order', $material->order) }}" min="0">
                </div>
            </div>
            <div class="form-group"><label class="form-label">Konten / Penjelasan</label>
                <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="6">{{ old('content', $material->content) }}</textarea>
                @error('content')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group mb-0"><label class="form-label">File Lampiran</label>
                @if($material->file_path)
                    <div style="margin-bottom:.5rem;"><a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="btn btn-sm btn-outline"><i class="ri-download-line"></i> {{ $material->file_name }}</a></div>
                @endif
                <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.jpeg,.png,.zip">
                <span class="form-hint">Upload baru untuk mengganti file sebelumnya.</span>
                @error('file')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="card-footer d-flex justify-between">
            <a href="{{ route('teacher.materials.index', $subject->id) }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="ri-save-3-line"></i> Update Materi</button>
        </div>
    </form>
</div>
@endsection
