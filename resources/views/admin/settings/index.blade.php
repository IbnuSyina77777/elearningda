@extends('layouts.app')

@section('title', 'Pengaturan Aplikasi')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <span class="current">Pengaturan Aplikasi</span>
@endsection

@section('content')
<div class="page-header d-flex justify-between align-center flex-wrap gap-md">
    <div>
        <h1>Pengaturan Aplikasi</h1>
        <p>Sesuaikan identitas sekolah, nama aplikasi, dan warna tema.</p>
    </div>
</div>

<div class="card" style="max-width: 900px;">
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="card-body">
            <!-- Tabs Navigation -->
            <div class="tabs mb-4" style="border-bottom: 1px solid var(--border-color); display: flex; gap: 16px;">
                <button type="button" class="tab-btn active" data-target="#tab-sekolah" style="background: none; border: none; border-bottom: 2px solid var(--primary-600); padding: 8px 16px; font-weight: 600; color: var(--primary-600); cursor: pointer;">Identitas Sekolah</button>
                <button type="button" class="tab-btn" data-target="#tab-aplikasi" style="background: none; border: none; border-bottom: 2px solid transparent; padding: 8px 16px; font-weight: 600; color: var(--text-muted); cursor: pointer;">Identitas Aplikasi & Tema</button>
            </div>

            <!-- Tab: Sekolah -->
            <div id="tab-sekolah" class="tab-pane active" style="display: block;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Nama Sekolah</label>
                            <input type="text" name="school_name" class="form-control" value="{{ $settings['school_name'] ?? 'SMK Bisa Hebat' }}" placeholder="Contoh: SMK Negeri 1 Jakarta">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Email Sekolah</label>
                            <input type="email" name="school_email" class="form-control" value="{{ $settings['school_email'] ?? 'info@smkbisa.sch.id' }}">
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="school_address" class="form-control" rows="3">{{ $settings['school_address'] ?? 'Jl. Pendidikan No. 123, Kota Pelajar' }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" name="school_phone" class="form-control" value="{{ $settings['school_phone'] ?? '(021) 1234567' }}">
                        </div>
                    </div>
                </div>

                <hr style="margin: 24px 0; border: none; border-top: 1px solid var(--border-color);">
                <h3 class="mb-3" style="font-size: 1.1rem;">Identitas Kepala Sekolah (Untuk Laporan PDF)</h3>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Nama Kepala Sekolah</label>
                            <input type="text" name="principal_name" class="form-control" value="{{ $settings['principal_name'] ?? 'Budi Santoso, M.Pd' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">NIP Kepala Sekolah</label>
                            <input type="text" name="principal_nip" class="form-control" value="{{ $settings['principal_nip'] ?? '19800101 200501 1 001' }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Aplikasi -->
            <div id="tab-aplikasi" class="tab-pane" style="display: none;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Nama Aplikasi</label>
                            <input type="text" name="app_name" class="form-control" value="{{ $settings['app_name'] ?? 'EduPay' }}" placeholder="Contoh: EduPay">
                            <div class="text-sm text-muted mt-1">Nama ini akan muncul di sidebar dan judul browser.</div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Teks Copyright (Footer)</label>
                            <input type="text" name="app_copyright" class="form-control" value="{{ $settings['app_copyright'] ?? '© 2026 EduPay - All rights reserved.' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Warna Utama Tema (Primary Color)</label>
                            <div class="d-flex align-center gap-sm">
                                <input type="color" name="theme_color" class="form-control" style="width: 60px; padding: 4px; height: 42px;" value="{{ $settings['theme_color'] ?? '#dc2626' }}">
                                <span class="text-sm text-muted">Akan mengubah aksen warna tombol & menu.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3 mt-3">
                    <label class="form-label">Logo Sekolah / Aplikasi</label>
                    <div class="d-flex align-center gap-md">
                        @if(isset($settings['app_logo']) && $settings['app_logo'])
                            <div style="width: 80px; height: 80px; border-radius: 8px; border: 1px solid var(--border-color); overflow: hidden; display: flex; align-items: center; justify-content: center; background: #f9f9f9;">
                                <img src="{{ asset('storage/' . $settings['app_logo']) }}" alt="Logo" style="max-width: 100%; max-height: 100%;">
                            </div>
                        @else
                            <div style="width: 80px; height: 80px; border-radius: 8px; border: 1px dashed var(--border-color); display: flex; align-items: center; justify-content: center; background: #f9f9f9; color: var(--text-muted);">
                                <i class="ri-image-add-line" style="font-size: 24px;"></i>
                            </div>
                        @endif
                        <div style="flex: 1;">
                            <input type="file" name="app_logo" class="form-control" accept="image/png, image/jpeg, image/jpg">
                            <div class="text-sm text-muted mt-1">Biarkan kosong jika tidak ingin mengubah logo. (Disarankan PNG transparan, rasio 1:1)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex justify-between">
            <button type="submit" class="btn btn-primary" style="min-width: 150px;">
                <i class="ri-save-line"></i> Simpan Pengaturan
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.tab-btn');
        const panes = document.querySelectorAll('.tab-pane');

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Reset tabs
                tabs.forEach(t => {
                    t.classList.remove('active');
                    t.style.borderBottomColor = 'transparent';
                    t.style.color = 'var(--text-muted)';
                });
                
                // Set active tab
                this.classList.add('active');
                this.style.borderBottomColor = 'var(--primary-600)';
                this.style.color = 'var(--primary-600)';

                // Hide all panes
                panes.forEach(p => p.style.display = 'none');
                
                // Show target pane
                const target = document.querySelector(this.dataset.target);
                if (target) {
                    target.style.display = 'block';
                }
            });
        });
    });
</script>
@endsection
