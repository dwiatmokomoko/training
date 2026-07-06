@extends('layouts.app')

@section('title', 'Laporan Kebutuhan Pelatihan')
@section('page-title', 'Laporan Kebutuhan Pelatihan')
@section('page-subtitle', 'Laporan Lengkap Hasil Analisis SAW')

@section('content')
<div class="toolbar-panel mb-4">
    <div>
        <h5 class="toolbar-title">Laporan Prioritas Pelatihan</h5>
        <p class="toolbar-subtitle">Cetak atau export rekap kebutuhan pelatihan hasil analisis SAW.</p>
    </div>
    <div class="toolbar-actions">
        <button type="button" onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print me-2"></i>
            Cetak Laporan
        </button>
        <button type="button" onclick="exportToExcel()" class="btn btn-success">
            <i class="fas fa-file-excel me-2"></i>
            Export CSV
        </button>
        <button type="button" onclick="exportToPDF()" class="btn btn-danger">
            <i class="fas fa-file-pdf me-2"></i>
            Export PDF
        </button>
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

<!-- SAW Calculation Flow -->
<div class="card saw-method-card mb-5">
    <div class="card-header">
        <div>
            <h5 class="mb-0">
                <i class="fas fa-square-root-variable me-2"></i>
                Alur Perhitungan SAW
            </h5>
            <small>Matriks keputusan X, normalisasi R, nilai preferensi V, lalu ranking prioritas.</small>
        </div>
    </div>
    <div class="card-body">
        <div class="saw-formula-grid mb-4">
            <div class="formula-box">
                <span>Benefit</span>
                <strong>rij = xij / max(xij)</strong>
                <p>C2, C3, dan C4. Semakin besar skor mentah, semakin besar nilai normalisasi.</p>
            </div>
            <div class="formula-box">
                <span>Cost</span>
                <strong>rij = min(xij) / xij</strong>
                <p>C1 dan C5. Semakin kecil skor mentah, semakin besar nilai normalisasi.</p>
            </div>
            <div class="formula-box">
                <span>Preferensi</span>
                <strong>Vi = SUM(Wj x rij)</strong>
                <p>Nilai akhir untuk mengurutkan prioritas kebutuhan pelatihan pegawai.</p>
            </div>
        </div>

        <div class="table-responsive data-table-shell mb-4">
            <div class="table-caption">
                <h6>Kriteria Perhitungan dan Skala Nilai</h6>
                <p>Tahap awal seperti contoh: sistem menyiapkan kriteria, atribut, bobot, sumber data, dan konversi skor.</p>
            </div>
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
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
                        <tr>
                            <td class="fw-bold">{{ $criterion->code }}</td>
                            <td>
                                <div class="fw-bold">{{ $criterion->name }}</div>
                                <small class="text-muted">{{ $criterion->description }}</small>
                            </td>
                            <td><span class="badge bg-secondary">{{ ucfirst($criterion->type) }}</span></td>
                            <td>{{ number_format($criterion->weight, 3) }}</td>
                            <td>
                                <div class="scale-list">
                                    @foreach($criteriaScaleRows[$criterion->code] ?? [] as $scale)
                                        <span>{{ $scale['label'] }} = {{ $scale['score'] }}</span>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="table-responsive data-table-shell mb-4">
            <div class="table-caption">
                <h6>Matriks Keputusan (X)</h6>
                <p>Nilai mentah setiap pegawai sebelum normalisasi. Angka diambil dari assessment dan data pegawai.</p>
            </div>
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Alternatif</th>
                        <th>Pegawai</th>
                        @foreach($criteria as $criterion)
                            <th class="text-center">{{ $criterion->code }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($decisionMatrix->take(10) as $index => $row)
                        <tr>
                            <td class="fw-bold">{{ $row['alternative_code'] ?? 'A' . ($index + 1) }}</td>
                            <td>
                                <div class="fw-bold">{{ $row['employee']->name }}</div>
                                <small class="text-muted">{{ $row['employee']->position->name ?? '-' }}</small>
                            </td>
                            @foreach($criteria as $criterion)
                                @php($code = strtoupper((string) $criterion->code))
                                <td class="text-center">{{ number_format($row['scores'][$code] ?? 0, 0) }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $criteria->count() + 2 }}" class="text-center text-muted py-4">
                                Belum ada matriks keputusan. Jalankan analisis setelah data assessment tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($decisionMatrix->isNotEmpty())
                    <tfoot class="table-secondary">
                        <tr>
                            <th colspan="2">Min / Max Kolom</th>
                            @foreach($criteria as $criterion)
                                @php($bound = $criteriaBounds[strtoupper((string) $criterion->code)] ?? ['min' => 0, 'max' => 0])
                                <th class="text-center">
                                    {{ number_format($bound['min'], 0) }} / {{ number_format($bound['max'], 0) }}
                                </th>
                            @endforeach
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <div class="table-responsive data-table-shell mb-4">
            <div class="table-caption">
                <h6>Hasil Normalisasi (R)</h6>
                <p>Nilai R diperoleh dari rumus benefit/cost per kolom kriteria, kemudian dikalikan bobot.</p>
            </div>
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Preferensi</th>
                        <th>Pegawai</th>
                        @foreach($criteria as $criterion)
                            <th class="text-center">{{ $criterion->code }}</th>
                        @endforeach
                        <th class="text-center">V</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sawPreview as $index => $row)
                        <tr>
                            <td>
                                <span class="badge bg-primary">{{ $row['preference_code'] ?? 'V' . ($index + 1) }}</span>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $row['employee']->name }}</div>
                                <small class="text-muted">{{ $row['employee']->nip }}</small>
                            </td>
                            @foreach($criteria as $criterion)
                                @php($item = collect($row['breakdown'])->first(fn ($detail) => $detail['criteria']->id === $criterion->id))
                                <td class="text-center">{{ $item ? number_format($item['normalized_score'], 3) : '-' }}</td>
                            @endforeach
                            <td class="text-center fw-bold text-primary">{{ number_format($row['saw_score'], 4) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $criteria->count() + 3 }}" class="text-center text-muted py-4">
                                Belum ada hasil normalisasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-responsive data-table-shell mb-4">
            <div class="table-caption">
                <h6>Perhitungan Nilai Preferensi (V)</h6>
                <p>Setiap nilai normalisasi dikalikan bobot, lalu dijumlahkan untuk mendapatkan skor akhir.</p>
            </div>
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Alternatif</th>
                        <th>Pegawai</th>
                        <th>Perhitungan</th>
                        <th class="text-center">Hasil V</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($preferenceRows as $row)
                        <tr>
                            <td class="fw-bold">{{ $row['preference'] }}</td>
                            <td>
                                <div class="fw-bold">{{ $row['employee']->name }}</div>
                                <small class="text-muted">{{ $row['alternative'] }}</small>
                            </td>
                            <td><code>{{ $row['preference'] }} = {{ $row['formula'] }}</code></td>
                            <td class="text-center fw-bold text-primary">{{ number_format($row['score'], 4) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Belum ada perhitungan preferensi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-responsive data-table-shell">
            <div class="table-caption">
                <h6>Hasil Perangkingan Prioritas</h6>
                <p>Nilai V tertinggi menjadi prioritas pertama untuk rekomendasi pelatihan.</p>
            </div>
            <table class="table table-sm table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Rank</th>
                        <th>NIP</th>
                        <th>Nama Pegawai</th>
                        <th>Jabatan</th>
                        <th>Nilai V</th>
                        <th>Prioritas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sawPreview as $index => $row)
                        <tr>
                            <td class="fw-bold">#{{ $index + 1 }}</td>
                            <td>{{ $row['employee']->nip }}</td>
                            <td>{{ $row['employee']->name }}</td>
                            <td>{{ $row['employee']->position->name ?? '-' }}</td>
                            <td>
                                <span class="fw-bold text-primary">{{ number_format($row['saw_score'], 4) }}</span>
                                <small class="d-block text-muted">{{ $row['preference_code'] ?? 'V' . ($index + 1) }}</small>
                            </td>
                            <td>
                                @if($index < 3)
                                    <span class="badge bg-danger">Sangat Tinggi</span>
                                @elseif($index < 10)
                                    <span class="badge bg-warning text-dark">Tinggi</span>
                                @else
                                    <span class="badge bg-secondary">Sedang</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Ranking belum tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
        <div class="table-responsive data-table-shell">
            <table class="table table-striped table-hover js-data-table" id="reportTable" data-page-length="25" data-order="[[1,&quot;asc&quot;]]">
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
    border-radius: 8px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 20px;
    height: 100%;
}

