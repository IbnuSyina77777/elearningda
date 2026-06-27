@extends('layouts.app')
@section('title', 'Tambah Guru')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a><span class="separator">/</span>
    <a href="{{ route('admin.teachers.index') }}">Data Guru</a><span class="separator">/</span>
    <span class="current">Tambah Baru</span>
@endsection
@section('content')
<div class="page-header"><h1>Tambah Guru Baru</h1><p>Data ini akan otomatis membuatkan akun login untuk portal guru.</p></div>
<div class="card" style="max-width: 800px;">
    <form action="{{ route('admin.teachers.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <h3 style="font-size:1.1rem;margin-bottom:16px;color:var(--primary-700);border-bottom:1px solid var(--border-color);padding-bottom:8px;"><i class="ri-user-settings-line"></i> Akun Login</h3>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email Login <span class="required">*</span></label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Password <span class="required">*</span></label>
                <input type="text" id="password" name="password" class="form-control @error('password') is-invalid @enderror" value="password123" required>
                <span class="form-hint">Default password adalah "password123".</span>
                @error('password')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <h3 style="font-size:1.1rem;margin-top:24px;margin-bottom:16px;color:var(--primary-700);border-bottom:1px solid var(--border-color);padding-bottom:8px;"><i class="ri-profile-line"></i> Data Pribadi</h3>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="nip">NIP <span class="required">*</span></label>
                    <input type="text" id="nip" name="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip') }}" required>
                    @error('nip')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="specialization">Spesialisasi / Bidang Keahlian</label>
                    <input type="text" id="specialization" name="specialization" class="form-control @error('specialization') is-invalid @enderror" value="{{ old('specialization') }}" placeholder="Contoh: Matematika, RPL">
                    @error('specialization')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="position">Jabatan (Bisa pilih lebih dari satu)</label>
                    <select id="position" name="position[]" class="form-control form-select @error('position') is-invalid @enderror" multiple size="6">
                        <optgroup label="Manajemen Sekolah">
                            <option value="Kepala Sekolah" {{ in_array('Kepala Sekolah', old('position', [])) ? 'selected' : '' }}>Kepala Sekolah</option>
                            <option value="Wakasek Kurikulum" {{ in_array('Wakasek Kurikulum', old('position', [])) ? 'selected' : '' }}>Wakasek Kurikulum</option>
                            <option value="Wakasek Kesiswaan" {{ in_array('Wakasek Kesiswaan', old('position', [])) ? 'selected' : '' }}>Wakasek Kesiswaan</option>
                            <option value="Wakasek Hubin / Humas" {{ in_array('Wakasek Hubin / Humas', old('position', [])) ? 'selected' : '' }}>Wakasek Hubin / Humas</option>
                            <option value="Wakasek Sarana Prasarana" {{ in_array('Wakasek Sarana Prasarana', old('position', [])) ? 'selected' : '' }}>Wakasek Sarana Prasarana</option>
                        </optgroup>
                        <optgroup label="Kejuruan & Bimbingan">
                            <option value="Kepala Program Keahlian (Kajur)" {{ in_array('Kepala Program Keahlian (Kajur)', old('position', [])) ? 'selected' : '' }}>Kepala Program Keahlian (Kajur)</option>
                            <option value="Kepala Bengkel / Laboratorium" {{ in_array('Kepala Bengkel / Laboratorium', old('position', [])) ? 'selected' : '' }}>Kepala Bengkel / Laboratorium</option>
                            <option value="Guru Bimbingan Konseling (BK)" {{ in_array('Guru Bimbingan Konseling (BK)', old('position', [])) ? 'selected' : '' }}>Guru Bimbingan Konseling (BK)</option>
                        </optgroup>
                        <optgroup label="Tenaga Pendidik">
                            <option value="Wali Kelas" {{ in_array('Wali Kelas', old('position', [])) ? 'selected' : '' }}>Wali Kelas</option>
                            <option value="Guru Produktif / Kejuruan" {{ in_array('Guru Produktif / Kejuruan', old('position', [])) ? 'selected' : '' }}>Guru Produktif / Kejuruan</option>
                            <option value="Guru Mata Pelajaran" {{ in_array('Guru Mata Pelajaran', old('position', ['Guru Mata Pelajaran'])) ? 'selected' : '' }}>Guru Mata Pelajaran Umum</option>
                        </optgroup>
                        <optgroup label="Tenaga Kependidikan">
                            <option value="Staf Tata Usaha (TU)" {{ in_array('Staf Tata Usaha (TU)', old('position', [])) ? 'selected' : '' }}>Staf Tata Usaha (TU)</option>
                            <option value="Pustakawan" {{ in_array('Pustakawan', old('position', [])) ? 'selected' : '' }}>Pustakawan</option>
                        </optgroup>
                    </select>
                    @error('position')<span class="form-error">{{ $message }}</span>@enderror
                    <small class="text-muted">Gunakan Ctrl/Cmd + Klik untuk memilih lebih dari satu jabatan.</small>
                </div>
                <div class="form-group" id="classroom_group" style="display: {{ old('position') == 'Wali Kelas' ? 'block' : 'none' }};">
                    <label class="form-label" for="classroom_id">Pilih Kelas</label>
                    <select id="classroom_id" name="classroom_id" class="form-control form-select @error('classroom_id') is-invalid @enderror">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classrooms as $room)
                            <option value="{{ $room->id }}" {{ old('classroom_id') == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                        @endforeach
                    </select>
                    @error('classroom_id')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group" id="major_group" style="display: {{ in_array(old('position'), ['Kepala Program Keahlian (Kajur)', 'Kepala Bengkel / Laboratorium']) ? 'block' : 'none' }};">
                    <label class="form-label" for="major_id">Pilih Jurusan <span class="required">*</span></label>
                    <select id="major_id" name="major_id" class="form-control form-select @error('major_id') is-invalid @enderror">
                        <option value="">-- Pilih Jurusan --</option>
                        @foreach($majors as $major)
                            <option value="{{ $major->id }}" {{ old('major_id') == $major->id ? 'selected' : '' }}>{{ $major->name }}</option>
                        @endforeach
                    </select>
                    @error('major_id')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                @php
                    $nonTeaching = ['Kepala Sekolah', 'Wakasek Kurikulum', 'Wakasek Kesiswaan', 'Wakasek Hubin / Humas', 'Wakasek Sarana Prasarana', 'Staf Tata Usaha (TU)', 'Pustakawan', 'Kepala Bengkel / Laboratorium', 'Kepala Program Keahlian (Kajur)'];
                    $oldPositions = old('position', ['Guru Mata Pelajaran']);
                    $hasTeaching = count(array_diff($oldPositions, $nonTeaching)) > 0;
                @endphp
                <div class="form-group" id="taught_classes_group" style="display: {{ $hasTeaching ? 'block' : 'none' }};">
                    <div class="d-flex justify-between align-center mb-1 flex-wrap gap-xs">
                        <label class="form-label mb-0" for="taught_classes">Kelas yang Diajar</label>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline" id="btn_select_x" style="padding: 2px 6px; font-size: 0.75rem;" data-tooltip="Pilih / Batal Kelas X">Kelas X</button>
                            <button type="button" class="btn btn-sm btn-outline" id="btn_select_xi" style="padding: 2px 6px; font-size: 0.75rem;" data-tooltip="Pilih / Batal Kelas XI">Kelas XI</button>
                            <button type="button" class="btn btn-sm btn-outline" id="btn_select_xii" style="padding: 2px 6px; font-size: 0.75rem;" data-tooltip="Pilih / Batal Kelas XII">Kelas XII</button>
                            <button type="button" class="btn btn-sm btn-outline" id="btn_select_all_classes" style="padding: 2px 6px; font-size: 0.75rem;" data-tooltip="Pilih / Batal Semua">Semua</button>
                        </div>
                    </div>
                    <select id="taught_classes" name="taught_classes[]" class="form-control form-select @error('taught_classes') is-invalid @enderror" multiple style="height: 120px;">
                        @foreach($classrooms as $room)
                            <option value="{{ $room->id }}" data-level="{{ $room->level }}" data-major="{{ $room->major_id }}" {{ in_array($room->id, old('taught_classes', [])) ? 'selected' : '' }}>{{ $room->name }}</option>
                        @endforeach
                    </select>
                    <span class="form-hint">Tahan tombol Ctrl/Command untuk memilih lebih dari satu.</span>
                    @error('taught_classes')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group" id="taught_subjects_group" style="display: {{ $hasTeaching ? 'block' : 'none' }};">
                    <label class="form-label" for="taught_subjects">Mata Pelajaran yang Diampu</label>
                    <select id="taught_subjects" name="taught_subjects[]" class="form-control form-select @error('taught_subjects') is-invalid @enderror" multiple style="height: 120px;">
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" data-level="{{ $subject->level }}" data-major="{{ $subject->major_id }}" {{ in_array($subject->id, old('taught_subjects', [])) ? 'selected' : '' }}>{{ $subject->name }} (Kelas {{ $subject->level }})</option>
                        @endforeach
                    </select>
                    <span class="form-hint">Pilih mata pelajaran yang diampu.</span>
                    @error('taught_subjects')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="phone">No. Handphone</label>
                    <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                    @error('phone')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="photo">Foto Guru</label>
                    <input type="file" id="photo" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg">
                    @error('photo')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-group mb-0">
                <label class="form-label" for="address">Alamat Lengkap</label>
                <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address') }}</textarea>
                @error('address')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="card-footer d-flex justify-between">
            <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="ri-save-3-line"></i> Simpan Data Guru</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const positionSelect = document.getElementById('position');
        const classroomGroup = document.getElementById('classroom_group');
        const majorGroup = document.getElementById('major_group');
        const taughtClassesGroup = document.getElementById('taught_classes_group');

        const taughtSubjectsGroup = document.getElementById('taught_subjects_group');
        const taughtSubjectsSelect = document.getElementById('taught_subjects');
        const taughtClassesSelect = document.getElementById('taught_classes');
        const classOptions = Array.from(taughtClassesSelect.options);

        const subjectOptions = Array.from(taughtSubjectsSelect.options);

        function toggleGroups() {
            const selectedOptions = Array.from(positionSelect.selectedOptions).map(opt => opt.value);
            
            if (selectedOptions.includes('Wali Kelas')) {
                classroomGroup.style.display = 'block';
            } else {
                classroomGroup.style.display = 'none';
            }

            if (selectedOptions.includes('Kepala Program Keahlian (Kajur)') || selectedOptions.includes('Kepala Bengkel / Laboratorium')) {
                majorGroup.style.display = 'block';
            } else {
                majorGroup.style.display = 'none';
            }
            
            // Non-Teaching Staff & Management cannot have taught classes/subjects if they ONLY have those positions
            const nonTeachingPositions = ['Kepala Sekolah', 'Wakasek Kurikulum', 'Wakasek Kesiswaan', 'Wakasek Hubin / Humas', 'Wakasek Sarana Prasarana', 'Staf Tata Usaha (TU)', 'Pustakawan', 'Kepala Bengkel / Laboratorium', 'Kepala Program Keahlian (Kajur)'];
            
            let hasTeaching = false;
            for (let i = 0; i < selectedOptions.length; i++) {
                if (!nonTeachingPositions.includes(selectedOptions[i])) {
                    hasTeaching = true;
                    break;
                }
            }
            
            if (!hasTeaching && selectedOptions.length > 0) {
                taughtClassesGroup.style.display = 'none';
                taughtSubjectsGroup.style.display = 'none';
            } else {
                taughtClassesGroup.style.display = 'block';
                taughtSubjectsGroup.style.display = 'block';
            }
        }

        function filterSubjectsByClass() {
            const selectedClasses = Array.from(taughtClassesSelect.selectedOptions).map(opt => ({
                level: opt.getAttribute('data-level'),
                major: opt.getAttribute('data-major')
            }));
            
            if (selectedClasses.length === 0) {
                subjectOptions.forEach(opt => {
                    opt.style.display = 'none';
                    opt.disabled = true;
                    opt.selected = false;
                });
                return;
            }

            const selectedPositions = Array.from(positionSelect.selectedOptions).map(opt => opt.value);

            subjectOptions.forEach(opt => {
                const subLevel = opt.getAttribute('data-level');
                const subMajor = opt.getAttribute('data-major');
                
                let isValid = selectedClasses.some(cls => {
                    return subLevel === cls.level && (subMajor === "" || subMajor === cls.major);
                });
                
                if (isValid) {
                    if (selectedPositions.includes('Guru Mata Pelajaran') && !selectedPositions.includes('Guru Produktif / Kejuruan')) {
                        // Hanya mapel Umum
                        if (subMajor !== "") isValid = false;
                    } else if (selectedPositions.includes('Guru Produktif / Kejuruan') && !selectedPositions.includes('Guru Mata Pelajaran')) {
                        // Hanya mapel Produktif
                        if (subMajor === "") isValid = false;
                    }
                    // Jika memiliki keduanya, maka bisa lihat keduanya
                }
                
                if (isValid) {
                    opt.style.display = '';
                    opt.disabled = false;
                } else {
                    opt.style.display = 'none';
                    opt.disabled = true;
                    opt.selected = false;
                }
            });
        }

        const btnSelectAllClasses = document.getElementById('btn_select_all_classes');
        const btnSelectX = document.getElementById('btn_select_x');
        const btnSelectXI = document.getElementById('btn_select_xi');
        const btnSelectXII = document.getElementById('btn_select_xii');

        function toggleClassesByLevel(levelStr) {
            const levelOptions = classOptions.filter(opt => opt.getAttribute('data-level') === levelStr);
            if (levelOptions.length === 0) return;
            const isAllSelected = levelOptions.every(opt => opt.selected);
            levelOptions.forEach(opt => opt.selected = !isAllSelected);
            taughtClassesSelect.dispatchEvent(new Event('change'));
        }

        if (btnSelectX) btnSelectX.addEventListener('click', () => toggleClassesByLevel('X'));
        if (btnSelectXI) btnSelectXI.addEventListener('click', () => toggleClassesByLevel('XI'));
        if (btnSelectXII) btnSelectXII.addEventListener('click', () => toggleClassesByLevel('XII'));

        if (btnSelectAllClasses) {
            btnSelectAllClasses.addEventListener('click', function() {
                const isAllSelected = classOptions.every(opt => opt.selected);
                classOptions.forEach(opt => opt.selected = !isAllSelected);
                this.textContent = isAllSelected ? 'Semua' : 'Batal Semua';
                taughtClassesSelect.dispatchEvent(new Event('change'));
            });

            taughtClassesSelect.addEventListener('change', function() {
                const isAllSelected = classOptions.length > 0 && classOptions.every(opt => opt.selected);
                btnSelectAllClasses.textContent = isAllSelected ? 'Batal Semua' : 'Semua';
            });
        }

        positionSelect.addEventListener('change', toggleGroups);
        positionSelect.addEventListener('change', filterSubjectsByClass);
        taughtClassesSelect.addEventListener('change', filterSubjectsByClass);
        
        toggleGroups(); // Run on load to set initial state
        filterSubjectsByClass(); // Run on load for old inputs
    });
</script>
@endsection
