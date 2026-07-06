@extends('layouts.app')

@section('title', 'Detail Pegawai')
@section('page-title', 'Detail Pegawai')
@section('page-subtitle', 'Informasi lengkap data pegawai')

@section('content')
@php
    $canManageEmployees = \App\Support\Access::allows('employees.manage');
    $canManageTrainingHistory = \App\Support\Access::allows('training-history.manage');
    $latestTrainingDate = $employee->latest_training_date;
@endphp

<div class="toolbar-panel mb-3">
    <div>
        <h5 class="toolbar-title">{{ $employee->name }}</h5>
        <p class="toolbar-subtitle">{{ $employee->nip }} · {{ $employee->position->name }} · {{ $employee->workUnit?->name ?? '-' }}</p>
    </div>
    <div class="toolbar-actions">
        <a href="#profil-pegawai" class="btn btn-outline-primary">
            <i class="fas fa-user me-2"></i>Profil
        </a>
        <a href="#riwayat-pelatihan" class="btn btn-outline-primary">
            <i class="fas fa-graduation-cap me-2"></i>Riwayat Pelatihan
        </a>
        <a href="#rekomendasi-tna" class="btn btn-outline-primary">
            <i class="fas fa-magnifying-glass-chart me-2"></i>Rekomendasi TNA
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card" id="profil-pegawai">
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
                            <small class="text-muted">{{ $employee->position->jobFamily?->name ?? '-' }}</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Unit Kerja</label>
                            <p class="form-control-plaintext">{{ $employee->workUnit?->name ?? '-' }}</p>
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
                            <label class="form-label fw-bold">Masa Jabatan Saat Ini</label>
                            <p class="form-control-plaintext">
                                {{ $employee->current_position_years }} tahun
                                @if($employee->current_position_start_date)
                                    <small class="text-muted d-block">TMT {{ $employee->current_position_start_date->format('d F Y') }}</small>
                                @endif
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pelatihan Terakhir</label>
                            <p class="form-control-plaintext">
                                {{ $latestTrainingDate ? $latestTrainingDate->format('d F Y') : 'Belum pernah / belum tercatat' }}
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Promosi Terakhir</label>
                            <p class="form-control-plaintext">
                                {{ $employee->last_promotion_date ? $employee->last_promotion_date->format('d F Y') : 'Tidak pernah / belum tercatat' }}
                            </p>
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
        
        <div class="card mt-3" id="rekomendasi-tna">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-magnifying-glass-chart me-2"></i>
                    Rekomendasi TNA
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
                        <i class="fas fa-magnifying-glass-chart fa-3x mb-3"></i>
                        <p>Belum ada rekomendasi pelatihan</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-3" id="riwayat-pelatihan">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">
                    <i class="fas fa-graduation-cap me-2"></i>
                    Riwayat Pelatihan Pegawai
                </h5>
                @if($canManageTrainingHistory)
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#trainingHistoryModal">
                        <i class="fas fa-plus me-2"></i>
                        Tambah Riwayat
                    </button>
                @endif
            </div>
            <div class="card-body">
                @if($employee->trainingHistories->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Pelatihan</th>
                                    <th>Kategori</th>
                                    <th>Penyelenggara</th>
                                    <th>Tanggal</th>
                                    <th>JP</th>
                                    <th>Sertifikat</th>
                                    <th>Hasil</th>
                                    @if($canManageTrainingHistory)
                                        <th class="text-end">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employee->trainingHistories as $history)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $history->training_name }}</div>
                                            @if($history->notes)
                                                <small class="text-muted">{{ $history->notes }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $history->category ?? '-' }}</td>
                                        <td>{{ $history->provider ?? '-' }}</td>
                                        <td>
                                            @if($history->start_date || $history->end_date)
                                                {{ $history->start_date?->format('d/m/Y') ?? '-' }}
                                                @if($history->end_date && $history->end_date->ne($history->start_date))
                                                    - {{ $history->end_date->format('d/m/Y') }}
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $history->hours ? $history->hours . ' JP' : '-' }}</td>
                                        <td>{{ $history->certificate_number ?? '-' }}</td>
                                        <td>{{ $history->result ?? '-' }}</td>
                                        @if($canManageTrainingHistory)
                                            <td class="text-end">
                                                <form action="{{ route('training-histories.destroy', $history) }}" method="POST" onsubmit="return confirm('Hapus riwayat pelatihan ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                        <p class="mb-0">Belum ada riwayat pelatihan yang diinput petugas kepegawaian.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="d-flex gap-2">
            @if($canManageEmployees)
            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>
                Edit Data
            </a>
            @endif
            @if(\App\Support\Access::allows('assessments.manage'))
            <a href="{{ route('assessments.create', ['employee_id' => $employee->id]) }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>
                Tambah Penilaian
            </a>
            @endif
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Kembali
            </a>
        </div>
    </div>
</div>

@if($canManageTrainingHistory)
<div class="modal fade" id="trainingHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Riwayat Pelatihan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('employees.training-histories.store', $employee) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="training_name" class="form-label">Nama Pelatihan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="training_name" name="training_name" required>
                        </div>
                        <div class="col-md-4">
                            <label for="category" class="form-label">Kategori</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Pilih kategori</option>
                                <option value="Teknis Yudisial">Teknis Yudisial</option>
                                <option value="Kepaniteraan">Kepaniteraan</option>
                                <option value="Kesekretariatan">Kesekretariatan</option>
                                <option value="Manajerial">Manajerial</option>
                                <option value="E-Learning">E-Learning</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="provider" class="form-label">Penyelenggara</label>
                            <input type="text" class="form-control" id="provider" name="provider">
                        </div>
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="start_date" name="start_date">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" id="end_date" name="end_date">
                        </div>
                        <div class="col-md-3">
                            <label for="hours" class="form-label">Jumlah JP</label>
                            <input type="number" class="form-control" id="hours" name="hours" min="1" max="1000">
                        </div>
                        <div class="col-md-5">
                            <label for="certificate_number" class="form-label">Nomor Sertifikat</label>
                            <input type="text" class="form-control" id="certificate_number" name="certificate_number">
                        </div>
                        <div class="col-md-4">
                            <label for="result" class="form-label">Hasil/Kelulusan</label>
                            <input type="text" class="form-control" id="result" name="result" placeholder="Lulus / Selesai / Nilai">
                        </div>
                        <div class="col-12">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