.summary-card:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

.summary-icon {
    width: 60px;
    height: 60px;
    border-radius: 8px;
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

.saw-method-card .card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.saw-method-card .card-header small {
    display: inline-flex;
    margin-top: 0.25rem;
    color: rgba(255, 255, 255, 0.78);
}

.saw-formula-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1rem;
}

.formula-box {
    border: 1px solid var(--line);
    border-radius: 8px;
    padding: 1rem;
    background: #f8fbf9;
}

.formula-box span {
    display: block;
    color: var(--ma-green);
    font-size: 0.82rem;
    font-weight: 800;
    text-transform: uppercase;
    margin-bottom: 0.35rem;
}

.formula-box strong {
    display: block;
    color: var(--text-main);
    font-size: 1.05rem;
    margin-bottom: 0.4rem;
}

.formula-box p,
.table-caption p {
    margin: 0;
    color: var(--text-muted);
    line-height: 1.5;
}

.table-caption {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 0.75rem;
}

.table-caption h6 {
    margin: 0;
    font-weight: 800;
    color: var(--text-main);
}

.scale-list {
    display: grid;
    gap: 0.28rem;
    min-width: 220px;
}

.scale-list span {
    display: inline-flex;
    align-items: center;
    padding: 0.22rem 0.45rem;
    border-radius: 6px;
    background: #eef7f1;
    color: var(--text-main);
    font-size: 0.84rem;
    line-height: 1.35;
}

.saw-method-card code {
    color: #165b31;
    white-space: normal;
    word-break: break-word;
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

@media (max-width: 991.98px) {
    .saw-formula-grid {
        grid-template-columns: 1fr;
    }

    .table-caption {
        align-items: flex-start;
        flex-direction: column;
    }
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
    const rows = [
        ['No', 'Prioritas', 'NIP', 'Nama Pegawai', 'Jabatan', 'Jenis Pelatihan', 'Skor SAW', 'Status', 'Tanggal Rekomendasi', 'Catatan'],
        @foreach($trainingNeeds as $index => $need)
            [
                '{{ $index + 1 }}',
                '{{ $need->priority_rank }}',
                @json($need->employee->nip),
                @json($need->employee->name),
                @json($need->employee->position->name),
                @json($need->training_type),
                '{{ number_format((float) $need->saw_score, 4) }}',
                @json($need->status),
                @json($need->recommended_date ? $need->recommended_date->format('d/m/Y') : '-'),
                @json($need->notes ?: '-')
            ],
        @endforeach
    ];

    const csv = rows
        .map(row => row.map(value => `"${String(value).replaceAll('"', '""')}"`).join(','))
        .join('\n');
    
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
