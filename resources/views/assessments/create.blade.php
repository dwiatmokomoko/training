@extends('layouts.app')

@section('title', 'Tambah Penilaian')
@section('page-title', 'Tambah Penilaian')
@section('page-subtitle', 'Input penilaian pegawai berdasarkan kriteria SAW')

@section('content')
@php
    $scoreOptions = [
        1 => ['range' => '<= 60', 'label' => 'Sangat Kurang'],
        2 => ['range' => '61-70', 'label' => 'Kurang'],
        3 => ['range' => '71-80', 'label' => 'Cukup'],
        4 => ['range' => '81-90', 'label' => 'Baik'],
        5 => ['range' => '91-100', 'label' => 'Sangat Baik'],
    ];
@endphp

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
                                <i class="fas fa-table-list me-2"></i>
                                Kriteria Penilaian
                            </h6>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Pilih kategori nilai capaian kinerja. Sistem tetap mengonversi pilihan menjadi skala 1-5 untuk perhitungan SAW.
                            </div>
                        </div>
                    </div>
                    
                    @foreach($criteria as $criterion)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <h6 class="mb-1">{{ $criterion->code }} - {{ $criterion->name }}</h6>
                                    <small class="text-muted">
                                        Bobot: {{ number_format($criterion->weight * 100, 1) }}% · {{ ucfirst($criterion->type) }}
                                    </small>
                                    @if($criterion->description)
                                        <p class="small text-muted mt-1">{{ $criterion->description }}</p>
                                    @endif
                                </div>
                                <div class="col-md-8">
                                    <div class="score-option-head">
                                        <span>Nilai Capaian</span>
                                        <span>Kategori</span>
                                    </div>
                                    <div class="score-option-list">
                                        @foreach($scoreOptions as $i => $option)
                                            <div class="form-check">
                                                <input class="form-check-input @error('scores.' . $criterion->id) is-invalid @enderror" 
                                                       type="radio" 
                                                       name="scores[{{ $criterion->id }}]" 
                                                       id="score_{{ $criterion->id }}_{{ $i }}" 
                                                       value="{{ $i }}"
                                                       {{ old('scores.' . $criterion->id) == $i ? 'checked' : '' }}>
                                                <label class="form-check-label" for="score_{{ $criterion->id }}_{{ $i }}">
                                                    <div class="score-option">
                                                        <div class="score-range">{{ $option['range'] }}</div>
                                                        <div class="score-category">
                                                            <span>{{ $option['label'] }}</span>
                                                            <small>Skor {{ $i }}</small>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
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
.score-option-head {
    display: grid;
    grid-template-columns: 160px minmax(0, 1fr);
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border: 1px solid var(--line);
    border-bottom: 0;
    border-radius: 8px 8px 0 0;
    background: #f8faf9;
    color: var(--text-muted);
    font-size: 0.78rem;
    font-weight: 800;
    text-transform: uppercase;
}

.score-option-list {
    display: grid;
    border: 1px solid var(--line);
    border-radius: 0 0 8px 8px;
    overflow: hidden;
}

.score-option-list .form-check {
    position: relative;
    min-height: 0;
    padding: 0;
    margin: 0;
}

.score-option-list .form-check:not(:last-child) {
    border-bottom: 1px solid var(--line);
}

.score-option-list .form-check-input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.score-option-list .form-check-label {
    display: block;
    margin: 0;
}

.score-option {
    display: grid;
    grid-template-columns: 160px minmax(0, 1fr);
    gap: 0.75rem;
    align-items: center;
    padding: 0.9rem 1rem;
    background: #ffffff;
    transition: background-color 0.2s ease, box-shadow 0.2s ease;
    cursor: pointer;
}

.score-option:hover {
    background-color: #f6fbf8;
}

.form-check-input:checked + .form-check-label .score-option {
    background-color: #effaf3;
    box-shadow: inset 4px 0 0 var(--ma-green);
}

.score-range {
    color: var(--text-main);
    font-weight: 850;
}

.score-category {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
}

.score-category span {
    color: var(--text-main);
    font-weight: 750;
}

.score-category small {
    padding: 0.25rem 0.5rem;
    border-radius: 999px;
    background: var(--ma-light-yellow);
    color: #6f4d00;
    font-size: 0.8rem;
    font-weight: 800;
    white-space: nowrap;
}

.form-check-input:checked + .form-check-label .score-category small {
    background: var(--ma-green);
    color: #ffffff;
}

@media (max-width: 575.98px) {
    .score-option-head {
        display: none;
    }

    .score-option {
        grid-template-columns: 1fr;
    }

    .score-category {
        align-items: flex-start;
        flex-direction: column;
    }
}
</style>
@endsection
