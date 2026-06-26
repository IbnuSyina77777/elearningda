@extends('layouts.app')

@section('title', 'Laporan Keuangan')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <span class="current">Laporan Keuangan</span>
@endsection

@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-md">
    <div>
        <h1>Laporan Keuangan</h1>
        <p>Lihat dan cetak laporan penerimaan dana sekolah.</p>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.reports.index') }}" method="GET" class="d-flex flex-wrap gap-md align-center">
            <div class="form-group m-0" style="flex:1; min-width:150px;">
                <label class="form-label text-sm">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            
            <div class="form-group m-0" style="flex:1; min-width:150px;">
                <label class="form-label text-sm">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            
            <div class="form-group m-0" style="flex:1; min-width:200px;">
                <label class="form-label text-sm">Kategori Tagihan</label>
                <select name="category_id" class="form-control form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group m-0" style="flex:1; min-width:200px;">
                <label class="form-label text-sm">Cari Nama / NIS</label>
                <input type="text" name="search" class="form-control" placeholder="Opsional..." value="{{ request('search') }}">
            </div>
            
            <div class="form-group m-0 d-flex gap-sm" style="margin-top: 24px !important;">
                <button type="submit" class="btn btn-primary"><i class="ri-filter-3-line"></i> Filter</button>
                @if(request('start_date') || request('end_date') || request('category_id') || request('search'))
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline">Reset</a>
                @endif
                <a href="{{ route('admin.reports.exportPdf', request()->all()) }}" class="btn btn-success">
                    <i class="ri-file-pdf-2-line"></i> Cetak PDF
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-between align-center">
        <h3 class="m-0">Hasil Laporan</h3>
        <div style="font-weight: 600; font-size: 1.1rem;">
            Total: Rp {{ number_format($totalAmount, 0, ',', '.') }}
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Bayar</th>
                        <th>Nama Siswa / Kelas</th>
                        <th>Kategori Tagihan</th>
                        <th>Metode</th>
                        <th class="text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $index => $trx)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $trx->paid_at ? \Carbon\Carbon::parse($trx->paid_at)->format('d M Y, H:i') : '-' }}</td>
                            <td>
                                <strong>{{ $trx->bill->student->name ?? '-' }}</strong><br>
                                <span class="text-muted text-sm">{{ $trx->bill->student->classroom->name ?? '-' }}</span>
                            </td>
                            <td>{{ $trx->bill->paymentCategory->name ?? '-' }}</td>
                            <td>{{ strtoupper($trx->payment_type) }}</td>
                            <td class="text-right font-weight-bold">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Tidak ada transaksi ditemukan pada kriteria ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
