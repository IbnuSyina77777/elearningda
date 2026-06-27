@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <span class="current">Dashboard</span>
@endsection

@section('content')
<div class="page-header">
    <h1>Overview</h1>
    <p>Ringkasan statistik dan aktivitas pembayaran terbaru.</p>
</div>

<div class="dashboard-grid">
    {{-- Left Column: Stats & Chart --}}
    <div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-icon blue"><i class="ri-group-line"></i></div>
                <div class="stat-card-info">
                    <div class="stat-card-label">Total Siswa (Keseluruhan)</div>
                    <div class="stat-card-value">{{ number_format($totalStudents, 0, ',', '.') }}</div>
                    <div style="font-size: 0.75rem; margin-top: 4px; color: var(--text-secondary); display: flex; gap: 8px;">
                        @foreach(['X', 'XI', 'XII'] as $lvl)
                            <span>Kls {{ $lvl }}: <strong>{{ $studentsPerLevel[$lvl] ?? 0 }}</strong></span>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-card-icon red"><i class="ri-wallet-3-line"></i></div>
                <div class="stat-card-info">
                    <div class="stat-card-label">Total Tagihan</div>
                    <div class="stat-card-value">Rp {{ number_format($totalBillsAmount / 1000000, 1, ',', '.') }}jt</div>
                    <div style="font-size: 0.75rem; margin-top: 4px; color: var(--text-secondary); display: flex; gap: 8px;">
                        @foreach(['X', 'XI', 'XII'] as $lvl)
                            <span>Kls {{ $lvl }}: <strong>{{ isset($billsPerLevel[$lvl]) ? number_format($billsPerLevel[$lvl]->total_amount / 1000000, 1, ',', '.') . 'jt' : '0' }}</strong></span>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-icon green"><i class="ri-safe-2-line"></i></div>
                <div class="stat-card-info">
                    <div class="stat-card-label">Dana Masuk</div>
                    <div class="stat-card-value">Rp {{ number_format($totalPaidAmount / 1000000, 1, ',', '.') }}jt</div>
                    <div style="font-size: 0.75rem; margin-top: 4px; color: var(--text-secondary); display: flex; gap: 8px; flex-wrap: wrap;">
                        @foreach(['X', 'XI', 'XII'] as $lvl)
                            <span>Kls {{ $lvl }}: <strong>{{ isset($billsPerLevel[$lvl]) ? number_format($billsPerLevel[$lvl]->total_paid / 1000000, 1, ',', '.') . 'jt' : '0' }}</strong></span>
                        @endforeach
                    </div>
                    @if($totalBillsAmount > 0)
                        <div class="stat-card-change up" style="margin-top: 4px;">
                            <i class="ri-arrow-up-line"></i> {{ round(($totalPaidAmount / $totalBillsAmount) * 100, 1) }}% collected
                        </div>
                    @endif
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-icon yellow"><i class="ri-alarm-warning-line"></i></div>
                <div class="stat-card-info">
                    <div class="stat-card-label">Jatuh Tempo</div>
                    <div class="stat-card-value">{{ $overdueCount }}</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;"><i class="ri-graduation-cap-line"></i></div>
                <div class="stat-card-info">
                    <div class="stat-card-label">Total Alumni</div>
                    <div class="stat-card-value">{{ number_format($totalAlumni, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-icon" style="background: rgba(249, 115, 22, 0.1); color: #f97316;"><i class="ri-money-dollar-circle-line"></i></div>
                <div class="stat-card-info">
                    <div class="stat-card-label">Tagihan Alumni</div>
                    <div class="stat-card-value">Rp {{ number_format($totalAlumniBillsAmount / 1000000, 1, ',', '.') }}jt</div>
                    <div style="font-size: 0.75rem; margin-top: 4px; color: var(--text-secondary);">
                        Dana Masuk: <strong>Rp {{ number_format($totalAlumniPaidAmount / 1000000, 1, ',', '.') }}jt</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h3>Pendapatan 6 Bulan Terakhir</h3>
                <button class="btn btn-sm btn-outline"><i class="ri-download-line"></i> Report</button>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="revenueChart" 
                            data-labels="{{ json_encode($chartData['labels']) }}" 
                            data-values="{{ json_encode($chartData['values']) }}">
                    </canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column: Recent Transactions --}}
    <div>
        <div class="card">
            <div class="card-header">
                <h3>Transaksi Terbaru</h3>
                <a href="{{ route('admin.transactions.index') }}" class="btn btn-sm btn-secondary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-container" style="border:none;box-shadow:none;border-radius:0;">
                    <table class="table">
                        <tbody>
                            @forelse($recentTransactions as $trx)
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <div class="user-avatar">{{ strtoupper(substr($trx->bill->student->name ?? '?', 0, 1)) }}</div>
                                            <div class="user-info">
                                                <strong>{{ $trx->bill->student->name ?? 'Unknown' }}</strong>
                                                <span>{{ $trx->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <div style="font-weight:700;">{{ $trx->formatted_amount }}</div>
                                        <span class="badge badge-{{ $trx->status_color }} badge-dot">{{ $trx->status_label }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">Belum ada transaksi</td>
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
