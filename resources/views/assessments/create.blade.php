@extends('layouts.app')

@section('title', 'Tambah Penilaian')
@section('page-title', 'Tambah Penilaian')
@section('page-subtitle', 'Input penilaian pegawai berdasarkan kriteria SAW')

@section('content')
@php
    $scoreOptions = [
        ['score' => 1, 'range' => '91-100', 'label' => 'Sangat Baik'],
        ['score' => 2, 'range' => '81-90', 'label' => 'Baik'],
        ['score' => 3, 'range' => '71-80', 'label' => 'Cukup'],
        ['score' => 4, 'range' => '61-70', 'label' => 'Kurang'],
        ['score' => 5, 'range' => '<= 60', 'label' => 'Sangat Kurang'],
    ];

    $criteriaScales = [
        'C1' => ['91-100 (Sangat Baik) = 1', '81-90 (Baik) = 2', '71-80 (Cukup) = 3', '61-70 (Kurang) = 4', '<= 60 (Sangat Kurang) = 5'],
        'C2' => ['Belum pernah atau > 5 tahun = 5', '4-5 tahun = 4', '2-3 tahun = 3', '1 tahun = 2', '< 1 tahun = 1'],
        'C3' => ['> 8 tahun = 5', '6-8 tahun = 4', '4-5 tahun = 3', '2-3 tahun = 2', '< 2 tahun = 1'],
        'C4' => ['Baru promosi (< 1 tahun) = 5', 'Promosi 1-3 tahun = 4', 'Promosi 3-5 tahun = 3', 'Promosi > 5 tahun = 2', 'Tidak pernah promosi = 1'],
        'C5' => ['<= 30 tahun = 5', '31-40 tahun = 4', '41-50 tahun = 3', '51-55 tahun = 2', '> 55 tahun = 1'],
    ];

    $manualCriteria = $criteria->filter(fn ($criterion) => strtoupper((string) $criterion->code) === 'C1');
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
                                Pilih kategori nilai capaian kinerja untuk C1. Kriteria C2-C5 dihitung otomatis dari data pegawai.
                            </div>
                        </div>
                    </div>
                    
                    @foreach($manualCriteria as $criterion)
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
                                        @foreach($scoreOptions as $option)
                                            @php $score = $option['score']; @endphp
                                            <div class="form-check">
                                                <input class="form-check-input @error('scores.' . $criterion->id) is-invalid @enderror" 
                                                       type="radio" 
                                                       name="scores[{{ $criterion->id }}]" 
                                                       id="score_{{ $criterion->id }}_{{ $score }}" 
                                                       value="{{ $score }}"
                                                       {{ old('scores.' . $criterion->id) == $score ? 'checked' : '' }}>
                                                <label class="form-check-label" for="score_{{ $criterion->id }}_{{ $score }}">
                                                    <div class="score-option">
                                                        <div class="score-range">{{ $option['range'] }}</div>
                                                        <div class="score-category">
                                                            <span>{{ $option['label'] }}</span>
                                                            <small>Skor {{ $score }}</small>
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

                    <div class="criteria-reference-card mb-3">
                        <div class="criteria-reference-head">
                            <div>
                                <h6>Referensi Kriteria dan Skala Nilai SAW</h6>
                                <p>C1 diinput manual pada form ini. C2 sampai C5 dihitung otomatis dari profil, riwayat pelatihan, jabatan, promosi, dan usia pegawai.</p>
                            </div>
                            <span class="auto-note">C2-C5 Otomatis</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle criteria-reference-table">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Kriteria</th>
                                        <th>Atribut</th>
                                        <th>Bobot</th>
                                        <th>Skala Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($criteria as $criterion)
                                        @php $code = strtoupper((string) $criterion->code); @endphp
                                        <tr>
                                            <td class="criteria-code-cell">{{ $criterion->code }}</td>
                                            <td>
                                                <strong>{{ $criterion->name }}</strong>
                                                @if($criterion->description)
                                                    <small>{{ $criterion->description }}</small>
                                                @endif
                                                @if($code !== 'C1')
                                                    <span class="auto-source-badge">Dihitung otomatis</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="attribute-badge">{{ ucfirst($criterion->type) }}</span>
                                            </td>
                                            <td>{{ number_format($criterion->weight, 3) }}</td>
                                            <td>
                                                <div class="scale-chip-list">
                                                    @foreach($criteriaScales[$code] ?? [] as $scale)
                                                        <span>{{ $scale }}</span>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                     
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

.criteria-reference-card {
    border: 1px solid var(--line);
    border-radius: 8px;
    overflow: hidden;
    background: #ffffff;
}

.criteria-reference-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid var(--line);
    background: #f8faf9;
}

.criteria-reference-head h6 {
    margin: 0;
    color: var(--text-main);
    font-weight: 850;
}

.criteria-reference-head p {
    margin: 0.35rem 0 0;
    color: var(--text-muted);
    line-height: 1.5;
}

.auto-note,
.auto-source-badge,
.attribute-badge {
    display: inline-flex;
    align-items: center;
    width: fit-content;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 800;
    white-space: nowrap;
}

.auto-note {
    padding: 0.35rem 0.65rem;
    background: var(--ma-light-yellow);
    color: #6f4d00;
}

.criteria-reference-table {
    margin: 0;
}

.criteria-reference-table th {
    color: var(--text-muted);
    font-size: 0.78rem;
    font-weight: 850;
    text-transform: uppercase;
    background: #ffffff;
}

.criteria-reference-table td {
    vertical-align: middle;
    border-color: var(--line);
}

.criteria-reference-table td:nth-child(2) strong {
    display: block;
    color: var(--text-main);
    font-weight: 850;
}

.criteria-reference-table td:nth-child(2) small {
    display: block;
    margin-top: 0.2rem;
    color: var(--text-muted);
    line-height: 1.45;
}

.criteria-code-cell {
    color: var(--text-main);
    font-weight: 850;
}

.auto-source-badge {
    margin-top: 0.45rem;
    padding: 0.25rem 0.5rem;
    background: #edf6f0;
    color: var(--ma-dark-green);
}

.attribute-badge {
    padding: 0.28rem 0.55rem;
    background: #687481;
    color: #ffffff;
}

.scale-chip-list {
    display: grid;
    gap: 0.35rem;
    min-width: 260px;
}

.scale-chip-list span {
    display: block;
    padding: 0.35rem 0.55rem;
    border-radius: 6px;
    background: #edf6f0;
    color: var(--text-main);
    font-size: 0.88rem;
    line-height: 1.3;
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

    .criteria-reference-head {
        flex-direction: column;
    }

    .scale-chip-list {
        min-width: 220px;
    }
}
</style>
@endsection
