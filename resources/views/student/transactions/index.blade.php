@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')
@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <span class="current">Riwayat Pembayaran</span>
@endsection

@section('content')
<div class="page-header">
    <h1>Riwayat Pembayaran</h1>
    <p>Daftar seluruh transaksi pembayaran yang pernah Anda lakukan.</p>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Tanggal & Waktu</th>
                        <th>Tagihan (Kategori)</th>
                        <th>Nominal Pembayaran</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                        <tr>
                            <td><strong>#{{ $trx->order_id }}</strong></td>
                            <td>
                                <strong>{{ $trx->created_at->format('d M Y') }}</strong>
                                <div class="text-sm text-muted">{{ $trx->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td>
                                <strong>{{ $trx->bill->paymentCategory->name ?? '-' }}</strong>
                                <div class="text-sm text-muted">{{ $trx->bill->academicYear->name ?? '-' }}</div>
                            </td>
                            <td style="font-weight:700;">{{ $trx->formatted_amount }}</td>
                            <td>
                                <div class="d-flex flex-column align-center gap-xs">
                                    <span class="badge badge-{{ $trx->status_color }}">{{ $trx->status_label }}</span>
                                    @if($trx->status === 'success')
                                        <a href="{{ route('student.transactions.receipt', $trx->id) }}" class="btn btn-sm btn-outline mt-1" style="font-size: 11px; padding: 2px 6px;">
                                            <i class="ri-printer-line"></i> Kwitansi
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat pembayaran.</td>
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
