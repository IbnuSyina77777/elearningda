@extends('layouts.app')

@section('title', 'Administrasi Mengajar')
@section('breadcrumb')
    <a href="{{ route('teacher.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <span class="current">Administrasi Mengajar</span>
@endsection

@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-md">
    <div>
        <h1>Administrasi Mengajar</h1>
        <p>Kelola dokumen kelengkapan Kurikulum Merdeka Anda (Modul Ajar, ATP, dll).</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="openModal('uploadModal')">
        <i class="ri-upload-cloud-2-line"></i> Unggah Dokumen
    </button>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('teacher.administrations.index') }}" method="GET" class="d-flex flex-wrap gap-md align-center">
            <div class="form-group m-0" style="flex:1; min-width:200px;">
                <label class="form-label text-sm">Mata Pelajaran</label>
                <select name="subject_id" class="form-control form-select">
                    <option value="">Semua Mata Pelajaran</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group m-0" style="flex:1; min-width:200px;">
                <label class="form-label text-sm">Jenis Dokumen</label>
                <select name="type" class="form-control form-select">
                    <option value="">Semua Jenis</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group m-0 d-flex gap-sm" style="margin-top: 24px !important;">
                <button type="submit" class="btn btn-outline"><i class="ri-filter-3-line"></i> Filter</button>
                @if(request('subject_id') || request('type'))
                    <a href="{{ route('teacher.administrations.index') }}" class="btn btn-outline">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tgl Unggah</th>
                        <th>Judul Dokumen</th>
                        <th>Mata Pelajaran</th>
                        <th>Keterangan</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($administrations as $admin)
                        <tr>
                            <td style="white-space: nowrap;">
                                {{ $admin->created_at->format('d M Y') }}
                            </td>
                            <td>
                                <strong>{{ $admin->title }}</strong>
                                <div class="text-sm text-muted" style="margin-top: 4px;">
                                    <span class="badge badge-info" style="font-size:10px;">{{ $admin->type }}</span>
                                </div>
                            </td>
                            <td>{{ $admin->subject->name ?? '-' }}</td>
                            <td>{{ $admin->description ?? '-' }}</td>
                            <td class="text-right">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="previewDocument('{{ asset('storage/' . $admin->file_path) }}', '{{ $admin->title }}')">
                                    <i class="ri-eye-line"></i> Lihat
                                </button>
                                <a href="{{ asset('storage/' . $admin->file_path) }}" target="_blank" class="btn btn-sm btn-outline">
                                    <i class="ri-download-2-line"></i> Unduh
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ route('teacher.administrations.destroy', $admin->id) }}', 'Yakin ingin menghapus dokumen ini?')">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada dokumen administrasi yang diunggah.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($administrations->hasPages())
        <div class="card-footer" style="background:#fff;">
            {{ $administrations->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>

{{-- Modal Upload --}}
<div id="uploadModal" class="modal-overlay">
    <div class="card modal" style="width: 100%; max-width: 500px; margin: auto;">
        <div class="card-header d-flex justify-between align-center">
            <h3 class="m-0">Unggah Dokumen Administrasi</h3>
            <button type="button" class="btn btn-sm btn-outline" style="border:none;" onclick="closeModal('uploadModal')"><i class="ri-close-line"></i></button>
        </div>
        <form action="{{ route('teacher.administrations.store') }}" method="POST" enctype="multipart/form-data" onsubmit="document.getElementById('submitBtn').disabled = true; document.getElementById('submitBtn').innerHTML = '<i class=\'ri-loader-4-line ri-spin\'></i> Mengunggah...';">
            @csrf
            <div class="card-body">
                <div class="form-group mb-3">
                    <label class="form-label">Mata Pelajaran <span class="text-danger">*</span></label>
                    <select name="subject_id" class="form-control form-select" required>
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group mb-3">
                    <label class="form-label">Jenis Dokumen <span class="text-danger">*</span></label>
                    <select name="type" class="form-control form-select" required>
                        <option value="">-- Pilih Jenis --</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group mb-3">
                    <label class="form-label">Judul Dokumen <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" required placeholder="Contoh: Modul Ajar Bab 1 - Jaringan Komputer Dasar">
                </div>
                
                <div class="form-group mb-3">
                    <label class="form-label">File (PDF/Word/Excel) <span class="text-danger">*</span></label>
                    <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" required>
                    <div class="text-sm text-muted mt-1">Maksimal ukuran file 10MB.</div>
                </div>
                
                <div class="form-group m-0">
                    <label class="form-label">Keterangan (Opsional)</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Catatan tambahan bila ada..."></textarea>
                </div>
            </div>
            <div class="card-footer d-flex justify-between">
                <button type="button" class="btn btn-secondary" onclick="closeModal('uploadModal')">Batal</button>
                <button type="submit" id="submitBtn" class="btn btn-primary"><i class="ri-save-line"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Preview Dokumen --}}
<div id="previewModal" class="modal-overlay">
    <div class="card modal" style="width: 100%; max-width: 800px; height: 90vh; display: flex; flex-direction: column; margin: auto;">
        <div class="card-header d-flex justify-between align-center">
            <h3 class="m-0" id="previewTitle">Pratinjau Dokumen</h3>
            <button type="button" class="btn btn-sm btn-outline" style="border:none;" onclick="closeModal('previewModal')"><i class="ri-close-line"></i></button>
        </div>
        <div class="card-body p-0" style="flex: 1; overflow: hidden; position: relative;">
            <div id="previewNotSupported" class="text-center p-4" style="display:none; position: absolute; top:50%; left:50%; transform:translate(-50%,-50%);">
                <i class="ri-file-download-line text-muted" style="font-size:48px;"></i>
                <p class="mt-3">Format dokumen ini mungkin tidak bisa dipratinjau langsung di peramban.<br>File akan otomatis terunduh atau Anda dapat mengunduhnya secara manual.</p>
            </div>
            <iframe id="previewFrame" src="" style="width:100%; height:100%; border:none; z-index: 1; position: relative;"></iframe>
        </div>
    </div>
</div>

<script>
    function previewDocument(url, title) {
        document.getElementById('previewTitle').innerText = title;
        document.getElementById('previewFrame').src = url;
        
        // Show fallback message if it's not a pdf (rudimentary check based on extension in url)
        if (url.toLowerCase().endsWith('.pdf')) {
            document.getElementById('previewNotSupported').style.display = 'none';
        } else {
            document.getElementById('previewNotSupported').style.display = 'block';
        }
        
        openModal('previewModal');
    }
</script>
@endsection
