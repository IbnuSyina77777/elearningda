@extends('layouts.app')

@section('title', 'Detail Tagihan')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <a href="{{ route('admin.bills.index') }}">Data Tagihan</a>
    <span class="separator">/</span>
    <span class="current">Detail</span>
@endsection

@section('content')
<div class="page-header d-flex justify-between align-center">
    <div>
        <h1>Detail Tagihan</h1>
        <p>Informasi tagihan dan riwayat pembayaran.</p>
    </div>
    <a href="{{ route('admin.bills.index') }}" class="btn btn-secondary">
        <i class="ri-arrow-left-line"></i> Kembali
    </a>
</div>

<div class="dashboard-grid">
    {{-- Left: Bill Information --}}
    <div>
        <div class="card mb-3">
            <div class="card-header">
                <h3>Informasi Tagihan</h3>
            </div>
            <div class="card-body">
                <table style="width:100%; border-spacing:0 12px;">
                    <tr>
                        <td style="color:var(--text-secondary);width:40%;">Nama Siswa</td>
                        <td><strong>{{ $bill->student->name ?? 'N/A' }}</strong> ({{ $bill->student->nis ?? '-' }})</td>
                    </tr>
                    <tr>
                        <td style="color:var(--text-secondary);">Kelas</td>
                        <td>{{ $bill->student->classroom->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><hr style="border:0;border-top:1px solid var(--border-color);margin:4px 0;"></td>
                    </tr>
                    <tr>
                        <td style="color:var(--text-secondary);">Kategori</td>
                        <td>{{ $bill->paymentCategory->name ?? 'N/A' }} ({{ $bill->academicYear->name ?? 'N/A' }})</td>
                    </tr>
                    <tr>
                        <td style="color:var(--text-secondary);">Jatuh Tempo</td>
                        <td>{{ $bill->due_date->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td style="color:var(--text-secondary);">Status</td>
                        <td>
                            @if($bill->status === 'paid')
                                <span class="badge badge-success">Lunas</span>
                            @elseif($bill->status === 'partial')
                                <span class="badge badge-warning">Dicicil</span>
                            @else
                                <span class="badge badge-danger">Belum Bayar</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-body" style="background:var(--primary-50);">
                <div class="d-flex justify-between align-center mb-1">
                    <span style="color:var(--text-secondary);">Total Tagihan</span>
                    <strong>Rp {{ number_format($bill->amount, 0, ',', '.') }}</strong>
                </div>
                <div class="d-flex justify-between align-center mb-1">
                    <span style="color:var(--text-secondary);">Sudah Dibayar</span>
                    <strong style="color:var(--success-color);">Rp {{ number_format($bill->total_paid, 0, ',', '.') }}</strong>
                </div>
                <hr style="border:0;border-top:1px dashed rgba(0,0,0,.1);margin:12px 0;">
                <div class="d-flex justify-between align-center">
                    <span style="font-weight:600;">Sisa Tagihan</span>
                    <strong style="font-size:1.5rem;color:var(--primary-600);">
                        Rp {{ number_format($bill->amount - $bill->total_paid, 0, ',', '.') }}
                    </strong>
                </div>
            </div>
        </div>
    </div>

    {{-- Right: Transactions --}}
    <div>
        <div class="card">
            <div class="card-header d-flex justify-between align-center">
                <h3>Riwayat Pembayaran</h3>
                @if($bill->status !== 'paid')
                    <button class="btn btn-sm btn-primary" onclick="openModal('addPaymentModal')">
                        <i class="ri-add-line"></i> Input Pembayaran
                    </button>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Ref</th>
                                <th>Metode</th>
                                <th class="text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bill->transactions as $trx)
                                <tr>
                                    <td>{{ $trx->payment_date->format('d M Y') }}</td>
                                    <td><span class="badge badge-secondary">{{ $trx->reference_number }}</span></td>
                                    <td>{{ strtoupper($trx->payment_method) }}</td>
                                    <td class="text-right" style="font-weight:600;color:var(--success-color);">
                                        + Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada riwayat pembayaran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal for adding payment --}}
@if($bill->status !== 'paid')
<div class="modal-overlay" id="addPaymentModal">
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header">
            <h3>Input Pembayaran Manual</h3>
            <button class="modal-close" onclick="closeModal('addPaymentModal')"><i class="ri-close-line"></i></button>
        </div>
        <form action="{{ route('admin.transactions.store') }}" method="POST">
            @csrf
            <input type="hidden" name="bill_id" value="{{ $bill->id }}">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Sisa Tagihan</label>
                    <input type="text" class="form-control" value="Rp {{ number_format($bill->amount - $bill->total_paid, 0, ',', '.') }}" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label" for="amount">Nominal Bayar <span class="required">*</span></label>
                    <input type="number" id="amount" name="amount" class="form-control" required min="1" max="{{ $bill->amount - $bill->total_paid }}" value="{{ $bill->amount - $bill->total_paid }}">
                    <span class="form-hint">Biarkan default untuk melunasi seluruh sisa tagihan.</span>
                </div>
                <div class="form-group">
                    <label class="form-label" for="payment_method">Metode Pembayaran <span class="required">*</span></label>
                    <select id="payment_method" name="payment_method" class="form-control form-select" required>
                        <option value="cash">Tunai (Cash)</option>
                        <option value="transfer">Transfer Bank (Manual)</option>
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label" for="notes">Catatan (Opsional)</label>
                    <textarea id="notes" name="notes" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer d-flex justify-between">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addPaymentModal')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="ri-check-line"></i> Simpan Pembayaran</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
