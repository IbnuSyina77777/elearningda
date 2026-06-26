@extends('layouts.app')
@section('title', 'Edit Guru')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a><span class="separator">/</span>
    <a href="{{ route('admin.teachers.index') }}">Data Guru</a><span class="separator">/</span>
    <span class="current">Edit Data</span>
@endsection
@section('content')
<div class="page-header"><h1>Edit Data Guru</h1><p>Ubah profil dan akun login untuk <strong>{{ $teacher->name }}</strong>.</p></div>
<div class="card" style="max-width: 800px;">
    <form action="{{ route('admin.teachers.update', $teacher->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="card-body">
            <h3 style="font-size:1.1rem;margin-bottom:16px;color:var(--primary-700);border-bottom:1px solid var(--border-color);padding-bottom:8px;"><i class="ri-user-settings-line"></i> Akun Login</h3>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $teacher->user->name) }}" required>
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email Login <span class="required">*</span></label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $teacher->user->email) }}" required>
                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Password Baru</label>
                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Kosongkan jika tidak ingin mengubah">
                @error('password')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <h3 style="font-size:1.1rem;margin-top:24px;margin-bottom:16px;color:var(--primary-700);border-bottom:1px solid var(--border-color);padding-bottom:8px;"><i class="ri-profile-line"></i> Data Pribadi</h3>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="nip">NIP <span class="required">*</span></label>
                    <input type="text" id="nip" name="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip', $teacher->nip) }}" required>
                    @error('nip')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="specialization">Spesialisasi</label>
                    <input type="text" id="specialization" name="specialization" class="form-control @error('specialization') is-invalid @enderror" value="{{ old('specialization', $teacher->specialization) }}">
                    @error('specialization')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="position">Jabatan</label>
                    <select id="position" name="position" class="form-control form-select @error('position') is-invalid @enderror">
                        <option value="Guru Mata Pelajaran" {{ old('position', $teacher->position) == 'Guru Mata Pelajaran' ? 'selected' : '' }}>Guru Mata Pelajaran</option>
                        <option value="Wali Kelas" {{ old('position', $teacher->position) == 'Wali Kelas' ? 'selected' : '' }}>Wali Kelas</option>
                        <option value="Kepala Sekolah" {{ old('position', $teacher->position) == 'Kepala Sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                        <option value="Staf Tata Usaha" {{ old('position', $teacher->position) == 'Staf Tata Usaha' ? 'selected' : '' }}>Staf Tata Usaha</option>
                    </select>
                    @error('position')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group" id="classroom_group" style="display: {{ old('position', $teacher->position) == 'Wali Kelas' ? 'block' : 'none' }};">
                    <label class="form-label" for="classroom_id">Pilih Kelas</label>
                    <select id="classroom_id" name="classroom_id" class="form-control form-select @error('classroom_id') is-invalid @enderror">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classrooms as $room)
                            <option value="{{ $room->id }}" {{ old('classroom_id', $teacher->classroom_id) == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                        @endforeach
                    </select>
                    @error('classroom_id')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group" id="taught_classes_group" style="display: {{ in_array(old('position', $teacher->position), ['Guru Mata Pelajaran', 'Wali Kelas']) || !old('position', $teacher->position) ? 'block' : 'none' }};">
                    <label class="form-label" for="taught_classes">Kelas yang Diajar</label>
                    <select id="taught_classes" name="taught_classes[]" class="form-control form-select @error('taught_classes') is-invalid @enderror" multiple style="height: 120px;">
                        @foreach($classrooms as $room)
                            <option value="{{ $room->id }}" data-level="{{ $room->level }}" {{ in_array($room->id, old('taught_classes', $teacher->taughtClassrooms->pluck('id')->toArray())) ? 'selected' : '' }}>{{ $room->name }}</option>
                        @endforeach
                    </select>
                    <span class="form-hint">Tahan tombol Ctrl/Command untuk memilih lebih dari satu.</span>
                    @error('taught_classes')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group" id="taught_subjects_group" style="display: {{ in_array(old('position', $teacher->position), ['Guru Mata Pelajaran', 'Wali Kelas']) || !old('position', $teacher->position) ? 'block' : 'none' }};">
                    <label class="form-label" for="taught_subjects">Mata Pelajaran yang Diampu</label>
                    <select id="taught_subjects" name="taught_subjects[]" class="form-control form-select @error('taught_subjects') is-invalid @enderror" multiple style="height: 120px;">
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" data-level="{{ $subject->level }}" {{ in_array($subject->id, old('taught_subjects', $teacher->taughtSubjects->pluck('id')->toArray())) ? 'selected' : '' }}>{{ $subject->name }} (Kelas {{ $subject->level }})</option>
                        @endforeach
                    </select>
                    <span class="form-hint">Pilih mata pelajaran yang diampu.</span>
                    @error('taught_subjects')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="phone">No. Handphone</label>
                    <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $teacher->phone) }}">
                    @error('phone')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group" style="flex:1;">
                    <label class="form-label" for="photo">Foto Guru</label>
                    @if($teacher->photo)
                        <div style="margin-bottom:0.5rem;"><img src="{{ asset('storage/' . $teacher->photo) }}" style="width:80px;height:80px;border-radius:var(--radius-md);object-fit:cover;border:2px solid var(--border-color);"></div>
                    @endif
                    <input type="file" id="photo" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg">
                    @error('photo')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-group mb-0">
                <label class="form-label" for="address">Alamat Lengkap</label>
                <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $teacher->address) }}</textarea>
                @error('address')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="card-footer d-flex justify-between">
            <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="ri-save-3-line"></i> Update Data Guru</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const positionSelect = document.getElementById('position');
        const classroomGroup = document.getElementById('classroom_group');
        const taughtClassesGroup = document.getElementById('taught_classes_group');

        const taughtSubjectsGroup = document.getElementById('taught_subjects_group');
        const taughtSubjectsSelect = document.getElementById('taught_subjects');
        const taughtClassesSelect = document.getElementById('taught_classes');
        const classOptions = Array.from(taughtClassesSelect.options);

        function toggleGroups() {
            if (positionSelect.value === 'Wali Kelas') {
                classroomGroup.style.display = 'block';
                taughtClassesGroup.style.display = 'block';
                taughtSubjectsGroup.style.display = 'block';
            } else if (positionSelect.value === 'Guru Mata Pelajaran' || positionSelect.value === '') {
                classroomGroup.style.display = 'none';
                taughtClassesGroup.style.display = 'block';
                taughtSubjectsGroup.style.display = 'block';
            } else {
                classroomGroup.style.display = 'none';
                taughtClassesGroup.style.display = 'none';
                taughtSubjectsGroup.style.display = 'none';
            }
        }

        function filterClassesBySubjectLevel() {
            const selectedLevels = Array.from(taughtSubjectsSelect.selectedOptions).map(opt => opt.getAttribute('data-level'));
            
            if (selectedLevels.length === 0) {
                classOptions.forEach(opt => {
                    opt.style.display = '';
                    opt.disabled = false;
                });
                return;
            }

            classOptions.forEach(opt => {
                const classLevel = opt.getAttribute('data-level');
                if (selectedLevels.includes(classLevel)) {
                    opt.style.display = '';
                    opt.disabled = false;
                } else {
                    opt.style.display = 'none';
                    opt.disabled = true;
                    opt.selected = false;
                }
            });
        }

        positionSelect.addEventListener('change', toggleGroups);
        taughtSubjectsSelect.addEventListener('change', filterClassesBySubjectLevel);
        
        toggleGroups(); // Run on load to set initial state
        filterClassesBySubjectLevel(); // Run on load for old inputs
    });
</script>
@endsection
