@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <span class="current">Dashboard</span>
@endsection

@section('content')
{{-- Greeting Card --}}
@php
    $hour = (int) now()->format('H');
    if ($hour >= 5 && $hour < 12) $greeting = 'Selamat Pagi';
    elseif ($hour >= 12 && $hour < 15) $greeting = 'Selamat Siang';
    elseif ($hour >= 15 && $hour < 18) $greeting = 'Selamat Sore';
    else $greeting = 'Selamat Malam';
    $emoji = $hour >= 5 && $hour < 18 ? '☀️' : '🌙';
@endphp
<div class="greeting-card">
    <h1>{{ $greeting }}, {{ explode(' ', auth()->user()->name)[0] }}! {{ $emoji }}</h1>
    <p>Ringkasan statistik dan aktivitas pembayaran terbaru. Hari ini {{ now()->translatedFormat('l, d F Y') }}.</p>
    <div class="greeting-actions">
        <a href="{{ route('admin.students.index') }}" class="btn"><i class="ri-user-add-line"></i> Data Siswa</a>
        <a href="{{ route('admin.bills.index') }}" class="btn"><i class="ri-bill-line"></i> Kelola Tagihan</a>
        <a href="{{ route('admin.transactions.index') }}" class="btn"><i class="ri-exchange-funds-line"></i> Transaksi</a>
        <a href="{{ route('admin.reports.index') }}" class="btn"><i class="ri-file-chart-line"></i> Laporan</a>
    </div>
</div>

{{-- Stats Grid — Full Width --}}
<div class="stats-grid">
    {{-- 1. Total Siswa (Gradient Hero Card) --}}
    <div class="stat-card stat-card-gradient">
        <div class="stat-card-icon"><i class="ri-group-line"></i></div>
        <div class="stat-card-info">
            <div class="stat-card-label">Total Siswa Aktif</div>
            <div class="stat-card-value">{{ number_format($totalStudents, 0, ',', '.') }}</div>
            <div class="stat-detail-row">
                @foreach(['X', 'XI', 'XII'] as $lvl)
                    <span class="stat-badge">Kls {{ $lvl }}: <strong>{{ $studentsPerLevel[$lvl] ?? 0 }}</strong></span>
                @endforeach
            </div>
        </div>
    </div>

    {{-- 2. Total Tagihan --}}
    <div class="stat-card">
        <div class="stat-card-icon red"><i class="ri-wallet-3-line"></i></div>
        <div class="stat-card-info">
            <div class="stat-card-label">Total Tagihan</div>
            <div class="stat-card-value">Rp {{ number_format($totalBillsAmount / 1000000, 1, ',', '.') }}jt</div>
            <div class="stat-detail-row">
                @foreach(['X', 'XI', 'XII'] as $lvl)
                    <span class="stat-badge">{{ $lvl }}: {{ isset($billsPerLevel[$lvl]) ? number_format($billsPerLevel[$lvl]->total_amount / 1000000, 1, ',', '.') . 'jt' : '0' }}</span>
                @endforeach
            </div>
        </div>
    </div>

    {{-- 3. Dana Masuk --}}
    <div class="stat-card">
        <div class="stat-card-icon green"><i class="ri-safe-2-line"></i></div>
        <div class="stat-card-info">
            <div class="stat-card-label">Dana Masuk</div>
            <div class="stat-card-value">Rp {{ number_format($totalPaidAmount / 1000000, 1, ',', '.') }}jt</div>
            <div class="stat-detail-row">
                @foreach(['X', 'XI', 'XII'] as $lvl)
                    <span class="stat-badge">{{ $lvl }}: {{ isset($billsPerLevel[$lvl]) ? number_format($billsPerLevel[$lvl]->total_paid / 1000000, 1, ',', '.') . 'jt' : '0' }}</span>
                @endforeach
            </div>
            @if($totalBillsAmount > 0)
                <div class="stat-progress">
                    <div class="stat-progress-bar" style="width: {{ round(($totalPaidAmount / $totalBillsAmount) * 100, 1) }}%"></div>
                </div>
                <div class="stat-card-change up" style="margin-top: 6px;">
                    <i class="ri-arrow-up-line"></i> {{ round(($totalPaidAmount / $totalBillsAmount) * 100, 1) }}% terkumpul
                </div>
            @endif
        </div>
    </div>

    {{-- 4. Jatuh Tempo --}}
    <div class="stat-card">
        <div class="stat-card-icon yellow"><i class="ri-alarm-warning-line"></i></div>
        <div class="stat-card-info">
            <div class="stat-card-label">Jatuh Tempo</div>
            <div class="stat-card-value">{{ $overdueCount }}</div>
            @if($overdueCount > 0)
                <div class="stat-card-change down" style="margin-top: 6px;">
                    <i class="ri-alert-line"></i> Perlu perhatian
                </div>
            @endif
        </div>
    </div>

    {{-- 5. Total Alumni --}}
    <div class="stat-card">
        <div class="stat-card-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;"><i class="ri-graduation-cap-line"></i></div>
        <div class="stat-card-info">
            <div class="stat-card-label">Total Alumni</div>
            <div class="stat-card-value">{{ number_format($totalAlumni, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- 6. Tagihan Alumni --}}
    <div class="stat-card">
        <div class="stat-card-icon" style="background: rgba(249, 115, 22, 0.1); color: #f97316;"><i class="ri-money-dollar-circle-line"></i></div>
        <div class="stat-card-info">
            <div class="stat-card-label">Tagihan Alumni</div>
            <div class="stat-card-value">Rp {{ number_format($totalAlumniBillsAmount / 1000000, 1, ',', '.') }}jt</div>
            <div style="font-size: 0.75rem; margin-top: 4px; color: var(--text-secondary);">
                Dana Masuk: <strong>Rp {{ number_format($totalAlumniPaidAmount / 1000000, 1, ',', '.') }}jt</strong>
            </div>
            @if($totalAlumniBillsAmount > 0)
                <div class="stat-progress">
                    <div class="stat-progress-bar" style="width: {{ round(($totalAlumniPaidAmount / $totalAlumniBillsAmount) * 100, 1) }}%"></div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Chart & Transactions Row --}}
<div class="dashboard-grid">
    <div>
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
