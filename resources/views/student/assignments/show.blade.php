@extends('layouts.app')
@section('title', 'Detail Tugas')
@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}"><i class="ri-home-4-line"></i></a><span class="separator">/</span>
    <a href="{{ route('student.subjects.show', $assignment->subject_id) }}">{{ $assignment->subject->code }}</a><span class="separator">/</span>
    <span class="current">Tugas</span>
@endsection
@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-md">
    <div>
        <h1>{{ $assignment->title }}</h1>
        <p>{{ $assignment->subject->name }} · Guru: {{ $assignment->subject->teacher->name ?? '-' }}</p>
    </div>
    <a href="{{ route('student.subjects.show', $assignment->subject_id) }}" class="btn btn-secondary"><i class="ri-arrow-left-line"></i> Kembali</a>
</div>

<div class="dashboard-grid">
    {{-- Left: Assignment Detail --}}
    <div>
        <div class="card mb-3">
            <div class="card-header"><h3>Instruksi Tugas</h3></div>
            <div class="card-body">
                @if($assignment->description)
                    <div style="line-height:1.8;white-space:pre-wrap;">{{ $assignment->description }}</div>
                @else
                    <p class="text-muted">Tidak ada deskripsi tambahan.</p>
                @endif

                @if($assignment->file_path)
                    <div style="margin-top:1rem;padding:1rem;background:var(--gray-50);border-radius:var(--radius-md);border:1px dashed var(--border-color);">
                        <strong><i class="ri-attachment-line"></i> File Lampiran:</strong>
                        <a href="{{ asset('storage/' . $assignment->file_path) }}" target="_blank" class="btn btn-sm btn-outline ml-2"><i class="ri-download-line"></i> {{ $assignment->file_name }}</a>
                    </div>
                @endif

                <div class="d-flex gap-md mt-3" style="flex-wrap:wrap;">
                    <div>
                        <span class="text-sm text-muted">Deadline:</span>
                        <strong style="display:block;">{{ $assignment->due_date->format('d F Y, H:i') }}</strong>
                    </div>
                    <div>
                        <span class="text-sm text-muted">Status:</span>
                        @if($assignment->is_overdue)
                            <span class="badge badge-danger" style="display:block;margin-top:4px;">Melewati Deadline</span>
                        @else
                            <span class="badge badge-success" style="display:block;margin-top:4px;">Masih Dibuka</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right: Submission --}}
    <div>
        <div class="card">
            <div class="card-header"><h3>Pengumpulan Tugas</h3></div>
            <div class="card-body">
                @if($submission)
                    {{-- Already submitted --}}
                    <div style="padding:1rem;background:rgba(16, 185, 129, 0.08);border:1px solid rgba(16, 185, 129, 0.2);border-radius:var(--radius-md);margin-bottom:1rem;">
                        <i class="ri-checkbox-circle-fill" style="color:var(--success-500);margin-right:6px;"></i>
                        <strong>Tugas sudah dikumpulkan!</strong>
                        <div class="text-sm text-muted mt-1">Dikirim pada: {{ $submission->submitted_at->format('d M Y, H:i') }}</div>
                    </div>
                    
                    <table class="table-details">
                        <tr><th>File</th><td><a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank"><i class="ri-download-line"></i> {{ $submission->file_name }}</a></td></tr>
                        @if($submission->notes)<tr><th>Catatan</th><td>{{ $submission->notes }}</td></tr>@endif
                        <tr><th>Nilai</th><td>
                            @if($submission->grade !== null)
                                <span class="badge {{ $submission->grade >= 75 ? 'badge-success' : ($submission->grade >= 50 ? 'badge-warning' : 'badge-danger') }}" style="font-size:1.2rem;padding:6px 16px;">{{ $submission->grade }}</span>
                            @else
                                <span class="badge badge-secondary">Belum Dinilai</span>
                            @endif
                        </td></tr>
                        @if($submission->feedback)<tr><th>Feedback Guru</th><td style="font-style:italic;">{{ $submission->feedback }}</td></tr>@endif
                    </table>

                    @if($submission->is_late)
                        <div class="text-sm mt-2" style="color:var(--danger-500);"><i class="ri-error-warning-line"></i> Tugas dikumpulkan melewati batas waktu.</div>
                    @endif
                @else
                    {{-- Submit form --}}
                    @if(!$assignment->is_overdue)
                        <form action="{{ route('student.assignments.submit', $assignment->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Upload File Jawaban <span class="required">*</span></label>
                                <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" required accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.jpeg,.png,.zip">
                                <span class="form-hint">Format: PDF, DOC, PPT, gambar, atau ZIP. Maks 10MB.</span>
                                @error('file')<span class="form-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Catatan (Opsional)</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Tambahkan catatan untuk guru...">{{ old('notes') }}</textarea>
                                @error('notes')<span class="form-error">{{ $message }}</span>@enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-full"><i class="ri-upload-2-line"></i> Kumpulkan Tugas</button>
                        </form>
                    @else
                        <div style="padding:1rem;background:rgba(220, 38, 38, 0.08);border:1px solid rgba(220, 38, 38, 0.2);border-radius:var(--radius-md);">
                            <i class="ri-error-warning-fill" style="color:var(--danger-500);margin-right:6px;"></i>
                            <strong>Batas waktu telah lewat.</strong>
                            <div class="text-sm text-muted mt-1">Hubungi guru pengajar jika Anda memerlukan perpanjangan waktu.</div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
