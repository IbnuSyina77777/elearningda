@extends('layouts.app')

@section('title', 'Tambah Kategori Pembayaran')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <a href="{{ route('admin.payment-categories.index') }}">Kategori Pembayaran</a>
    <span class="separator">/</span>
    <span class="current">Tambah Baru</span>
@endsection

@section('content')
<div class="page-header">
    <h1>Tambah Kategori Pembayaran</h1>
</div>

<div class="card" style="max-width: 600px;">
    <form action="{{ route('admin.payment-categories.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Jenis Kategori Pembayaran <span class="required">*</span></label>
                    <select id="name" name="name" class="form-control form-select @error('name') is-invalid @enderror" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="PAS" {{ old('name') == 'PAS' ? 'selected' : '' }}>Penilaian Akhir Semester (PAS)</option>
                        <option value="PTS" {{ old('name') == 'PTS' ? 'selected' : '' }}>Penilaian Tengah Semester (PTS)</option>
                        <option value="UJIKOM" {{ old('name') == 'UJIKOM' ? 'selected' : '' }}>Uji Kompetensi (UJIKOM)</option>
                        <option value="SERAGAM" {{ old('name') == 'SERAGAM' ? 'selected' : '' }}>Seragam Siswa Baru (SERAGAM)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="academic_year_id">Tahun Ajaran <span class="required">*</span></label>
                    <select id="academic_year_id" name="academic_year_id" class="form-control form-select @error('academic_year_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ (old('academic_year_id') == $year->id || $year->is_active) ? 'selected' : '' }}>
                                {{ $year->name }} {{ $year->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('academic_year_id')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="semester">Semester <span class="required">*</span></label>
                    <select id="semester" name="semester" class="form-control form-select @error('semester') is-invalid @enderror" required>
                        <option value="">-- Pilih Semester --</option>
                        @for($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                        @endfor
                    </select>
                    @error('semester')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="default_amount">Nominal Tagihan Default <span class="required">*</span></label>
                    <input type="number" id="default_amount" name="default_amount" class="form-control @error('default_amount') is-invalid @enderror" value="{{ old('default_amount') }}" required min="0">
                    @error('default_amount')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group mb-0" style="flex: 1;">
                    <label class="form-label" for="description">Deskripsi (Opsional)</label>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                    @error('description')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group mb-0" style="flex: 1;">
                    <label class="form-label" for="classroom_id">Pilih Kelas (Jalan Pintas) <span class="badge badge-info" style="font-size:0.6rem; margin-left:4px;">Baru</span></label>
                    <select id="classroom_id" name="classroom_id" class="form-control form-select @error('classroom_id') is-invalid @enderror">
                        <option value="">-- Hanya Simpan Kategori --</option>
                        <option value="all" {{ old('classroom_id') == 'all' ? 'selected' : '' }} style="font-weight: bold; color: var(--primary);">Semua Kelas (Seluruh Siswa)</option>
                        @foreach($classrooms as $room)
                            <option value="{{ $room->id }}" data-level="{{ $room->level }}" {{ old('classroom_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->name }} ({{ $room->major->code }})
                            </option>
                        @endforeach
                    </select>
                    <span class="form-hint">Opsi ini memungkinkan sistem otomatis membuatkan tagihan untuk kelas terpilih tepat setelah kategori ini disimpan. Kosongkan jika tidak ingin generate tagihan sekarang.</span>
                    @error('classroom_id')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex justify-between">
            <a href="{{ route('admin.payment-categories.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="ri-save-3-line"></i> Simpan Data</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const semesterSelect = document.getElementById('semester');
        const classroomSelect = document.getElementById('classroom_id');
        const originalOptions = Array.from(classroomSelect.options);

        function filterClassrooms() {
            const semester = parseInt(semesterSelect.value);
            let targetLevel = null;
            
            if (semester === 1 || semester === 2) targetLevel = 'X';
            if (semester === 3 || semester === 4) targetLevel = 'XI';
            if (semester === 5 || semester === 6) targetLevel = 'XII';

            // Bersihkan dropdown kelas
            classroomSelect.innerHTML = '';
            
            // Tambahkan kembali opsi yang sesuai
            originalOptions.forEach(option => {
                if (!option.dataset.level || option.dataset.level === targetLevel || option.value === '' || option.value === 'all') {
                    classroomSelect.appendChild(option.cloneNode(true));
                }
            });
            
            // Reset value jika opsi yang terpilih sebelumnya hilang
            if (!Array.from(classroomSelect.options).some(opt => opt.selected)) {
                classroomSelect.value = '';
            }
        }

        semesterSelect.addEventListener('change', filterClassrooms);
        filterClassrooms(); // Initial call
    });
</script>
@endsection
