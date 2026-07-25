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

<div class="card" style="max-width: 700px;">
    <form action="{{ route('admin.payment-categories.store') }}" method="POST">
        @csrf
        <input type="hidden" name="target_type" id="target_type" value="{{ old('target_type', '') }}">
        
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

            <div class="form-group">
                <label class="form-label" for="description">Deskripsi (Opsional)</label>
                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                @error('description')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            {{-- Target Type Switcher --}}
            <div class="form-group">
                <label class="form-label">Generate Tagihan Otomatis <span class="badge badge-info" style="font-size:0.6rem; margin-left:4px;">Opsional</span></label>
                <div class="target-switcher" style="display: flex; gap: 8px; margin-bottom: 12px;">
                    <button type="button" class="btn btn-sm target-btn" id="btn-none" onclick="setTargetType('')" style="flex:1;">
                        <i class="ri-close-line"></i> Tidak Generate
                    </button>
                    <button type="button" class="btn btn-sm target-btn" id="btn-classroom" onclick="setTargetType('classroom')" style="flex:1;">
                        <i class="ri-group-line"></i> Per Kelas
                    </button>
                    <button type="button" class="btn btn-sm target-btn" id="btn-student" onclick="setTargetType('student')" style="flex:1;">
                        <i class="ri-user-line"></i> Perorangan
                    </button>
                </div>
                <span class="form-hint">Pilih apakah ingin langsung men-generate tagihan saat kategori ini disimpan.</span>
            </div>

            {{-- Panel Kelas --}}
            <div class="form-group" id="panel-classroom" style="display: none;">
                <label class="form-label" for="classroom_id">Pilih Kelas Target</label>
                <select id="classroom_id" name="classroom_id" class="form-control form-select @error('classroom_id') is-invalid @enderror">
                    <option value="">-- Pilih Kelas --</option>
                    <option value="all" {{ old('classroom_id') == 'all' ? 'selected' : '' }} style="font-weight: bold; color: var(--primary);">Semua Kelas (Seluruh Siswa)</option>
                    @foreach($classrooms as $room)
                        <option value="{{ $room->id }}" data-level="{{ $room->level }}" {{ old('classroom_id') == $room->id ? 'selected' : '' }}>
                            {{ $room->name }} ({{ $room->major->code }})
                        </option>
                    @endforeach
                </select>
                <span class="form-hint">Tagihan akan otomatis dibuat untuk SELURUH siswa di kelas yang dipilih.</span>
                @error('classroom_id')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            {{-- Panel Perorangan --}}
            <div class="form-group" id="panel-student" style="display: none;">
                <label class="form-label">Pilih Siswa <span class="text-muted" style="font-weight:normal; font-size:0.8rem;">(bisa pilih lebih dari satu)</span></label>
                
                <div class="student-picker-wrapper" style="border: 1px solid var(--border-color, #ddd); border-radius: 8px; overflow: hidden;">
                    {{-- Search Input --}}
                    <div style="padding: 8px 12px; border-bottom: 1px solid var(--border-color, #eee); background: var(--bg-light, #f9f9f9);">
                        <div class="search-input" style="width: 100%; margin:0;">
                            <i class="ri-search-line search-icon" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#999;"></i>
                            <input type="text" id="student-search" class="form-control" placeholder="Cari nama, NIS, atau NISN..." style="padding-left: 32px; border: none; background: transparent; box-shadow: none;" autocomplete="off">
                        </div>
                    </div>

                    {{-- Selected Count --}}
                    <div id="selected-count" style="padding: 6px 12px; font-size: 0.8rem; color: var(--primary, #4f46e5); font-weight: 600; background: var(--bg-light, #f0f0ff); display: none;">
                        <i class="ri-checkbox-circle-fill"></i> <span id="count-text">0 siswa dipilih</span>
                        <a href="javascript:void(0)" onclick="clearAllStudents()" style="float: right; color: var(--danger, #e53e3e); font-size: 0.75rem; text-decoration: none;">
                            <i class="ri-close-line"></i> Hapus Semua
                        </a>
                    </div>

                    {{-- Student List --}}
                    <div id="student-list" style="max-height: 280px; overflow-y: auto;">
                        @foreach($students as $student)
                            <label class="student-item" data-name="{{ strtolower($student->user->name) }}" data-nis="{{ $student->nis }}" data-nisn="{{ $student->nisn }}" style="display: flex; align-items: center; padding: 10px 12px; gap: 10px; cursor: pointer; border-bottom: 1px solid var(--border-color, #f0f0f0); transition: background 0.15s;">
                                <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox" style="width: 16px; height: 16px; accent-color: var(--primary, #4f46e5);" {{ in_array($student->id, old('student_ids', [])) ? 'checked' : '' }}>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-weight: 600; font-size: 0.85rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $student->user->name }}</div>
                                    <div style="font-size: 0.75rem; color: #888;">
                                        NIS: {{ $student->nis ?? '-' }} &bull; 
                                        {{ $student->classroom ? $student->classroom->name . ' (' . $student->classroom->major->code . ')' : 'Tanpa Kelas' }}
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    @if($students->isEmpty())
                        <div style="padding: 24px; text-align: center; color: #999; font-size: 0.85rem;">
                            <i class="ri-user-unfollow-line" style="font-size: 1.5rem; display: block; margin-bottom: 4px;"></i>
                            Belum ada data siswa.
                        </div>
                    @endif
                </div>
                <span class="form-hint">Tagihan akan dibuat hanya untuk siswa yang dipilih di atas.</span>
                @error('student_ids')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>
        
        <div class="card-footer d-flex justify-between">
            <a href="{{ route('admin.payment-categories.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="ri-save-3-line"></i> Simpan Data</button>
        </div>
    </form>
</div>

<style>
    .target-switcher .target-btn {
        border: 2px solid var(--border-color, #ddd);
        background: var(--bg-light, #fff);
        color: var(--text-secondary, #666);
        font-weight: 600;
        font-size: 0.8rem;
        padding: 8px 12px;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    .target-switcher .target-btn:hover {
        border-color: var(--primary, #4f46e5);
        color: var(--primary, #4f46e5);
        background: var(--bg-light, #f8f7ff);
    }
    .target-switcher .target-btn.active {
        border-color: var(--primary, #4f46e5);
        background: var(--primary, #4f46e5);
        color: #fff;
    }
    .student-item:hover {
        background: var(--bg-light, #f7f7ff) !important;
    }
    .student-item:has(input:checked) {
        background: rgba(79, 70, 229, 0.05) !important;
    }
    .student-item.hidden {
        display: none !important;
    }

    /* Custom scrollbar for student list */
    #student-list::-webkit-scrollbar {
        width: 6px;
    }
    #student-list::-webkit-scrollbar-track {
        background: transparent;
    }
    #student-list::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 3px;
    }
    #student-list::-webkit-scrollbar-thumb:hover {
        background: #aaa;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const semesterSelect = document.getElementById('semester');
        const classroomSelect = document.getElementById('classroom_id');
        const originalOptions = Array.from(classroomSelect.options);

        // Restore target type from old input
        const oldTargetType = '{{ old("target_type", "") }}';
        if (oldTargetType) {
            setTargetType(oldTargetType);
        } else {
            setTargetType('');
        }

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

        // Student search
        const searchInput = document.getElementById('student-search');
        const studentItems = document.querySelectorAll('.student-item');
        
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            studentItems.forEach(item => {
                const name = item.dataset.name || '';
                const nis = item.dataset.nis || '';
                const nisn = item.dataset.nisn || '';
                
                if (name.includes(query) || nis.includes(query) || nisn.includes(query)) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        });

        // Student checkbox count
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateSelectedCount);
        });
        updateSelectedCount();
    });

    function setTargetType(type) {
        document.getElementById('target_type').value = type;
        
        // Toggle active button
        document.getElementById('btn-none').classList.toggle('active', type === '');
        document.getElementById('btn-classroom').classList.toggle('active', type === 'classroom');
        document.getElementById('btn-student').classList.toggle('active', type === 'student');

        // Toggle panels
        document.getElementById('panel-classroom').style.display = type === 'classroom' ? 'block' : 'none';
        document.getElementById('panel-student').style.display = type === 'student' ? 'block' : 'none';
    }

    function updateSelectedCount() {
        const checked = document.querySelectorAll('.student-checkbox:checked');
        const countEl = document.getElementById('selected-count');
        const textEl = document.getElementById('count-text');
        
        if (checked.length > 0) {
            countEl.style.display = 'block';
            textEl.textContent = checked.length + ' siswa dipilih';
        } else {
            countEl.style.display = 'none';
        }
    }

    function clearAllStudents() {
        document.querySelectorAll('.student-checkbox').forEach(cb => {
            cb.checked = false;
        });
        updateSelectedCount();
    }
</script>
@endsection
