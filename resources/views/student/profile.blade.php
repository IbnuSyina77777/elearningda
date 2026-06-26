@extends('layouts.app')

@section('title', 'Profil Saya')
@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <span class="current">Profil Saya</span>
@endsection

@section('content')
<div class="page-header">
    <h1>Profil Saya</h1>
    <p>Informasi biodata Anda yang terdaftar di sekolah.</p>
</div>

<div class="dashboard-grid">
    {{-- Left Column: Photo & Identity Card --}}
    <div>
        <div class="card mb-3">
            <div class="card-body" style="display: flex; flex-direction: column; align-items: center; padding: 2rem;">
                {{-- Photo --}}
                @if($student->photo)
                    <div style="width: 160px; height: 160px; border-radius: 50%; overflow: hidden; border: 4px solid var(--primary-600); box-shadow: 0 8px 25px rgba(220, 38, 38, 0.25); margin-bottom: 1.25rem;">
                        <img src="{{ asset('storage/' . $student->photo) }}" alt="Foto {{ $student->user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                @else
                    <div style="width: 160px; height: 160px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-600), var(--primary-800)); display: flex; align-items: center; justify-content: center; font-size: 4rem; font-weight: 700; color: #fff; box-shadow: 0 8px 25px rgba(220, 38, 38, 0.25); margin-bottom: 1.25rem;">
                        {{ strtoupper(substr($student->user->name ?? 'S', 0, 1)) }}
                    </div>
                @endif

                <h2 style="margin: 0; font-size: 1.4rem; text-align: center;">{{ $student->user->name ?? '-' }}</h2>
                <p style="color: var(--text-secondary); margin: 0.25rem 0 0.75rem;">Siswa Aktif</p>

                <div class="d-flex gap-sm flex-wrap" style="justify-content: center;">
                    <span class="badge badge-primary" style="font-size: 0.85rem; padding: 6px 14px;">{{ $student->classroom->name ?? '-' }}</span>
                    <span class="badge badge-secondary" style="font-size: 0.85rem; padding: 6px 14px;">{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                </div>
            </div>
        </div>

        {{-- Address Card --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="ri-map-pin-line" style="color: var(--primary-600); margin-right: 6px;"></i> Alamat Lengkap</h3>
            </div>
            <div class="card-body">
                @if($student->address)
                    <p style="line-height: 1.7; margin: 0; color: var(--text-primary);">{{ $student->address }}</p>
                @else
                    <p class="text-muted" style="margin: 0; font-style: italic;">Alamat belum dilengkapi. Hubungi Tata Usaha untuk memperbarui data Anda.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Right Column: Detailed Information --}}
    <div>
        <div class="card mb-3">
            <div class="card-header">
                <h3><i class="ri-id-card-line" style="color: var(--primary-600); margin-right: 6px;"></i> Data Akademik</h3>
            </div>
            <div class="card-body">
                <table class="table-details">
                    <tr>
                        <th style="width: 160px;">NISN</th>
                        <td><strong>{{ $student->nisn ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <th>NIS</th>
                        <td><strong>{{ $student->nis }}</strong></td>
                    </tr>
                    <tr>
                        <th>Kelas</th>
                        <td>
                            <span class="badge badge-primary">{{ $student->classroom->name ?? '-' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Jenis Kelamin</th>
                        <td>{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    </tr>
                    @if($student->birth_date)
                    <tr>
                        <th>Tanggal Lahir</th>
                        <td>{{ $student->birth_date->format('d F Y') }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h3><i class="ri-contacts-book-line" style="color: var(--primary-600); margin-right: 6px;"></i> Kontak & Orang Tua</h3>
            </div>
            <div class="card-body">
                <table class="table-details">
                    <tr>
                        <th style="width: 160px;">Email</th>
                        <td>{{ $student->user->email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>No. HP Siswa</th>
                        <td>{{ $student->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Nama Orang Tua</th>
                        <td>{{ $student->parent_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>No. HP Orang Tua</th>
                        <td>{{ $student->parent_phone ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="ri-time-line" style="color: var(--primary-600); margin-right: 6px;"></i> Informasi Sistem</h3>
            </div>
            <div class="card-body">
                <table class="table-details">
                    <tr>
                        <th style="width: 160px;">Terdaftar Sejak</th>
                        <td>{{ $student->created_at->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <th>Terakhir Diupdate</th>
                        <td>{{ $student->updated_at->format('d F Y, H:i') }} WIB</td>
                    </tr>
                </table>
            </div>
            <div class="card-footer text-center" style="background:#fff;">
                <p class="text-sm text-muted m-0">
                    <i class="ri-information-line"></i>
                    Jika terdapat kesalahan data, mohon hubungi bagian <strong>Tata Usaha</strong> sekolah untuk perbaikan.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
