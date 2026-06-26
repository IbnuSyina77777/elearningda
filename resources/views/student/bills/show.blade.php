@extends('layouts.app')

@section('title', 'Detail Tagihan')
@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <a href="{{ route('student.bills.index') }}">Tagihan Saya</a>
    <span class="separator">/</span>
    <span class="current">Detail</span>
@endsection

@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-md">
    <div>
        <h1>Detail Tagihan</h1>
        <p>Rincian tagihan {{ $bill->paymentCategory->name ?? 'Pembayaran' }}</p>
    </div>
    <a href="{{ route('student.bills.index') }}" class="btn btn-secondary">
        <i class="ri-arrow-left-line"></i> Kembali
    </a>
</div>

<div class="dashboard-grid">
    {{-- Left Column: Bill Information --}}
    <div>
        <div class="card mb-3">
            <div class="card-header">
                <h3>Informasi Tagihan</h3>
            </div>
            <div class="card-body">
                <table class="table-details">
                    <tr>
                        <th>Kategori</th>
                        <td><strong>{{ $bill->paymentCategory->name ?? '-' }}</strong></td>
                    </tr>
                    @if($bill->paymentCategory && $bill->paymentCategory->semester)
                    <tr>
                        <th>Semester</th>
                        <td>Semester {{ $bill->paymentCategory->semester }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Tahun Ajaran</th>
                        <td>{{ $bill->academicYear->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
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
                    <tr>
                        <th>Jatuh Tempo</th>
                        <td>
                            {{ $bill->due_date->format('d M Y') }}
                            @if($bill->due_date < now() && $bill->status !== 'paid')
                                <span class="badge badge-danger badge-dot ml-1" title="Lewat Jatuh Tempo"></span>
                            @endif
                        </td>
                    </tr>
                </table>
                
                <hr style="margin: 1.5rem 0; border:none; border-top: 1px dashed var(--gray-300);">
                
                <table class="table-details">
                    <tr>
                        <th>Total Tagihan</th>
                        <td class="text-right" style="font-weight:700; font-size:1.1rem;">{{ $bill->formatted_amount }}</td>
                    </tr>
                    <tr>
                        <th>Total Dibayar</th>
                        <td class="text-right text-success" style="font-weight:600;">{{ $bill->formatted_paid }}</td>
                    </tr>
                    <tr>
                        <th>Sisa Kekurangan</th>
                        <td class="text-right text-primary" style="font-weight:700; font-size:1.2rem;">{{ $bill->formatted_remaining }}</td>
                    </tr>
                </table>

                @if($bill->status !== 'paid')
                    {{-- Midtrans Pay Button --}}
                    <div class="mt-4" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                        <h4 style="margin-bottom: 12px; font-size: 1rem;"><i class="ri-bank-card-line"></i> Bayar Online</h4>
                        
                        <div class="form-group mb-3">
                            <label class="form-label text-sm">Nominal Pembayaran</label>
                            <div class="d-flex gap-sm">
                                <input type="number" id="payAmount" class="form-control" 
                                       value="{{ (int)($bill->amount - $bill->total_paid) }}" 
                                       min="1" 
                                       max="{{ (int)($bill->amount - $bill->total_paid) }}" 
                                       style="max-width: 250px;">
                                <button type="button" id="btnPayFull" class="btn btn-sm btn-outline" 
                                        onclick="document.getElementById('payAmount').value='{{ (int)($bill->amount - $bill->total_paid) }}'">
                                    Bayar Penuh
                                </button>
                            </div>
                            <div class="text-sm text-muted mt-1">Minimal Rp 1. Anda boleh membayar sebagian (cicilan) atau langsung lunas.</div>
                        </div>

                        <button type="button" id="btnPayOnline" class="btn btn-primary" style="min-width: 200px;">
                            <i class="ri-secure-payment-line"></i> Bayar Sekarang
                        </button>

                        <div id="paymentError" class="alert alert-danger mt-2" style="display:none; padding: 8px 12px; font-size: 0.9rem;"></div>
                        <div id="paymentLoading" class="mt-2" style="display:none; font-size: 0.9rem; color: var(--text-muted);">
                            <i class="ri-loader-4-line" style="animation: spin 1s linear infinite; display: inline-block;"></i> Memproses...
                        </div>
                    </div>
                @else
                    <div class="alert alert-success mt-3" style="background: rgba(16, 185, 129, 0.1); color: var(--success-600); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: var(--radius-md); padding: 1rem;">
                        <i class="ri-checkbox-circle-fill" style="margin-right: 0.5rem; vertical-align: middle;"></i>
                        <span style="font-size: 0.9rem;">Terima kasih, tagihan ini sudah lunas sepenuhnya.</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right Column: Transaction History --}}
    <div>
        <div class="card">
            <div class="card-header">
                <h3>Riwayat Pembayaran (Cicilan)</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal & Waktu</th>
                                <th>Nominal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bill->transactions as $trx)
                                <tr>
                                    <td>
                                        <strong>{{ $trx->created_at->format('d M Y') }}</strong>
                                        <div class="text-sm text-muted">{{ $trx->created_at->format('H:i') }} WIB</div>
                                        <div class="text-sm text-muted mt-1">ID: #{{ $trx->order_id }}</div>
                                    </td>
                                    <td style="font-weight:700; color:var(--success-600);">{{ $trx->formatted_amount }}</td>
                                    <td>
                                        <span class="badge badge-{{ $trx->status_color }}">{{ $trx->status_label }}</span>
                                        @if($trx->is_payable)
                                            <button type="button" class="btn btn-sm btn-primary mt-1 btn-resume-pay" 
                                                    data-snap-token="{{ $trx->snap_token }}" style="font-size: 11px; padding: 2px 8px;">
                                                <i class="ri-refresh-line"></i> Lanjutkan Bayar
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Belum ada riwayat pembayaran untuk tagihan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if($bill->status !== 'paid')
<style>
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>

