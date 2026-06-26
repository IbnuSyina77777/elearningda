@extends('layouts.app')

@section('title', 'Tahun Ajaran')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <span class="current">Tahun Ajaran</span>
@endsection

@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-md">
    <div>
        <h1>Tahun Ajaran</h1>
        <p>Kelola daftar tahun ajaran (hanya satu yang aktif dalam satu waktu).</p>
    </div>
    <a href="{{ route('admin.academic-years.create') }}" class="btn btn-primary">
        <i class="ri-add-line"></i> Tambah Tahun Ajaran
    </a>
</div>

<div class="card" style="max-width: 800px;">
    <div class="card-body p-0">
        <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tahun Ajaran</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($years as $year)
                        <tr>
                            <td><strong>{{ $year->full_label }}</strong></td>
                            <td>
                                @if($year->is_active)
                                    <span class="badge badge-success badge-dot">Aktif Sekarang</span>
                                @else
                                    <span class="badge badge-secondary badge-dot">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group justify-center">
                                    <a href="{{ route('admin.academic-years.edit', $year->id) }}" class="btn btn-sm btn-secondary" data-tooltip="Edit">
                                        <i class="ri-edit-line"></i>
                                    </a>
                                    @if(!$year->is_active)
                                        <form action="{{ route('admin.academic-years.destroy', $year->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-secondary text-danger" data-confirm="Hapus tahun ajaran ini?" data-tooltip="Hapus">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">Belum ada data tahun ajaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
