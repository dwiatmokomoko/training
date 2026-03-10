@extends('layouts.app')

@section('title', 'Detail Kebutuhan Pelatihan')
@section('page-title', 'Detail Kebutuhan Pelatihan')
@section('page-subtitle', 'Informasi lengkap hasil analisis SAW')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-graduation-cap me-2"></i>
                    Hasil Analisis Kebutuhan Pelatihan
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pegawai</label>
                            <p class="form-control-plaintext">{{ $trainingNeed->employee->name }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">NIP</label>
                            <p class="form-control-plaintext">{{ $trainingNeed->employee->nip }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Jabatan</label>
                            <p class="form-control-plaintext">{{ $trainingNeed->employee->position->name }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Jenis Pelatihan</label>
                            <p class="form-control-plaintext">{{ $trainingNeed->training_type }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Prioritas</label>
                            <p class="form-control-plaintext">
                                @if($trainingNeed->priority_rank <= 3)
                                    <span class="badge bg-danger fs-6">#{{ $trainingNeed->priority_rank }} - Sangat Tinggi</span>
                                @elseif($trainingNeed->priority_rank <= 10)
                                    <span class="badge bg-warning fs-6">#{{ $trainingNeed->priority_rank }} - Tinggi</span>
                                @else
                                    <span class="badge bg-secondary fs-6">#{{ $trainingNeed->priority_rank }} - Sedang</span>
                                @endif
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Skor SAW</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-primary fs-6">{{ number_format($trainingNeed->saw_score, 4) }}</span>
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <p class="form-control-plaintext">
                                @if($trainingNeed->status === 'pending')
                                    <span class="badge bg-warning fs-6">Pending</span>
                                @elseif($trainingNeed->status === 'approved')
                                    <span class="badge bg-success fs-6">Disetujui</span>
                                @elseif($trainingNeed->status === 'completed')
                                    <span class="badge bg-info fs-6">Selesai</span>
                                @else
                                    <span class="badge bg-danger fs-6">Ditolak</span>
                                @endif
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal Rekomendasi</label>
                            <p class="form-control-plaintext">
                                {{ $trainingNeed->recommended_date ? $trainingNeed->recommended_date->format('d F Y') : '-' }}
                            </p>
                        </div>
                    </div>
                </div>
                
                @if($trainingNeed->training_description)
                <div class="mb-4">
                    <label class="form-label fw-bold">Deskripsi Pelatihan</label>
                    <div class="card">
                        <div class="card-body">
                            <p class="mb-0">{{ $trainingNeed->training_description }}</p>
                        </div>
                    </div>
                </div>
                @endif
                
                @if($trainingNeed->notes)
                <div class="mb-4">
                    <label class="form-label fw-bold">Catatan</label>
                    <div class="card">
                        <div class="card-body">
                            <p class="mb-0">{{ $trainingNeed->notes }}</p>
                        </div>
                    </div>
                </div>
                @endif
                
                <h6 class="mb-3">
                    <i class="fas fa-calculator me-2"></i>
                    Detail Perhitungan SAW
                </h6>
                
                @if($trainingNeed->employee->assessments->count() > 0)
                @php
                    $latestAssessment = $trainingNeed->employee->assessments->sortByDesc('created_at')->first();
                @endphp
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Kriteria</th>
                                <th>Bobot</th>
                                <th>Nilai</th>
                                <th>Normalisasi</th>
                                <th>Skor Terbobot</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalWeightedScore = 0;
                            @endphp
                            @foreach($latestAssessment->scores as $score)
                            @php
                                $normalizedScore = $score->score / 5; // Normalisasi ke skala 0-1
                                $weightedScore = $normalizedScore * $score->criteria->weight;
                                $totalWeightedScore += $weightedScore;
                            @endphp
                            <tr>
                                <td>{{ $score->criteria->name }}</td>
                                <td>{{ number_format($score->criteria->weight * 100, 1) }}%</td>
                                <td>{{ $score->score }}/5</td>
                                <td>{{ number_format($normalizedScore, 3) }}</td>
                                <td>{{ number_format($weightedScore, 4) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-secondary">
                            <tr>
                                <th colspan="4">Total Skor SAW</th>
                                <th>{{ number_format($totalWeightedScore, 4) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Belum ada data penilaian untuk pegawai ini.
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
                    Analisis Prioritas
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="priority-display">
                        <div class="priority-circle">
                            <span class="priority-rank">#{{ $trainingNeed->priority_rank }}</span>
                        </div>
                        <div class="priority-label">
                            @if($trainingNeed->priority_rank <= 3)
                                Prioritas Sangat Tinggi
                            @elseif($trainingNeed->priority_rank <= 10)
                                Prioritas Tinggi
                            @else
                                Prioritas Sedang
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="score-breakdown">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Skor SAW</span>
                        <span class="fw-bold">{{ number_format($trainingNeed->saw_score, 4) }}</span>
                    </div>
                    <div class="progress mb-3">
                        <div class="progress-bar" style="width: {{ ($trainingNeed->saw_score * 100) }}%"></div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small">Persentase dari Maksimal</span>
                        <span class="small fw-bold">{{ number_format(($trainingNeed->saw_score * 100), 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i>
                    Rekomendasi Tindakan
                </h5>
            </div>
            <div class="card-body">
                @if($trainingNeed->priority_rank <= 3)
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Tindakan Segera Diperlukan!</strong><br>
                        Pegawai ini memerlukan pelatihan dengan prioritas sangat tinggi.
                    </div>
                    <ul class="small mb-0">
                        <li>Jadwalkan pelatihan dalam 1-2 minggu</li>
                        <li>Alokasikan anggaran prioritas</li>
                        <li>Pertimbangkan pelatihan intensif</li>
                        <li>Monitor progress secara ketat</li>
                    </ul>
                @elseif($trainingNeed->priority_rank <= 10)
                    <div class="alert alert-warning">
                        <i class="fas fa-clock me-2"></i>
                        <strong>Pelatihan Prioritas Tinggi</strong><br>
                        Jadwalkan pelatihan dalam 1 bulan ke depan.
                    </div>
                    <ul class="small mb-0">
                        <li>Rencanakan pelatihan dalam 2-4 minggu</li>
                        <li>Koordinasi dengan unit terkait</li>
                        <li>Siapkan materi pelatihan yang sesuai</li>
                        <li>Evaluasi berkala setelah pelatihan</li>
                    </ul>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Pelatihan Terjadwal</strong><br>
                        Dapat dijadwalkan sesuai ketersediaan anggaran.
                    </div>
                    <ul class="small mb-0">
                        <li>Masukkan dalam rencana pelatihan tahunan</li>
                        <li>Pertimbangkan pelatihan berkelompok</li>
                        <li>Manfaatkan pelatihan internal</li>
                        <li>Monitor perkembangan kompetensi</li>
                    </ul>
                @endif
            </div>
        </div>
        
        @if($trainingNeed->status === 'pending')
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>
                    Update Status
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('training-needs.update', $trainingNeed) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="pending" {{ $trainingNeed->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $trainingNeed->status === 'approved' ? 'selected' : '' }}>Disetujui</option>
                            <option value="rejected" {{ $trainingNeed->status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            <option value="completed" {{ $trainingNeed->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Catatan</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3">{{ $trainingNeed->notes }}</textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i>
                        Update Status
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="d-flex gap-2">
            @if($trainingNeed->status === 'pending')
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                <i class="fas fa-check me-2"></i>
                Setujui
            </button>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="fas fa-times me-2"></i>
                Tolak
            </button>
            @endif
            <a href="{{ route('training-needs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Kembali
            </a>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Setujui Pelatihan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('training-needs.update', $trainingNeed) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="approved">
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menyetujui pelatihan untuk <strong>{{ $trainingNeed->employee->name }}</strong>?</p>
                    <div class="mb-3">
                        <label for="approve_notes" class="form-label">Catatan Persetujuan</label>
                        <textarea class="form-control" id="approve_notes" name="notes" rows="3" placeholder="Tambahkan catatan persetujuan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tolak Pelatihan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('training-needs.update', $trainingNeed) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="rejected">
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menolak pelatihan untuk <strong>{{ $trainingNeed->employee->name }}</strong>?</p>
                    <div class="mb-3">
                        <label for="reject_notes" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reject_notes" name="notes" rows="3" placeholder="Jelaskan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.priority-display {
    margin-bottom: 20px;
}

.priority-circle {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--ma-green), var(--ma-dark-green));
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    color: white;
}

.priority-rank {
    font-size: 2rem;
    font-weight: bold;
}

.priority-label {
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