@extends('layouts.app')

@section('title', 'Laporan Kebutuhan Pelatihan')
@section('page-title', 'Laporan Kebutuhan Pelatihan')
@section('page-subtitle', 'Laporan Lengkap Hasil Analisis SAW')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print me-2"></i>
                Cetak Laporan
            </button>
            <button onclick="exportToExcel()" class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i>
                Export Excel
            </button>
            <button onclick="exportToPDF()" class="btn btn-danger">
                <i class="fas fa-file-pdf me-2"></i>
                Export PDF
            </button>
        </div>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('training-needs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Kembali
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-4 mb-5">
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="summary-card total-card">
            <div class="summary-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="summary-content">
                <h3 class="summary-number">{{ $summary['total'] }}</h3>
                <p class="summary-label">Total Kebutuhan Pelatihan</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="summary-card priority-card">
            <div class="summary-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="summary-content">
                <h3 class="summary-number">{{ $summary['by_priority'] }}</h3>
                <p class="summary-label">Prioritas Tinggi (Top 10)</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="summary-card pending-card">
            <div class="summary-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="summary-content">
                <h3 class="summary-number">{{ $summary['by_status']['pending'] ?? 0 }}</h3>
                <p class="summary-label">Menunggu Persetujuan</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="summary-card score-card">
            <div class="summary-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="summary-content">
                <h3 class="summary-number">{{ number_format($summary['avg_score'], 3) }}</h3>
                <p class="summary-label">Rata-rata Skor SAW</p>
            </div>
        </div>
    </div>
</div>

<!-- Status Distribution Chart -->
<div class="row mb-5">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Distribusi Status Pelatihan
                </h5>
            </div>
            <div class="card-body">
                <canvas id="statusChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Top 10 Prioritas Pelatihan
                </h5>
            </div>
            <div class="card-body">
                <canvas id="priorityChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Report Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-table me-2"></i>
            Laporan Detail Kebutuhan Pelatihan
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="reportTable">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Prioritas</th>
                        <th>NIP</th>
                        <th>Nama Pegawai</th>
                        <th>Jabatan</th>
                        <th>Jenis Pelatihan</th>
                        <th>Skor SAW</th>
                        <th>Status</th>
                        <th>Tanggal Rekomendasi</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trainingNeeds as $index => $need)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if($need->priority_rank <= 3)
                                <span class="badge bg-danger">#{{ $need->priority_rank }}</span>
                            @elseif($need->priority_rank <= 10)
                                <span class="badge bg-warning">#{{ $need->priority_rank }}</span>
                            @else
                                <span class="badge bg-secondary">#{{ $need->priority_rank }}</span>
                            @endif
                        </td>
                        <td>{{ $need->employee->nip }}</td>
                        <td>{{ $need->employee->name }}</td>
                        <td>{{ $need->employee->position->name }}</td>
                        <td>{{ $need->training_type }}</td>
                        <td>
                            <span class="fw-bold text-primary">{{ number_format($need->saw_score, 4) }}</span>
                        </td>
                        <td>
                            @if($need->status === 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($need->status === 'approved')
                                <span class="badge bg-success">Disetujui</span>
                            @elseif($need->status === 'completed')
                                <span class="badge bg-info">Selesai</span>
                            @else
                                <span class="badge bg-danger">Ditolak</span>
                            @endif
                        </td>
                        <td>
                            {{ $need->recommended_date ? $need->recommended_date->format('d/m/Y') : '-' }}
                        </td>
                        <td>
                            {{ $need->notes ? Str::limit($need->notes, 50) : '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Report Footer -->
<div class="row mt-5 print-footer">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informasi Laporan</h6>
                        <p class="mb-1"><strong>Tanggal Cetak:</strong> {{ now()->format('d F Y H:i') }} WIB</p>
                        <p class="mb-1"><strong>Total Data:</strong> {{ $trainingNeeds->count() }} kebutuhan pelatihan</p>
                        <p class="mb-0"><strong>Metode Analisis:</strong> Simple Additive Weighting (SAW)</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <h6>Mahkamah Agung Republik Indonesia</h6>
                        <p class="mb-1">Sistem Analisa Kebutuhan Pelatihan</p>
                        <p class="mb-0">Training Need Analysis (TNA) System</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Report Styles */
.summary-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 20px;
    height: 100%;
}

.summary-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

.summary-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    flex-shrink: 0;
}

.total-card .summary-icon {
    background: linear-gradient(135deg, var(--ma-green), var(--ma-dark-green));
}

.priority-card .summary-icon {
    background: linear-gradient(135deg, #dc3545, #c82333);
}

.pending-card .summary-icon {
    background: linear-gradient(135deg, #ffc107, #ffb300);
}

.score-card .summary-icon {
    background: linear-gradient(135deg, #17a2b8, #138496);
}

.summary-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    line-height: 1;
}

.summary-label {
    color: #6c757d;
    margin: 5px 0 0 0;
    font-weight: 500;
}

/* Print Styles */
@media print {
    .btn, .card-header, .print-hide {
        display: none !important;
    }
    
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
    
    .summary-card {
        box-shadow: none !important;
        border: 1px solid #ddd;
    }
    
    .table {
        font-size: 12px;
    }
    
    .print-footer {
        page-break-inside: avoid;
    }
    
    body {
        background: white !important;
    }
}

/* Table Styles */
#reportTable {
    font-size: 0.9rem;
}

#reportTable th {
    background: linear-gradient(135deg, var(--ma-green), var(--ma-dark-green)) !important;
    color: white;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
}

#reportTable td {
    vertical-align: middle;
    text-align: center;
}

