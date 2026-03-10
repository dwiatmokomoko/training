@extends('layouts.app')

@section('title', 'Detail Penilaian')
@section('page-title', 'Detail Penilaian')
@section('page-subtitle', 'Informasi lengkap hasil penilaian pegawai')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-check me-2"></i>
                    Hasil Penilaian
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pegawai</label>
                            <p class="form-control-plaintext">{{ $assessment->employee->name }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">NIP</label>
                            <p class="form-control-plaintext">{{ $assessment->employee->nip }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Jabatan</label>
                            <p class="form-control-plaintext">{{ $assessment->employee->position->name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal Penilaian</label>
                            <p class="form-control-plaintext">{{ $assessment->assessment_date->format('d F Y') }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Total Skor</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-primary fs-6">{{ number_format($assessment->total_score, 2) }}</span>
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Dinilai Oleh</label>
                            <p class="form-control-plaintext">{{ $assessment->created_at->format('d F Y H:i') }}</p>
                        </div>
                    </div>
                </div>
                
                <h6 class="mb-3">
                    <i class="fas fa-star me-2"></i>
                    Detail Penilaian per Kriteria
                </h6>
                
                @foreach($assessment->scores as $score)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <h6 class="mb-1">{{ $score->criteria->name }}</h6>
                                <small class="text-muted">Bobot: {{ number_format($score->criteria->weight * 100, 1) }}%</small>
                                @if($score->criteria->description)
                                    <p class="small text-muted mt-1">{{ $score->criteria->description }}</p>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="score-display">
                                        <div class="score-number">{{ $score->score }}</div>
                                        <div class="score-stars">
                                            @for($i = 1; $i <= $score->score; $i++)
                                                <i class="fas fa-star text-warning"></i>
                                            @endfor
                                            @for($i = $score->score + 1; $i <= 5; $i++)
                                                <i class="far fa-star text-muted"></i>
                                            @endfor
                                        </div>
                                        <small class="score-label">
                                            @if($score->score == 1) Sangat Kurang
                                            @elseif($score->score == 2) Kurang
                                            @elseif($score->score == 3) Cukup
                                            @elseif($score->score == 4) Baik
                                            @else Sangat Baik
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <label class="form-label fw-bold">Skor Terbobot</label>
                                    <div class="weighted-score">
                                        {{ number_format($score->score * $score->criteria->weight, 3) }}
                                    </div>
                                    <div class="progress mt-2">
                                        <div class="progress-bar" style="width: {{ ($score->score * $score->criteria->weight / 5) * 100 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                
                @if($assessment->notes)
                <div class="card mt-4">
                    <div class="card-body">
                        <h6 class="mb-2">
                            <i class="fas fa-sticky-note me-2"></i>
                            Catatan Penilaian
                        </h6>
                        <p class="mb-0">{{ $assessment->notes }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Analisis Skor
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="total-score-display">
                        <div class="score-circle">
                            <span class="score-value">{{ number_format($assessment->total_score, 1) }}</span>
                            <span class="score-max">/5.0</span>
                        </div>
                        <div class="score-percentage">
                            {{ number_format(($assessment->total_score / 5) * 100, 1) }}%
                        </div>
                    </div>
                </div>
                
                <div class="score-breakdown">
                    @foreach($assessment->scores as $score)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small">{{ Str::limit($score->criteria->name, 20) }}</span>
                        <div class="d-flex align-items-center">
                            <div class="progress me-2" style="width: 60px; height: 8px;">
                                <div class="progress-bar" style="width: {{ ($score->score / 5) * 100 }}%"></div>
                            </div>
                            <span class="small fw-bold">{{ $score->score }}/5</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i>
                    Rekomendasi
                </h5>
            </div>
            <div class="card-body">
                @php
                    $averageScore = $assessment->total_score;
                    $lowestScore = $assessment->scores->min('score');
                    $lowestCriteria = $assessment->scores->where('score', $lowestScore)->first();
                @endphp
                
                @if($averageScore >= 4.0)
                    <div class="alert alert-success">
                        <i class="fas fa-trophy me-2"></i>
                        <strong>Performa Excellent!</strong><br>
                        Pegawai menunjukkan kinerja yang sangat baik di semua kriteria.
                    </div>
                @elseif($averageScore >= 3.0)
                    <div class="alert alert-info">
                        <i class="fas fa-thumbs-up me-2"></i>
                        <strong>Performa Baik</strong><br>
                        Masih ada ruang untuk peningkatan, terutama di area {{ $lowestCriteria->criteria->name }}.
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Perlu Peningkatan</strong><br>
                        Disarankan untuk memberikan pelatihan khusus di area {{ $lowestCriteria->criteria->name }}.
                    </div>
                @endif
                
                <div class="mt-3">
                    <h6 class="small fw-bold">Saran Pelatihan:</h6>
                    <ul class="small mb-0">
                        @if($lowestScore <= 2)
                            <li>Pelatihan intensif {{ $lowestCriteria->criteria->name }}</li>
                        @endif
                        @if($averageScore < 3.5)
                            <li>Program mentoring dan coaching</li>
                            <li>Workshop pengembangan kompetensi</li>
                        @endif
                        <li>Evaluasi berkala setiap 3 bulan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="d-flex gap-2">
            <a href="{{ route('assessments.edit', $assessment) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>
                Edit Penilaian
            </a>
            <a href="{{ route('assessments.create', ['employee_id' => $assessment->employee_id]) }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>
                Penilaian Baru
            </a>
            <a href="{{ route('assessments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Kembali
            </a>
        </div>
    </div>
</div>

<style>
.score-display {
    padding: 15px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 10px;
}

.score-number {
    font-size: 2rem;
    font-weight: bold;
    color: var(--ma-green);
}

.score-stars {
    margin: 8px 0;
}

.score-label {
    font-weight: 600;
    color: #6c757d;
}

.weighted-score {
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--ma-dark-green);
}

.total-score-display {
    margin-bottom: 20px;
}

.score-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--ma-green), var(--ma-dark-green));
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    color: white;
}

.score-value {
    font-size: 2.5rem;
    font-weight: bold;
    line-height: 1;
}

.score-max {
    font-size: 1rem;
    opacity: 0.8;
}

.score-percentage {
    font-size: 1.2rem;
    font-weight: bold;
    color: var(--ma-green);
}

.score-breakdown {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 10px;
}
</style>
@endsection