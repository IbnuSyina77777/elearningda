@extends('layouts.app')

@section('title', 'Kelola Pengumuman')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <span class="current">Pengumuman</span>
@endsection

@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-md">
    <div>
        <h1>Pengumuman</h1>
        <p>Siarkan informasi penting ke beranda Guru dan/atau Siswa.</p>
    </div>
    <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary">
        <i class="ri-add-line"></i> Buat Pengumuman Baru
    </a>
</div>

<div class="card mb-4">
    <div class="card-header">
        <form action="{{ route('admin.announcements.index') }}" method="GET" class="filter-bar m-0">
            <div class="search-input" style="width: 300px;">
                <i class="ri-search-line search-icon"></i>
                <input type="text" name="search" class="form-control" placeholder="Cari Judul..." value="{{ request('search') }}">
            </div>
            
            <div style="min-width: 200px;">
                <select name="target_audience" class="form-control form-select" onchange="this.form.submit()">
                    <option value="">Semua Target</option>
                    <option value="all" {{ request('target_audience') == 'all' ? 'selected' : '' }}>Semua Pengguna</option>
                    <option value="teachers" {{ request('target_audience') == 'teachers' ? 'selected' : '' }}>Hanya Guru</option>
                    <option value="students" {{ request('target_audience') == 'students' ? 'selected' : '' }}>Hanya Siswa</option>
                </select>
            </div>
            
            @if(request('search') || request('target_audience'))
                <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline">Reset</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Judul Pengumuman</th>
                        <th>Target</th>
                        <th>Status</th>
                        <th>Tgl Dibuat</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($announcements as $ann)
                        <tr>
                            <td>
                                <strong>{{ $ann->title }}</strong>
                                <div class="text-sm text-muted">{{ Str::limit(strip_tags($ann->content), 80) }}</div>
                            </td>
                            <td>
                                @if($ann->target_audience === 'all')
                                    <span class="badge badge-info">Semua</span>
                                @elseif($ann->target_audience === 'teachers')
                                    <span class="badge badge-primary">Guru</span>
                                @else
                                    <span class="badge badge-warning">Siswa</span>
                                @endif
                            </td>
                            <td>
                                @if($ann->is_active)
                                    <span class="badge badge-success badge-dot">Aktif</span>
                                @else
                                    <span class="badge badge-secondary badge-dot">Draf</span>
                                @endif
                            </td>
                            <td>{{ $ann->created_at->format('d M Y, H:i') }}</td>
                            <td class="text-center">
                                <div class="btn-group justify-center">
                                    <a href="{{ route('admin.announcements.edit', $ann->id) }}" class="btn btn-sm btn-secondary" data-tooltip="Edit">
                                        <i class="ri-edit-line"></i>
                                    </a>
                                    <form action="{{ route('admin.announcements.destroy', $ann->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-secondary text-danger" data-confirm="Hapus pengumuman ini secara permanen?" data-tooltip="Hapus">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada data pengumuman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($announcements->hasPages())
        <div class="card-footer" style="background:#fff;">
            {{ $announcements->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
@endsection
