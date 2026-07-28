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
                <button type="button" class="tab-btn" data-target="#tab-aplikasi" style="background: none; border: none; border-bottom: 2px solid transparent; padding: 8px 16px; font-weight: 600; color: var(--text-muted); cursor: pointer;">Aplikasi & Tema</button>
                <button type="button" class="tab-btn" data-target="#tab-landing" style="background: none; border: none; border-bottom: 2px solid transparent; padding: 8px 16px; font-weight: 600; color: var(--text-muted); cursor: pointer;">Landing Page</button>
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
                            <div class="d-flex align-center gap-sm mb-2">
                                <input type="color" name="theme_color" id="themeColorPicker" class="form-control" style="width: 60px; padding: 4px; height: 42px;" value="{{ $settings['theme_color'] ?? '#dc2626' }}">
                                <input type="text" id="themeColorHex" class="form-control" style="width: 100px; font-family: monospace;" value="{{ $settings['theme_color'] ?? '#dc2626' }}" maxlength="7">
                            </div>

                            {{-- Preset Color Swatches --}}
                            <label class="form-label" style="font-size: .85rem; margin-top: 8px;">Pilih Preset Warna:</label>
                            <div id="colorPresets" style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 12px;">
                                <button type="button" class="color-preset" data-color="#dc2626" title="Merah (Default)" style="width:36px;height:36px;border-radius:8px;border:2px solid transparent;background:#dc2626;cursor:pointer;transition:all .2s;"></button>
                                <button type="button" class="color-preset" data-color="#2563eb" title="Biru" style="width:36px;height:36px;border-radius:8px;border:2px solid transparent;background:#2563eb;cursor:pointer;transition:all .2s;"></button>
                                <button type="button" class="color-preset" data-color="#16a34a" title="Hijau" style="width:36px;height:36px;border-radius:8px;border:2px solid transparent;background:#16a34a;cursor:pointer;transition:all .2s;"></button>
                                <button type="button" class="color-preset" data-color="#9333ea" title="Ungu" style="width:36px;height:36px;border-radius:8px;border:2px solid transparent;background:#9333ea;cursor:pointer;transition:all .2s;"></button>
                                <button type="button" class="color-preset" data-color="#ea580c" title="Oranye" style="width:36px;height:36px;border-radius:8px;border:2px solid transparent;background:#ea580c;cursor:pointer;transition:all .2s;"></button>
                                <button type="button" class="color-preset" data-color="#0891b2" title="Cyan" style="width:36px;height:36px;border-radius:8px;border:2px solid transparent;background:#0891b2;cursor:pointer;transition:all .2s;"></button>
                                <button type="button" class="color-preset" data-color="#db2777" title="Pink" style="width:36px;height:36px;border-radius:8px;border:2px solid transparent;background:#db2777;cursor:pointer;transition:all .2s;"></button>
                                <button type="button" class="color-preset" data-color="#ca8a04" title="Kuning Emas" style="width:36px;height:36px;border-radius:8px;border:2px solid transparent;background:#ca8a04;cursor:pointer;transition:all .2s;"></button>
                                <button type="button" class="color-preset" data-color="#475569" title="Abu-abu Gelap" style="width:36px;height:36px;border-radius:8px;border:2px solid transparent;background:#475569;cursor:pointer;transition:all .2s;"></button>
                                <button type="button" class="color-preset" data-color="#0f766e" title="Teal" style="width:36px;height:36px;border-radius:8px;border:2px solid transparent;background:#0f766e;cursor:pointer;transition:all .2s;"></button>
                            </div>

                            {{-- Live Preview Strip --}}
                            <label class="form-label" style="font-size: .85rem;">Preview Palet Warna:</label>
                            <div id="palettePreview" style="display: flex; border-radius: 8px; overflow: hidden; height: 40px; box-shadow: 0 2px 8px rgba(0,0,0,.1);">
                                {{-- JS fills this --}}
                            </div>
                            <div id="paletteLabels" style="display: flex; margin-top: 4px;">
                            </div>

                            {{-- Sidebar Preview --}}
                            <label class="form-label" style="font-size: .85rem; margin-top: 12px;">Preview Sidebar:</label>
                            <div id="sidebarPreview" style="height: 60px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.1); display: flex; align-items: center; padding: 0 16px; color: #fff; font-weight: 600; font-size: .9rem;">
                                <span>🎓 {{ setting('app_name', 'EduPay') }}</span>
                            </div>

                            <div class="text-sm text-muted mt-2">Warna ini akan mengubah sidebar, tombol, link, landing page, dan seluruh elemen aksen pada aplikasi.</div>
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

                {{-- Login Background Image --}}
                <div class="form-group mb-3 mt-3">
                    <label class="form-label">Background Halaman Login</label>
                    <div class="d-flex align-center gap-md">
                        @if(isset($settings['login_bg_image']) && $settings['login_bg_image'])
                            <div style="width: 160px; height: 90px; border-radius: 8px; border: 1px solid var(--border-color); overflow: hidden; display: flex; align-items: center; justify-content: center; background: #f9f9f9;">
                                <img src="{{ asset('storage/' . $settings['login_bg_image']) }}" alt="Login BG" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        @else
                            <div style="width: 160px; height: 90px; border-radius: 8px; border: 1px dashed var(--border-color); display: flex; align-items: center; justify-content: center; background: #f9f9f9; color: var(--text-muted);">
                                <i class="ri-login-box-line" style="font-size: 28px;"></i>
                            </div>
                        @endif
                        <div style="flex: 1;">
                            <input type="file" name="login_bg_image" class="form-control" accept="image/png, image/jpeg, image/jpg, image/webp">
                            <div class="text-sm text-muted mt-1">Gambar latar belakang halaman login. Disarankan resolusi minimal 1920x1080px. Biarkan kosong untuk menggunakan warna gradien tema.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Landing Page -->
            <div id="tab-landing" class="tab-pane" style="display: none;">
                <div class="form-group mb-3">
                    <label class="form-label">Judul Hero (Hero Title)</label>
                    <input type="text" name="landing_title" class="form-control" value="{{ $settings['landing_title'] ?? 'Selamat Datang di SMK Bisa Hebat' }}" placeholder="Contoh: Selamat Datang di SMK Negeri 1">
                </div>
                
                <div class="form-group mb-3">
                    <label class="form-label">Tagline (Hero Subtitle)</label>
                    <input type="text" name="landing_tagline" class="form-control" value="{{ $settings['landing_tagline'] ?? 'Mewujudkan Generasi Kompeten, Inovatif, dan Siap Kerja di Era Digital.' }}" placeholder="Slogan singkat di halaman depan">
                </div>

                <hr style="margin: 24px 0; border: none; border-top: 1px solid var(--border-color);">
                <h3 class="mb-3" style="font-size: 1.1rem;"><i class="ri-image-2-line"></i> Gambar Landing Page</h3>

                {{-- Hero Background Image --}}
                <div class="form-group mb-3">
                    <label class="form-label">Gambar Background Hero</label>
                    <div class="d-flex align-center gap-md">
                        @if(isset($settings['landing_hero_image']) && $settings['landing_hero_image'])
                            <div style="width: 160px; height: 90px; border-radius: 8px; border: 1px solid var(--border-color); overflow: hidden; display: flex; align-items: center; justify-content: center; background: #f9f9f9;">
                                <img src="{{ asset('storage/' . $settings['landing_hero_image']) }}" alt="Hero BG" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        @else
                            <div style="width: 160px; height: 90px; border-radius: 8px; border: 1px dashed var(--border-color); display: flex; align-items: center; justify-content: center; background: #f9f9f9; color: var(--text-muted);">
                                <i class="ri-landscape-line" style="font-size: 28px;"></i>
                            </div>
                        @endif
                        <div style="flex: 1;">
                            <input type="file" name="landing_hero_image" class="form-control" accept="image/png, image/jpeg, image/jpg, image/webp">
                            <div class="text-sm text-muted mt-1">Gambar latar belakang bagian hero. Disarankan resolusi minimal 1920x600px (landscape). Biarkan kosong jika tidak ingin mengubah.</div>
                        </div>
                    </div>
                </div>

                {{-- About Section Image --}}
                <div class="form-group mb-3">
                    <label class="form-label">Gambar Tentang Sekolah</label>
                    <div class="d-flex align-center gap-md">
                        @if(isset($settings['landing_about_image']) && $settings['landing_about_image'])
                            <div style="width: 160px; height: 90px; border-radius: 8px; border: 1px solid var(--border-color); overflow: hidden; display: flex; align-items: center; justify-content: center; background: #f9f9f9;">
                                <img src="{{ asset('storage/' . $settings['landing_about_image']) }}" alt="About" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        @else
                            <div style="width: 160px; height: 90px; border-radius: 8px; border: 1px dashed var(--border-color); display: flex; align-items: center; justify-content: center; background: #f9f9f9; color: var(--text-muted);">
                                <i class="ri-building-line" style="font-size: 28px;"></i>
                            </div>
                        @endif
                        <div style="flex: 1;">
                            <input type="file" name="landing_about_image" class="form-control" accept="image/png, image/jpeg, image/jpg, image/webp">
                            <div class="text-sm text-muted mt-1">Foto gedung sekolah atau kegiatan belajar. Muncul di bagian "Tentang Sekolah". Disarankan rasio 16:9.</div>
                        </div>
                    </div>
                </div>

                <hr style="margin: 24px 0; border: none; border-top: 1px solid var(--border-color);">
                <h3 class="mb-3" style="font-size: 1.1rem;"><i class="ri-text"></i> Konten Teks</h3>

                <div class="form-group mb-3">
                    <label class="form-label">Tentang Sekolah</label>
                    <textarea name="landing_about" class="form-control" rows="4" placeholder="Deskripsi singkat mengenai sekolah Anda">{{ $settings['landing_about'] ?? 'Kami adalah institusi pendidikan kejuruan yang berkomitmen mencetak lulusan unggul dan berkarakter, didukung oleh fasilitas modern dan tenaga pengajar profesional.' }}</textarea>
                </div>
                
                <div class="form-group mb-3">
                    <label class="form-label">Program Keahlian (Jurusan)</label>
                    <textarea name="landing_majors" class="form-control" rows="3" placeholder="Pisahkan dengan koma. Contoh: Rekayasa Perangkat Lunak, Teknik Komputer Jaringan, Akuntansi">{{ $settings['landing_majors'] ?? 'Rekayasa Perangkat Lunak, Teknik Komputer Jaringan, Akuntansi dan Keuangan Lembaga, Otomatisasi Tata Kelola Perkantoran' }}</textarea>
                    <div class="text-sm text-muted mt-1">Gunakan tanda koma (,) untuk memisahkan setiap nama jurusan. Ini akan ditampilkan di bagian Program Keahlian.</div>
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

        // ===== Theme Color Preview System =====
        const picker = document.getElementById('themeColorPicker');
        const hexInput = document.getElementById('themeColorHex');
        const previewStrip = document.getElementById('palettePreview');
        const previewLabels = document.getElementById('paletteLabels');
        const sidebarPreview = document.getElementById('sidebarPreview');
        const presets = document.querySelectorAll('.color-preset');

        function hexToHSL(hex) {
            let r = parseInt(hex.slice(1,3),16)/255, g = parseInt(hex.slice(3,5),16)/255, b = parseInt(hex.slice(5,7),16)/255;
            let max = Math.max(r,g,b), min = Math.min(r,g,b), d = max-min, h, s, l = (max+min)/2;
            if (d === 0) { h = 0; s = 0; } else {
                s = l > 0.5 ? d/(2-max-min) : d/(max+min);
                switch(max) {
                    case r: h = ((g-b)/d + (g<b?6:0))*60; break;
                    case g: h = ((b-r)/d + 2)*60; break;
                    case b: h = ((r-g)/d + 4)*60; break;
                }
            }
            return { h: Math.round(h), s: Math.round(s*100), l: Math.round(l*100) };
        }

        function generatePalette(hex) {
            const hsl = hexToHSL(hex);
            const H = hsl.h, S = hsl.s;
            return {
                '50':  `hsl(${H},${Math.min(S+10,100)}%,97%)`,
                '100': `hsl(${H},${Math.min(S+8,100)}%,93%)`,
                '200': `hsl(${H},${Math.min(S+5,100)}%,86%)`,
                '300': `hsl(${H},${S}%,76%)`,
                '400': `hsl(${H},${S}%,62%)`,
                '500': `hsl(${H},${S}%,52%)`,
                '600': `hsl(${H},${S}%,45%)`,
                '700': `hsl(${H},${Math.max(S-5,0)}%,38%)`,
                '800': `hsl(${H},${Math.max(S-8,0)}%,30%)`,
                '900': `hsl(${H},${Math.max(S-10,0)}%,24%)`,
                '950': `hsl(${H},${Math.max(S-12,0)}%,14%)`
            };
        }

        function updatePreview(hex) {
            const palette = generatePalette(hex);
            const shades = Object.keys(palette);

            // Palette strip
            previewStrip.innerHTML = shades.map(s =>
                `<div style="flex:1;background:${palette[s]};position:relative;" title="${s}"></div>`
            ).join('');

            // Labels
            previewLabels.innerHTML = shades.map(s =>
                `<div style="flex:1;text-align:center;font-size:.65rem;color:var(--text-secondary);">${s}</div>`
            ).join('');

            // Sidebar preview gradient
            sidebarPreview.style.background = `linear-gradient(135deg, ${palette['800']} 0%, ${palette['900']} 50%, ${palette['950']} 100%)`;

            // Update active preset indicator
            presets.forEach(p => {
                if (p.dataset.color.toLowerCase() === hex.toLowerCase()) {
                    p.style.border = '2px solid #333';
                    p.style.transform = 'scale(1.15)';
                    p.style.boxShadow = '0 2px 8px rgba(0,0,0,.25)';
                } else {
                    p.style.border = '2px solid transparent';
                    p.style.transform = 'scale(1)';
                    p.style.boxShadow = 'none';
                }
            });
        }

        // Sync color picker <-> hex input
        if (picker && hexInput) {
            picker.addEventListener('input', function() {
                hexInput.value = this.value;
                updatePreview(this.value);
            });

            hexInput.addEventListener('input', function() {
                if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                    picker.value = this.value;
                    updatePreview(this.value);
                }
            });

            // Preset click
            presets.forEach(btn => {
                btn.addEventListener('click', function() {
                    const color = this.dataset.color;
                    picker.value = color;
                    hexInput.value = color;
                    updatePreview(color);
                });
            });

            // Initial render
            updatePreview(picker.value);
        }
    });
</script>
@endsection
