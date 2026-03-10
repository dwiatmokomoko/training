@extends('layouts.app')

@section('title', 'Detail Pegawai')
@section('page-title', 'Detail Pegawai')
@section('page-subtitle', 'Informasi lengkap data pegawai')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>
                    Informasi Pegawai
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">NIP</label>
                            <p class="form-control-plaintext">{{ $employee->nip }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <p class="form-control-plaintext">{{ $employee->name }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <p class="form-control-plaintext">{{ $employee->email }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Jabatan</label>
                            <p class="form-control-plaintext">{{ $employee->position->name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal Lahir</label>
                            <p class="form-control-plaintext">{{ $employee->birth_date ? $employee->birth_date->format('d F Y') : '-' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pendidikan</label>
                            <p class="form-control-plaintext">{{ $employee->education_level }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pengalaman Kerja</label>
                            <p class="form-control-plaintext">{{ $employee->work_experience }} tahun</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal Bergabung</label>
                            <p class="form-control-plaintext">{{ $employee->created_at->format('d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Statistik Penilaian
                </h5>
            </div>
            <div class="card-body">
                @if($employee->assessments->count() > 0)
                    @php
                        $latestAssessment = $employee->assessments->sortByDesc('created_at')->first();
                    @endphp
                    <div class="mb-3">
                        <label class="form-label fw-bold">Penilaian Terakhir</label>
                        <p class="form-control-plaintext">{{ $latestAssessment->created_at->format('d F Y') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Total Penilaian</label>
                        <p class="form-control-plaintext">{{ $employee->assessments->count() }} kali</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Rata-rata Skor</label>
                        <p class="form-control-plaintext">{{ number_format($employee->assessments->avg('total_score'), 2) }}</p>
                    </div>
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                        <p>Belum ada penilaian</p>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-graduation-cap me-2"></i>
                    Riwayat Pelatihan
                </h5>
            </div>
            <div class="card-body">
                @if($employee->trainingNeeds->count() > 0)
                    @foreach($employee->trainingNeeds->take(5) as $training)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <small class="fw-bold">{{ $training->training_type }}</small>
                            <br>
                            <small class="text-muted">{{ $training->created_at->format('M Y') }}</small>
                        </div>
                        <span class="badge 
                            @if($training->status === 'completed') bg-success
                            @elseif($training->status === 'approved') bg-primary
                            @elseif($training->status === 'pending') bg-warning
                            @else bg-danger
                            @endif">
                            {{ ucfirst($training->status) }}
                        </span>
                    </div>
                    @endforeach
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                        <p>Belum ada riwayat pelatihan</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="d-flex gap-2">
            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>
                Edit Data
            </a>
            <a href="{{ route('assessments.create', ['employee_id' => $employee->id]) }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>
                Tambah Penilaian
            </a>
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Kembali
            </a>
        </div>
    </div>
</div>
@endsection