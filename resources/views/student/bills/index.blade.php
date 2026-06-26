@extends('layouts.app')

@section('title', 'Tagihan Saya')
@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <span class="current">Tagihan Saya</span>
@endsection

@section('content')
<div class="page-header">
    <h1>Tagihan Saya</h1>
    <p>Daftar seluruh tagihan aktif dan riwayat lunas Anda.</p>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kategori Tagihan</th>
                        <th>Nominal Total</th>
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
                                <strong>{{ $bill->paymentCategory->name ?? '-' }}</strong>
                                @if($bill->paymentCategory && $bill->paymentCategory->semester)
                                    <div class="text-sm text-muted">Semester {{ $bill->paymentCategory->semester }}</div>
                                @endif
                                <div class="text-sm text-muted">{{ $bill->academicYear->name ?? '-' }}</div>
                            </td>
                            <td style="font-weight:600;">{{ $bill->formatted_amount }}</td>
                            <td style="font-weight:600; color: {{ $bill->amount - $bill->total_paid > 0 ? 'var(--primary-600)' : 'var(--success-500)' }};">
                                {{ $bill->formatted_remaining }}
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
                                <a href="{{ route('student.bills.show', $bill->id) }}" class="btn btn-sm btn-secondary" data-tooltip="Lihat Detail">
                                    <i class="ri-eye-line"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Tidak ada tagihan.</td>
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