@php
    $midtransClientKey = config('midtrans.client_key');
    $midtransIsProduction = config('midtrans.is_production');
    $snapUrl = $midtransIsProduction 
        ? 'https://app.midtrans.com/snap/snap.js' 
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
@endphp

<script src="{{ $snapUrl }}" data-client-key="{{ $midtransClientKey }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnPay = document.getElementById('btnPayOnline');
        const payAmount = document.getElementById('payAmount');
        const errorDiv = document.getElementById('paymentError');
        const loadingDiv = document.getElementById('paymentLoading');

        function showError(msg) {
            errorDiv.textContent = msg;
            errorDiv.style.display = 'block';
            setTimeout(() => errorDiv.style.display = 'none', 5000);
        }

        function setLoading(state) {
            btnPay.disabled = state;
            loadingDiv.style.display = state ? 'block' : 'none';
        }

        btnPay.addEventListener('click', function() {
            const amount = parseInt(payAmount.value);
            if (!amount || amount < 1) {
                showError('Nominal pembayaran harus minimal Rp 1.');
                return;
            }

            setLoading(true);
            errorDiv.style.display = 'none';

            fetch('{{ route("student.bills.pay", $bill->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ amount: amount }),
            })
            .then(res => res.json())
            .then(data => {
                setLoading(false);
                if (data.error) {
                    showError(data.error);
                    return;
                }

                // Open Midtrans Snap popup
                window.snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        window.location.href = '{{ route("student.payment.finish") }}';
                    },
                    onPending: function(result) {
                        window.location.href = '{{ route("student.payment.finish") }}';
                    },
                    onError: function(result) {
                        showError('Pembayaran gagal. Silakan coba lagi.');
                    },
                    onClose: function() {
                        // User closed the popup without completing payment
                    }
                });
            })
            .catch(err => {
                setLoading(false);
                showError('Terjadi kesalahan jaringan. Silakan coba lagi.');
            });
        });

        // Resume payment for pending transactions
        document.querySelectorAll('.btn-resume-pay').forEach(btn => {
            btn.addEventListener('click', function() {
                const snapToken = this.dataset.snapToken;
                window.snap.pay(snapToken, {
                    onSuccess: function(result) {
                        window.location.href = '{{ route("student.payment.finish") }}';
                    },
                    onPending: function(result) {
                        window.location.reload();
                    },
                    onError: function(result) {
                        showError('Pembayaran gagal.');
                    },
                    onClose: function() {}
                });
            });
        });
    });
</script>
@endif
@endsection
