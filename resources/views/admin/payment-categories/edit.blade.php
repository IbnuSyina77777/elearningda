@extends('layouts.app')

@section('title', 'Edit Kategori Pembayaran')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <a href="{{ route('admin.payment-categories.index') }}">Kategori Pembayaran</a>
    <span class="separator">/</span>
    <span class="current">Edit Data</span>
@endsection

@section('content')
<div class="page-header">
    <h1>Edit Kategori Pembayaran</h1>
</div>

<div class="card" style="max-width: 600px;">
    <form action="{{ route('admin.payment-categories.update', $paymentCategory->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Jenis Kategori Pembayaran <span class="required">*</span></label>
                    <select id="name" name="name" class="form-control form-select @error('name') is-invalid @enderror" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="PAS" {{ old('name', $paymentCategory->name) == 'PAS' || str_starts_with($paymentCategory->code, 'PAS') ? 'selected' : '' }}>Penilaian Akhir Semester (PAS)</option>
                        <option value="PTS" {{ old('name', $paymentCategory->name) == 'PTS' || str_starts_with($paymentCategory->code, 'PTS') ? 'selected' : '' }}>Penilaian Tengah Semester (PTS)</option>
                        <option value="UJIKOM" {{ old('name', $paymentCategory->name) == 'UJIKOM' || str_starts_with($paymentCategory->code, 'UJK') ? 'selected' : '' }}>Uji Kompetensi (UJIKOM)</option>
                        <option value="SERAGAM" {{ old('name', $paymentCategory->name) == 'SERAGAM' || str_starts_with($paymentCategory->code, 'SRG') ? 'selected' : '' }}>Seragam Siswa Baru (SERAGAM)</option>
                    </select>
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="semester">Semester <span class="required">*</span></label>
                    <select id="semester" name="semester" class="form-control form-select @error('semester') is-invalid @enderror" required>
                        <option value="">-- Pilih Semester --</option>
                        @for($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}" {{ old('semester', $paymentCategory->semester) == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                        @endfor
                    </select>
                    @error('semester')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="default_amount">Nominal Tagihan Default <span class="required">*</span></label>
                    <input type="number" id="default_amount" name="default_amount" class="form-control @error('default_amount') is-invalid @enderror" value="{{ old('default_amount', $paymentCategory->default_amount) }}" required min="0">
                    @error('default_amount')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-group mb-0">
                <label class="form-label" for="description">Deskripsi (Opsional)</label>
                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $paymentCategory->description) }}</textarea>
                @error('description')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>
        
        <div class="card-footer d-flex justify-between">
            <a href="{{ route('admin.payment-categories.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="ri-save-3-line"></i> Update Data</button>
        </div>
    </form>
</div>
@endsection
