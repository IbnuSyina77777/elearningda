@extends('layouts.app')

@section('title', 'Kenaikan Kelas / Kelulusan')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <a href="{{ route('admin.classrooms.index') }}">Data Kelas</a>
    <span class="separator">/</span>
    <span class="current">Kenaikan Kelas & Kelulusan</span>
@endsection

@section('content')
<div class="page-header">
    <h1>Kenaikan Kelas & Kelulusan Massal</h1>
    <p>Pindahkan seluruh siswa di kelas ini ke kelas baru, atau tandai mereka sebagai lulus.</p>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h2 class="card-title">Kelas Asal: <span class="text-primary">{{ $classroom->name }}</span></h2>
        <p class="text-muted m-0">Terdapat <strong>{{ $classroom->students_count }}</strong> siswa aktif di kelas ini.</p>
    </div>
    <form action="{{ route('admin.classrooms.promote', $classroom->id) }}" method="POST">
        @csrf
        <div class="card-body">
            @if($classroom->students_count == 0)
                <div class="alert alert-warning">
                    <i class="ri-error-warning-line"></i> Tidak ada siswa di kelas ini. Anda tidak dapat melakukan proses ini.
                </div>
            @else
                <div class="form-group">
                    <label class="form-label d-block mb-3">Pilih Tindakan <span class="required">*</span></label>
                    
                    <div class="custom-control custom-radio mb-3 p-3" style="border: 1px solid var(--gray-200); border-radius: var(--radius-md);">
                        <input type="radio" id="action_promote" name="action" class="custom-control-input" value="promote" {{ old('action', 'promote') == 'promote' ? 'checked' : '' }} onchange="toggleTargetClass(true)">
                        <label class="custom-control-label font-weight-bold" for="action_promote">Naik Kelas / Pindah Kelas</label>
                        <p class="text-muted text-sm mt-1 ml-4 mb-0">Pindahkan seluruh siswa ke kelas yang baru.</p>
                        
                        <div id="target_class_container" class="mt-3 ml-4" style="display: {{ old('action', 'promote') == 'promote' ? 'block' : 'none' }};">
                            <label class="form-label" for="target_classroom_id">Pilih Kelas Tujuan</label>
                            <select id="target_classroom_id" name="target_classroom_id" class="form-control form-select @error('target_classroom_id') is-invalid @enderror">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($classrooms as $room)
                                    <option value="{{ $room->id }}" {{ old('target_classroom_id') == $room->id ? 'selected' : '' }}>
                                        {{ $room->name }} (Tingkat {{ $room->level }}) - {{ $room->major->code }}
                                    </option>
                                @endforeach
                            </select>
                            @error('target_classroom_id')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="custom-control custom-radio p-3" style="border: 1px solid var(--danger-200); border-radius: var(--radius-md); background: rgba(239, 68, 68, 0.05);">
                        <input type="radio" id="action_graduate" name="action" class="custom-control-input" value="graduate" {{ old('action') == 'graduate' ? 'checked' : '' }} onchange="toggleTargetClass(false)">
                        <label class="custom-control-label font-weight-bold text-danger" for="action_graduate">Tandai Lulus (Alumni)</label>
                        <p class="text-muted text-sm mt-1 ml-4 mb-0">Keluarkan seluruh siswa dari kelas ini dan ubah status mereka menjadi alumni. Akun dan riwayat tagihan mereka akan tetap aman.</p>
                    </div>
                    @error('action')<span class="form-error d-block mt-2">{{ $message }}</span>@enderror
                </div>
            @endif
        </div>
        
        <div class="card-footer d-flex justify-between">
            <a href="{{ route('admin.classrooms.index') }}" class="btn btn-secondary">Batal</a>
            @if($classroom->students_count > 0)
                <button type="submit" class="btn btn-primary" data-confirm="Anda yakin ingin memproses seluruh data siswa di kelas ini? Aksi ini akan mengubah data kelas seluruh siswa sekaligus!"><i class="ri-flight-takeoff-line"></i> Proses Sekarang</button>
            @endif
        </div>
    </form>
</div>

<script>
    function toggleTargetClass(show) {
        document.getElementById('target_class_container').style.display = show ? 'block' : 'none';
    }
</script>
@endsection
