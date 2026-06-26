@extends('layouts.app')

@section('title', 'Detail Administrasi Guru')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <a href="{{ route('admin.teacher-administrations.index') }}">Administrasi Guru</a>
    <span class="separator">/</span>
    <span class="current">Detail</span>
@endsection

@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-md">
    <div>
        <a href="{{ route('admin.teacher-administrations.index') }}" class="btn btn-sm btn-outline mb-2" style="padding: 2px 8px; font-size:12px;">
            <i class="ri-arrow-left-line"></i> Kembali
        </a>
        <h1>Detail Administrasi Mengajar</h1>
        <p><strong>Guru:</strong> {{ $teacher->name }} | <strong>Mata Pelajaran:</strong> {{ $subject->name }}</p>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h3 class="m-0">Status Kelengkapan</h3>
            </div>
            <div class="card-body">
                @if(count($missingDocs) === 0)
                    <div class="alert alert-success m-0">
                        <div class="d-flex align-center gap-sm mb-2">
                            <i class="ri-checkbox-circle-fill" style="font-size: 24px;"></i>
                            <strong style="font-size: 16px;">Sangat Baik (100%)</strong>
                        </div>
                        Seluruh 7 dokumen wajib Kurikulum Merdeka telah diunggah oleh guru.
                    </div>
                @else
                    <div class="alert alert-warning m-0">
                        <strong>Dokumen Wajib yang Belum Diunggah:</strong>
                        <ul class="mb-0 mt-2 pl-3">
                            @foreach($missingDocs as $missing)
                                <li>{{ $missing }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h3 class="m-0">Daftar Dokumen Terunggah</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Jenis</th>
                                <th>Judul Dokumen</th>
                                <th>Keterangan</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($administrations as $admin)
                                <tr>
                                    <td><span class="badge badge-info">{{ $admin->type }}</span></td>
                                    <td>
                                        <strong>{{ $admin->title }}</strong>
                                        <div class="text-xs text-muted">{{ $admin->created_at->format('d M Y, H:i') }}</div>
                                    </td>
                                    <td>{{ $admin->description ?? '-' }}</td>
                                    <td class="text-right">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="previewDocument('{{ asset('storage/' . $admin->file_path) }}', '{{ addslashes($admin->title) }}')">
                                            <i class="ri-eye-line"></i> Lihat
                                        </button>
                                        <a href="{{ asset('storage/' . $admin->file_path) }}" target="_blank" class="btn btn-sm btn-outline">
                                            <i class="ri-download-2-line"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Guru belum mengunggah dokumen apapun untuk mata pelajaran ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
        
        if (url.toLowerCase().endsWith('.pdf')) {
            document.getElementById('previewNotSupported').style.display = 'none';
        } else {
            document.getElementById('previewNotSupported').style.display = 'block';
        }
        
        openModal('previewModal');
    }
</script>
@endsection
