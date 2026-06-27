@extends('layouts.app')

@section('title', 'Data Tagihan')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <span class="current">Data Tagihan</span>
@endsection

@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-md">
    <div>
        <h1>Data Tagihan</h1>
        <p>Pantau status seluruh tagihan siswa di sekolah.</p>
    </div>
    <div class="d-flex gap-sm">
        <form action="{{ route('admin.bills.destroy-all') }}" method="POST" style="display:inline-block;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline" style="border-color:var(--danger-500); color:var(--danger-500);" data-confirm="PERINGATAN KERAS! Aksi ini akan MENGHAPUS SEMUA TAGIHAN dan SEMUA RIWAYAT TRANSAKSI dari database. Anda yakin ingin melanjutkan?">
                <i class="ri-delete-bin-line"></i> Kosongkan Semua
            </button>
        </form>
        <form action="{{ route('admin.bills.auto-sync') }}" method="POST" style="display:inline-block;">
            @csrf
            <button type="submit" class="btn btn-secondary" data-confirm="Sistem akan otomatis mengecek dan membuatkan tagihan untuk SELURUH SISWA berdasarkan Kategori Pembayaran yang aktif saat ini. Lanjutkan?">
                <i class="ri-refresh-line"></i> Auto-Sync
            </button>
        </form>
        <a href="{{ route('admin.bills.create') }}" class="btn btn-primary">
            <i class="ri-add-line"></i> Generate Manual
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form action="{{ route('admin.bills.index') }}" method="GET" class="filter-bar m-0">
            <div class="search-input">
                <i class="ri-search-line search-icon"></i>
                <input type="text" name="search" class="form-control" placeholder="Cari Nama Siswa / NIS..." value="{{ request('search') }}">
            </div>
            
            <div style="min-width: 200px;">
                <select name="status" class="form-control form-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Belum Dibayar</option>
                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Dicicil</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            
            <div style="min-width: 200px;">
                <select name="classroom_id" class="form-control form-select" onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    @foreach($classrooms as $room)
                        <option value="{{ $room->id }}" {{ request('classroom_id') == $room->id ? 'selected' : '' }}>
                            {{ $room->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="min-width: 200px;">
                <select name="payment_category_id" class="form-control form-select" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('payment_category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }} {{ $cat->semester ? '(Smt '.$cat->semester.')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            @if(request('status') || request('classroom_id') || request('payment_category_id') || request('search'))
                <a href="{{ route('admin.bills.index') }}" class="btn btn-outline">Reset</a>
            @endif
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>Kategori Tagihan</th>
                        <th>Nominal</th>
                        <th>Kekurangan</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $bill)
                        <tr>
                            <td>
                                <strong>{{ $bill->student->name ?? '-' }}</strong>
                                <div class="text-sm text-muted">
                                    @if($bill->student && $bill->student->status === 'alumni')
                                        <span class="text-success">Lulus (Alumni)</span>
                                    @else
                                        {{ $bill->student->classroom->name ?? '-' }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                <strong>{{ $bill->paymentCategory->name ?? '-' }}</strong>
                                @if($bill->paymentCategory && $bill->paymentCategory->semester)
                                    <span class="badge badge-info" style="font-size:0.7rem; margin-left:4px;">Semester {{ $bill->paymentCategory->semester }}</span>
                                @endif
                                <div class="text-sm text-muted">{{ $bill->academicYear->name ?? '-' }}</div>
                            </td>
                            <td style="font-weight:600;">Rp {{ number_format($bill->amount, 0, ',', '.') }}</td>
                            <td style="font-weight:600; color:var(--primary-600);">
                                Rp {{ number_format($bill->amount - $bill->total_paid, 0, ',', '.') }}
                            </td>
                            <td>
                                {{ $bill->due_date->format('d M Y') }}
                                @if($bill->due_date < now() && $bill->status !== 'paid')
                                    <span class="badge badge-danger badge-dot ml-1" title="Lewat Jatuh Tempo"></span>
                                @endif
                            </td>
                            <td>
                                @if($bill->status === 'paid')
                                    <span class="badge badge-success">Lunas</span>
                                @elseif($bill->status === 'partial')
                                    <span class="badge badge-warning">Dicicil ({{ $bill->payment_percentage }}%)</span>
                                @else
                                    <span class="badge badge-danger">Belum Bayar</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group justify-center">
                                    <a href="{{ route('admin.bills.show', $bill->id) }}" class="btn btn-sm btn-secondary" data-tooltip="Detail & Bayar">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                    <form action="{{ route('admin.bills.destroy', $bill->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-secondary text-danger" data-confirm="Hapus tagihan ini beserta seluruh riwayat pembayarannya?" data-tooltip="Hapus">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Data tagihan tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($bills->hasPages())
        <div class="card-footer" style="background:#fff;">
            {{ $bills->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
@endsection
