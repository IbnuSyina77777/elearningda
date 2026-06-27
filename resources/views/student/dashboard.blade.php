@extends('layouts.app')

@section('title', 'Student Dashboard')
@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <span class="current">Dashboard</span>
@endsection

@section('content')
<div class="page-header">
    <h1>Halo, {{ explode(' ', auth()->user()->name)[0] }}! 👋</h1>
    <p>Selamat datang di portal pembelajaran dan pembayaran sekolah Anda.</p>
</div>

@if($announcements->count() > 0)
<div class="card mb-4" style="border-left: 4px solid var(--primary-500); background: #f0f7ff;">
    <div class="card-body p-3">
        <h3 class="mb-2" style="font-size: 1.1rem; color: var(--primary-700);"><i class="ri-notification-3-line"></i> Pengumuman Terbaru</h3>
        <div class="d-flex flex-column gap-sm">
            @foreach($announcements as $ann)
                <div style="background: #fff; padding: 12px 16px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,.05);">
                    <div class="d-flex justify-between align-center mb-1">
                        <strong style="font-size: 1.05rem;">{{ $ann->title }}</strong>
                        <span class="text-xs text-muted">{{ $ann->created_at->diffForHumans() }}</span>
                    </div>
                    <div style="font-size: 0.95rem; color: #4b5563;">
                        {!! nl2br($ann->content) !!}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<div class="dashboard-grid">
    {{-- Left Column: Financial Overview & Active Bills --}}
    <div>
        <div class="stats-grid">
            <div class="stat-card" style="background: linear-gradient(135deg, var(--primary-600), var(--primary-800)); color: #fff; border: none;">
                <div class="stat-card-icon" style="background: rgba(255,255,255,.2); color: #fff;"><i class="ri-bill-line"></i></div>
                <div class="stat-card-info">
                    <div class="stat-card-label" style="color: rgba(255,255,255,.7);">Total Tagihan Aktif</div>
                    <div class="stat-card-value" style="color: #fff;">Rp {{ number_format($totalRemaining, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-icon green"><i class="ri-checkbox-circle-line"></i></div>
                <div class="stat-card-info">
                    <div class="stat-card-label">Sudah Dibayar</div>
                    <div class="stat-card-value">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
                    @if($totalBills > 0)
                        <div class="progress mt-1">
                            <div class="progress-bar green" style="width: {{ ($totalPaid / $totalBills) * 100 }}%"></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h3>Tagihan Berjalan</h3>
                <a href="{{ route('student.bills.index') }}" class="btn btn-sm btn-outline">Lihat Semua</a>
            </div>
            <div class="card-body">
                <div class="d-grid gap-md">
                    @forelse($activeBills as $bill)
                        <div class="bill-card">
                            <div class="bill-card-icon">
                                <i class="ri-file-list-3-line"></i>
                            </div>
                            <div class="bill-card-info">
                                <h4>{{ $bill->paymentCategory->name }}</h4>
                                <p>Jatuh tempo: {{ $bill->due_date->format('d M Y') }}</p>
                                <div class="mt-1">
                                    @if($bill->status === 'unpaid')
                                        <span class="badge badge-danger">Belum Dibayar</span>
                                    @elseif($bill->status === 'partial')
                                        <span class="badge badge-warning">Dicicil ({{ $bill->payment_percentage }}%)</span>
                                    @endif
                                </div>
                            </div>
                            <div class="bill-card-amount">
                                <div class="amount">{{ $bill->formatted_remaining }}</div>
                                <a href="{{ route('student.bills.show', $bill->id) }}" class="btn btn-sm btn-primary mt-1">Detail</a>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="ri-checkbox-circle-fill empty-state-icon text-success" style="opacity:1;"></i>
                            <h3>Hore! Tidak ada tagihan.</h3>
                            <p>Semua tagihan Anda sudah lunas terbayar.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column: Profile & History --}}
    <div>
        <div class="card mb-3">
            <div class="profile-header">
                @if($student->photo)
                    <div style="width: 64px; height: 64px; border-radius: 50%; overflow: hidden; border: 3px solid var(--primary-600); box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);">
                        <img src="{{ asset('storage/' . $student->photo) }}" alt="Foto" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                @else
                    <div class="profile-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                @endif
                <div class="profile-info">
                    <h2>{{ auth()->user()->name }}</h2>
                    <p>NIS: {{ $student->nis }} &bull; {{ $student->status === 'alumni' ? 'Lulus (Alumni)' : ($student->classroom->name ?? 'Unknown Class') }}</p>
                </div>
            </div>
            <div class="card-footer" style="background:#fff;">
                <a href="{{ route('student.profile') }}" class="btn btn-secondary w-full">Lihat Profil Lengkap</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Riwayat Terakhir</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
                    <table class="table">
                        <tbody>
                            @forelse($recentTransactions as $trx)
                                <tr>
                                    <td>
                                        <strong style="display:block;font-size:.9rem;">{{ $trx->bill->paymentCategory->name ?? 'Pembayaran' }}</strong>
                                        <span style="font-size:.75rem;color:var(--text-secondary);">{{ $trx->created_at->format('d M Y, H:i') }}</span>
                                    </td>
                                    <td class="text-right">
                                        <div style="font-weight:700;font-size:.9rem;">{{ $trx->formatted_amount }}</div>
                                        <span class="badge badge-{{ $trx->status_color }} badge-dot" style="font-size:.65rem;padding:2px 6px;">{{ $trx->status_label }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">Belum ada riwayat pembayaran</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
