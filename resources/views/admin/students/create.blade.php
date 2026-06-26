@extends('layouts.app')

@section('title', 'Tambah Siswa')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <a href="{{ route('admin.students.index') }}">Data Siswa</a>
    <span class="separator">/</span>
    <span class="current">Tambah Baru</span>
@endsection

@section('content')
<div class="page-header">
    <h1>Tambah Siswa Baru</h1>
    <p>Data ini akan otomatis membuatkan akun login untuk portal siswa.</p>
</div>

<div class="card" style="max-width: 800px;">
    <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <h3 style="font-size:1.1rem;margin-bottom:16px;color:var(--primary-700);border-bottom:1px solid var(--border-color);padding-bottom:8px;">
                <i class="ri-user-settings-line"></i> Akun Login
            </h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email Login <span class="required">*</span></label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="email@student.smk.test">
                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password <span class="required">*</span></label>
                <input type="text" id="password" name="password" class="form-control @error('password') is-invalid @enderror" value="password123" required>
                <span class="form-hint">Default password adalah "password123". Siswa dapat menggantinya nanti.</span>
                @error('password')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <h3 style="font-size:1.1rem;margin-top:24px;margin-bottom:16px;color:var(--primary-700);border-bottom:1px solid var(--border-color);padding-bottom:8px;">
                <i class="ri-profile-line"></i> Data Akademik & Pribadi
            </h3>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="nisn">NISN <span class="required">*</span></label>
                    <input type="text" id="nisn" name="nisn" class="form-control @error('nisn') is-invalid @enderror" value="{{ old('nisn') }}" required>
                    @error('nisn')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="nis">NIS <span class="required">*</span></label>
                    <input type="text" id="nis" name="nis" class="form-control @error('nis') is-invalid @enderror" value="{{ old('nis') }}" required>
                    @error('nis')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="classroom_id">Kelas <span class="required">*</span></label>
                    <select id="classroom_id" name="classroom_id" class="form-control form-select @error('classroom_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classrooms as $room)
                            <option value="{{ $room->id }}" {{ old('classroom_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->name }} — {{ $room->major->code }}
                            </option>
                        @endforeach
                    </select>
                    @error('classroom_id')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="gender">Jenis Kelamin <span class="required">*</span></label>
                    <select id="gender" name="gender" class="form-control form-select @error('gender') is-invalid @enderror" required>
                        <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="phone">No. Handphone (Siswa)</label>
                    <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                    @error('phone')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="parent_phone">No. WhatsApp Orang Tua</label>
                    <input type="text" id="parent_phone" name="parent_phone" class="form-control @error('parent_phone') is-invalid @enderror" value="{{ old('parent_phone') }}" placeholder="Mulai dengan 08 / 62">
                    <span class="form-hint">Digunakan untuk notifikasi tagihan (jika fitur aktif).</span>
                    @error('parent_phone')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="parent_name">Nama Orang Tua/Wali</label>
                <input type="text" id="parent_name" name="parent_name" class="form-control @error('parent_name') is-invalid @enderror" value="{{ old('parent_name') }}">
                @error('parent_name')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="photo">Foto Siswa</label>
                <input type="file" id="photo" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg">
                <span class="form-hint">Format: JPG, JPEG, atau PNG. Maks: 2MB.</span>
                @error('photo')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group mb-0">
                <label class="form-label" for="address">Alamat Lengkap</label>
                <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address') }}</textarea>
                @error('address')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>
        
        <div class="card-footer d-flex justify-between">
            <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="ri-save-3-line"></i> Simpan Data Siswa</button>
        </div>
    </form>
</div>
@endsection
