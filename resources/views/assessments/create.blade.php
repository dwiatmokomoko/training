@extends('layouts.app')

@section('title', 'Tambah Penilaian')
@section('page-title', 'Tambah Penilaian')
@section('page-subtitle', 'Input penilaian pegawai berdasarkan kriteria SAW')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-check me-2"></i>
                    Form Penilaian Pegawai
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('assessments.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="employee_id" class="form-label">Pegawai <span class="text-danger">*</span></label>
                                <select class="form-select @error('employee_id') is-invalid @enderror" 
                                        id="employee_id" name="employee_id" required>
                                    <option value="">Pilih Pegawai</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" 
                                                {{ old('employee_id', request('employee_id')) == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->nip }} - {{ $employee->name }} ({{ $employee->position->name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="assessment_date" class="form-label">Tanggal Penilaian <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('assessment_date') is-invalid @enderror" 
                                       id="assessment_date" name="assessment_date" 
                                       value="{{ old('assessment_date', date('Y-m-d')) }}" required>
                                @error('assessment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <h6 class="mb-3">
                                <i class="fas fa-star me-2"></i>
                                Kriteria Penilaian
                            </h6>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Berikan nilai untuk setiap kriteria dengan skala 1-5 (1 = Sangat Kurang, 5 = Sangat Baik)
                            </div>
                        </div>
                    </div>
                    
                    @foreach($criteria as $criterion)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <h6 class="mb-1">{{ $criterion->name }}</h6>
                                    <small class="text-muted">Bobot: {{ number_format($criterion->weight * 100, 1) }}%</small>
                                    @if($criterion->description)
                                        <p class="small text-muted mt-1">{{ $criterion->description }}</p>
                                    @endif
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        @for($i = 1; $i <= 5; $i++)
                                        <div class="col">
                                            <div class="form-check text-center">
                                                <input class="form-check-input @error('scores.' . $criterion->id) is-invalid @enderror" 
                                                       type="radio" 
                                                       name="scores[{{ $criterion->id }}]" 
                                                       id="score_{{ $criterion->id }}_{{ $i }}" 
                                                       value="{{ $i }}"
                                                       {{ old('scores.' . $criterion->id) == $i ? 'checked' : '' }}>
                                                <label class="form-check-label" for="score_{{ $criterion->id }}_{{ $i }}">
                                                    <div class="score-option">
                                                        <div class="score-number">{{ $i }}</div>
                                                        <div class="score-stars">
                                                            @for($j = 1; $j <= $i; $j++)
                                                                <i class="fas fa-star text-warning"></i>
                                                            @endfor
                                                            @for($j = $i + 1; $j <= 5; $j++)
                                                                <i class="far fa-star text-muted"></i>
                                                            @endfor
                                                        </div>
                                                        <small class="score-label">
                                                            @if($i == 1) Sangat Kurang
                                                            @elseif($i == 2) Kurang
                                                            @elseif($i == 3) Cukup
                                                            @elseif($i == 4) Baik
                                                            @else Sangat Baik
                                                            @endif
                                                        </small>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        @endfor
                                    </div>
                                    @error('scores.' . $criterion->id)
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="notes" class="form-label">Catatan Penilaian</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3" 
                                          placeholder="Tambahkan catatan atau komentar mengenai penilaian ini...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Simpan Penilaian
                        </button>
                        <a href="{{ route('assessments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.score-option {
    padding: 10px;
    border-radius: 8px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.score-option:hover {
    background-color: rgba(34, 139, 34, 0.1);
}

.form-check-input:checked + .form-check-label .score-option {
    background-color: var(--ma-green);
    color: white;
}

.form-check-input:checked + .form-check-label .score-option .score-stars i {
    color: var(--ma-yellow) !important;
}

.score-number {
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 5px;
}

.score-stars {
    margin-bottom: 5px;
}

.score-label {
    font-size: 0.8rem;
    font-weight: 500;
}
</style>
@endsection