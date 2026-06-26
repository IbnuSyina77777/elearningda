@extends('layouts.app')

@section('title', 'Semua Transaksi')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <span class="current">Riwayat Transaksi</span>
@endsection

@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-md">
    <div>
        <h1>Riwayat Transaksi</h1>
        <p>Seluruh data riwayat pembayaran masuk.</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form action="{{ route('admin.transactions.index') }}" method="GET" class="filter-bar m-0">
            <div class="search-input">
                <i class="ri-search-line search-icon"></i>
                <input type="text" name="search" class="form-control" placeholder="Cari Ref / Nama Siswa..." value="{{ request('search') }}">
            </div>
            
            <div style="min-width: 200px;">
                <select name="status" class="form-control form-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Sukses (Berhasil)</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal / Kadaluarsa</option>
                </select>
            </div>
            
            @if(request('search') || request('status'))
                <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline">Reset</a>
            @endif
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Ref Number</th>
                        <th>Siswa</th>
                        <th>Pembayaran</th>
                        <th class="text-right">Nominal</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                        <tr>
                            <td>{{ $trx->payment_date ? $trx->payment_date->format('d M Y, H:i') : $trx->created_at->format('d M Y') }}</td>
                            <td><span class="badge badge-secondary" style="font-family:monospace;">{{ $trx->reference_number }}</span></td>
                            <td>
                                <strong>{{ $trx->bill->student->name ?? '-' }}</strong>
                                <div class="text-sm text-muted">{{ $trx->bill->student->classroom->name ?? '-' }}</div>
                            </td>
                            <td>
                                <strong>{{ $trx->bill->paymentCategory->name ?? '-' }}</strong>
                                <div class="text-sm text-muted">{{ strtoupper($trx->payment_method) }}</div>
                            </td>
                            <td class="text-right" style="font-weight:600;">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @if($trx->status === 'success')
                                    <div class="d-flex flex-column align-center gap-xs">
                                        <span class="badge badge-success badge-dot">Sukses</span>
                                        <a href="{{ route('admin.transactions.receipt', $trx->id) }}" class="btn btn-sm btn-outline mt-1" style="font-size: 11px; padding: 2px 6px;">
                                            <i class="ri-printer-line"></i> Kwitansi
                                        </a>
                                    </div>
                                @elseif($trx->status === 'pending')
                                    <span class="badge badge-warning badge-dot">Pending</span>
                                @else
                                    <span class="badge badge-danger badge-dot">Gagal</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Data transaksi tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($transactions->hasPages())
        <div class="card-footer" style="background:#fff;">
            {{ $transactions->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
@endsection
