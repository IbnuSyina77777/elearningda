@extends('layouts.app')

@section('title', 'Generate Tagihan Massal')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"><i class="ri-home-4-line"></i></a>
    <span class="separator">/</span>
    <a href="{{ route('admin.bills.index') }}">Data Tagihan</a>
    <span class="separator">/</span>
    <span class="current">Generate Massal</span>
@endsection

@section('content')
<div class="page-header">
    <h1>Generate Tagihan</h1>
    <p>Buat tagihan baru untuk satu kelas secara massal.</p>
</div>

<div class="card" style="max-width: 700px;">
    <form action="{{ route('admin.bills.store') }}" method="POST">
        @csrf
        {{-- Hidden input for target type since we only support classroom for now --}}
        <input type="hidden" name="target_type" value="classroom">
        
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="payment_category_id">Kategori Pembayaran <span class="required">*</span></label>
                    <select id="payment_category_id" name="payment_category_id" class="form-control form-select @error('payment_category_id') is-invalid @enderror" required onchange="updateDefaultAmount()">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" data-amount="{{ $category->amount }}" {{ old('payment_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }} (Default: Rp {{ number_format($category->amount, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    @error('payment_category_id')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="academic_year_id">Tahun Ajaran <span class="required">*</span></label>
                    <select id="academic_year_id" name="academic_year_id" class="form-control form-select @error('academic_year_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ (old('academic_year_id') == $year->id || $year->is_active) ? 'selected' : '' }}>
                                {{ $year->name }} {{ $year->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('academic_year_id')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="amount">Nominal Tagihan <span class="required">*</span></label>
                    <input type="number" id="amount" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required min="0">
                    <span class="form-hint">Nominal akan otomatis terisi saat memilih kategori, tapi bisa Anda ubah.</span>
                    @error('amount')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="due_date">Tanggal Jatuh Tempo <span class="required">*</span></label>
                    <input type="date" id="due_date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date') }}" required>
                    @error('due_date')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="classroom_id">Pilih Kelas Target <span class="required">*</span></label>
                <select id="classroom_id" name="classroom_id" class="form-control form-select @error('classroom_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($classrooms as $room)
                        <option value="{{ $room->id }}" {{ old('classroom_id') == $room->id ? 'selected' : '' }}>
                            {{ $room->name }} ({{ $room->major->code }})
                        </option>
                    @endforeach
                </select>
                <span class="form-hint">Sistem akan membuat tagihan ini untuk SELURUH siswa yang ada di kelas ini secara bersamaan.</span>
                @error('classroom_id')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>
        
        <div class="card-footer d-flex justify-between">
            <a href="{{ route('admin.bills.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary" data-confirm="Anda yakin ingin men-generate tagihan ini untuk semua siswa di kelas yang dipilih?"><i class="ri-flashlight-line"></i> Generate Massal</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function updateDefaultAmount() {
        const select = document.getElementById('payment_category_id');
        const selectedOption = select.options[select.selectedIndex];
        const amountInput = document.getElementById('amount');
        
        if (selectedOption && selectedOption.hasAttribute('data-amount')) {
            amountInput.value = selectedOption.getAttribute('data-amount');
        }
    }
</script>
@endpush
@endsection
