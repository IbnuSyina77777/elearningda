@extends('layouts.app')

@section('title', 'Data Alumni')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <span class="current">Data Alumni</span>
@endsection

@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-md">
    <div>
        <h1>Data Alumni</h1>
        <p>Daftar siswa yang telah lulus (berstatus alumni).</p>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-between align-center flex-wrap gap-sm">
        <form action="{{ route('admin.alumni.index') }}" method="GET" class="d-flex align-center gap-sm" style="flex:1; max-width:600px;">
            <div class="input-icon" style="flex:1;">
                <i class="ri-search-line"></i>
                <input type="text" name="search" class="form-control" placeholder="Cari NIS, NISN, atau Nama Alumni..." value="{{ request('search') }}">
            </div>
            
            <select name="graduation_year" class="form-control form-select" onchange="this.form.submit()" style="width:200px;">
                <option value="">Semua Tahun Lulus</option>
                @foreach($graduationYears as $year)
                    <option value="{{ $year }}" {{ request('graduation_year') == $year ? 'selected' : '' }}>
                        Lulusan {{ $year }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Alumni</th>
                        <th>NIS/NISN</th>
                        <th>Riwayat Kelas</th>
                        <th>Lulusan Tahun</th>
                        <th>Kontak</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alumnis as $alumni)
                        <tr>
                            <td>
                                <div class="user-card">
                                    @if($alumni->photo)
                                        <div class="user-avatar" style="background-image:url('{{ asset('storage/' . $alumni->photo) }}');background-size:cover;background-position:center;"></div>
                                    @else
                                        <div class="user-avatar">{{ strtoupper(substr($alumni->user->name, 0, 1)) }}</div>
                                    @endif
                                    <div class="user-info">
                                        <strong>{{ $alumni->user->name }}</strong>
                                        <span>{{ $alumni->user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight:600;">{{ $alumni->nisn }}</div>
                                <span class="text-muted text-sm">{{ $alumni->nis }}</span>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $alumni->graduated_from ?? '-' }}</span>
                                <div class="text-muted text-sm mt-1">Kelas Terakhir</div>
                            </td>
                            <td>
                                <span class="badge badge-success">Lulusan {{ $alumni->graduation_year ?? '?' }}</span>
                            </td>
                            <td>
                                <div>{{ $alumni->phone ?? '-' }}</div>
                                <span class="text-muted text-sm">{{ $alumni->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group justify-center">
                                    <a href="{{ route('admin.students.edit', $alumni->id) }}" class="btn btn-sm btn-secondary" data-tooltip="Edit">
                                        <i class="ri-edit-line"></i>
                                    </a>
                                    <form action="{{ route('admin.students.destroy', $alumni->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-secondary text-danger" data-confirm="Hapus data alumni ini beserta seluruh riwayat nilai dan transaksinya secara permanen?" data-tooltip="Hapus Permanen">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data alumni.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($alumnis->hasPages())
    <div class="card-footer">
        {{ $alumnis->links() }}
    </div>
    @endif
</div>
@endsection
