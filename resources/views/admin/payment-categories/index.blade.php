@extends('layouts.app')

@section('title', 'Kategori Pembayaran')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <span class="current">Kategori Pembayaran</span>
@endsection

@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-md">
    <div>
        <h1>Kategori Pembayaran</h1>
        <p>Kelola jenis tagihan seperti SPP, Uang Gedung, PTS, dll.</p>
    </div>
    <a href="{{ route('admin.payment-categories.create') }}" class="btn btn-primary">
        <i class="ri-add-line"></i> Tambah Kategori
    </a>
</div>

<div class="card mb-4">
    <div class="card-header">
        <form action="{{ route('admin.payment-categories.index') }}" method="GET" class="filter-bar m-0">
            <div class="search-input" style="width: 300px;">
                <i class="ri-search-line search-icon"></i>
                <input type="text" name="search" class="form-control" placeholder="Cari kategori atau kode..." value="{{ request('search') }}">
            </div>
            @if(request('search'))
                <a href="{{ route('admin.payment-categories.index') }}" class="btn btn-outline">Reset</a>
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
                        <th>Nama Kategori</th>
                        <th>Kode</th>
                        <th>Tahun Ajaran</th>
                        <th>Nominal Default</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>
                                <strong>{{ $category->name }}</strong> 
                                @if($category->semester)
                                    <span class="text-muted">(Semester {{ $category->semester }})</span>
                                @endif
                            </td>
                            <td><span class="badge badge-primary">{{ $category->code }}</span></td>
                            <td>
                                @if($category->academicYear)
                                    <span class="badge badge-info">{{ $category->academicYear->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td style="font-weight:600;">Rp {{ number_format($category->default_amount, 0, ',', '.') }}</td>
                            <td><span class="text-muted">{{ Str::limit($category->description, 50) ?: '-' }}</span></td>
                            <td class="text-center">
                                <div class="btn-group justify-center">
                                    <a href="{{ route('admin.payment-categories.edit', $category->id) }}" class="btn btn-sm btn-secondary" data-tooltip="Edit">
                                        <i class="ri-edit-line"></i>
                                    </a>
                                    <form action="{{ route('admin.payment-categories.destroy', $category->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-secondary text-danger" data-confirm="Hapus kategori pembayaran ini?" data-tooltip="Hapus">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada kategori pembayaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($categories->hasPages())
        <div class="card-footer" style="background:#fff;">
            {{ $categories->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
@endsection