#reportTable td:nth-child(4) {
    text-align: left;
}

#reportTable td:nth-child(10) {
    text-align: left;
}
</style>

<script>
// Chart.js for Status Distribution
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'Disetujui', 'Selesai', 'Ditolak'],
        datasets: [{
            data: [
                {{ $summary['by_status']['pending'] ?? 0 }},
                {{ $summary['by_status']['approved'] ?? 0 }},
                {{ $summary['by_status']['completed'] ?? 0 }},
                {{ $summary['by_status']['rejected'] ?? 0 }}
            ],
            backgroundColor: [
                '#ffc107',
                '#228B22',
                '#17a2b8',
                '#dc3545'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Chart.js for Priority Distribution
const priorityCtx = document.getElementById('priorityChart').getContext('2d');
const topPriorities = @json($trainingNeeds->take(10));
const priorityChart = new Chart(priorityCtx, {
    type: 'bar',
    data: {
        labels: topPriorities.map(item => item.employee.name.substring(0, 15) + '...'),
        datasets: [{
            label: 'Skor SAW',
            data: topPriorities.map(item => item.saw_score),
            backgroundColor: 'rgba(34, 139, 34, 0.8)',
            borderColor: 'rgba(34, 139, 34, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                max: 1
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Export Functions
function exportToExcel() {
    // Simple CSV export
    let csv = 'No,Prioritas,NIP,Nama Pegawai,Jabatan,Jenis Pelatihan,Skor SAW,Status,Tanggal Rekomendasi,Catatan\n';
    
    @foreach($trainingNeeds as $index => $need)
    csv += '{{ $index + 1 }},{{ $need->priority_rank }},"{{ $need->employee->nip }}","{{ $need->employee->name }}","{{ $need->employee->position->name }}","{{ $need->training_type }}",{{ $need->saw_score }},"{{ $need->status }}","{{ $need->recommended_date ? $need->recommended_date->format('d/m/Y') : '-' }}","{{ $need->notes ? str_replace('"', '""', $need->notes) : '-' }}"\n';
    @endforeach
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'laporan-kebutuhan-pelatihan-' + new Date().toISOString().split('T')[0] + '.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

function exportToPDF() {
    // Simple print to PDF
    window.print();
}

// Make table sortable (optional enhancement)
document.addEventListener('DOMContentLoaded', function() {
    // Add sorting functionality if needed
    console.log('Report loaded with {{ $trainingNeeds->count() }} records');
});
</script>
@endsection