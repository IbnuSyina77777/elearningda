@extends('layouts.app')

@section('title', 'Edit Jadwal Pelajaran')

@section('content')
<div class="page-header d-flex justify-between align-center">
    <div>
        <h1>Edit Jadwal Pelajaran</h1>
    </div>
    <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary">
        <i class="ri-arrow-left-line"></i> Kembali
    </a>
</div>

<div class="card" style="max-width: 800px;">
    <form action="{{ route('admin.schedules.update', $schedule->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kelas <span class="required">*</span></label>
                    <select name="classroom_id" id="classroom_id" class="form-control" required data-selected="{{ $schedule->classroom_id }}">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classrooms as $classroom)
                            <option value="{{ $classroom->id }}" {{ $schedule->classroom_id == $classroom->id ? 'selected' : '' }}>
                                {{ $classroom->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mata Pelajaran <span class="required">*</span></label>
                    <select name="subject_id" id="subject_id" class="form-control" required data-selected="{{ $schedule->subject_id }}">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ $schedule->subject_id == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Guru Pengajar <span class="required">*</span></label>
                    <select name="teacher_id" id="teacher_id" class="form-control" required data-selected="{{ $schedule->teacher_id }}">
                        <option value="">-- Pilih Guru --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ $schedule->teacher_id == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Hari <span class="required">*</span></label>
                    <select name="day_of_week" class="form-control" required>
                        <option value="">-- Pilih Hari --</option>
                        @foreach($days as $day)
                            <option value="{{ $day }}" {{ $schedule->day_of_week == $day ? 'selected' : '' }}>
                                {{ $day }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Jam Mulai <span class="required">*</span></label>
                    <input type="time" name="start_time" class="form-control" value="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Jam Selesai <span class="required">*</span></label>
                    <input type="time" name="end_time" class="form-control" value="{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}" required>
                </div>
            </div>
        </div>
        <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary"><i class="ri-save-line"></i> Update Jadwal</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const classroomsData = {
        @foreach($classrooms as $c)
            "{{ $c->id }}": { level: "{{ $c->level }}", major_id: "{{ $c->major_id }}" },
        @endforeach
    };

    const subjectsData = [
        @foreach($subjects as $s)
            { id: "{{ $s->id }}", name: "{{ $s->name }}", level: "{{ $s->level }}", major_id: "{{ $s->major_id }}" },
        @endforeach
    ];

    const subjectTeachers = {
        @foreach($subjects as $subject)
            "{{ $subject->id }}": [
                @foreach($subject->taughtBy as $teacher)
                    { id: "{{ $teacher->id }}", name: "{{ $teacher->user->name }}" },
                @endforeach
            ],
        @endforeach
    };

    const classroomSelect = document.getElementById('classroom_id');
    const subjectSelect = document.getElementById('subject_id');
    const teacherSelect = document.getElementById('teacher_id');

    classroomSelect.addEventListener('change', function(e) {
        const classId = this.value;
        const currentSubjectId = subjectSelect.dataset.selected || subjectSelect.value;
        
        subjectSelect.innerHTML = '<option value="">-- Pilih Mata Pelajaran --</option>';
        if (!currentSubjectId) teacherSelect.innerHTML = '<option value="">-- Pilih Guru --</option>';
        
        if (classId && classroomsData[classId]) {
            const cls = classroomsData[classId];
            
            const validSubjects = subjectsData.filter(s => {
                return s.level === cls.level && (s.major_id === "" || s.major_id === cls.major_id);
            });
            
            validSubjects.forEach(function(subject) {
                const option = document.createElement('option');
                option.value = subject.id;
                option.textContent = subject.name;
                subjectSelect.appendChild(option);
            });
            
            const hasOption = Array.from(subjectSelect.options).some(opt => opt.value === currentSubjectId);
            if (hasOption) {
                subjectSelect.value = currentSubjectId;
                subjectSelect.dispatchEvent(new Event('change'));
            }
        }
    });

    subjectSelect.addEventListener('change', function(e) {
        const subjectId = this.value;
        const currentTeacherId = teacherSelect.dataset.selected || teacherSelect.value;
        
        teacherSelect.innerHTML = '<option value="">-- Pilih Guru --</option>';
        
        if (subjectId && subjectTeachers[subjectId]) {
            subjectTeachers[subjectId].forEach(function(teacher) {
                const option = document.createElement('option');
                option.value = teacher.id;
                option.textContent = teacher.name;
                teacherSelect.appendChild(option);
            });
            
            // Restore previous value if possible
            const hasOption = Array.from(teacherSelect.options).some(opt => opt.value === currentTeacherId);
            if (hasOption) {
                teacherSelect.value = currentTeacherId;
            } else if (subjectTeachers[subjectId].length === 1 && !e.isTrusted) {
                // Auto-select if only 1 teacher available, but only on initial load
                teacherSelect.value = subjectTeachers[subjectId][0].id;
            }
        }
    });
    
    // Trigger initial population
    classroomSelect.dispatchEvent(new Event('change'));
});
</script>
@endsection
