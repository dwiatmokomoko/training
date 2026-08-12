@extends('layouts.app')

@section('title', 'Analisis TNA - Sistem TNA')
@section('page-title', 'Analisis Kebutuhan Pelatihan (TNA)')
@section('page-subtitle', 'Perbandingan kompetensi aktual, gap, klasifikasi, dan ranking SAW')

@section('content')
<div class="toolbar-panel mb-4">
    <div>
        <h5 class="toolbar-title">Analisis Gap dan Prioritas Pelatihan</h5>
        <p class="toolbar-subtitle">Gunakan filter rumpun, jenis pelatihan, dan periode untuk meninjau hasil ranking SAW secara ringkas.</p>
    </div>
    <div class="toolbar-actions">
        <a href="{{ route('training-needs.report') }}" class="btn btn-success">
            <i class="fas fa-download me-2"></i>
            Buka Laporan
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-graduation-cap me-2"></i>
            Hasil Analisis TNA
        </h5>
    </div>
    <div class="card-body">
        @php
            $statusLabels = [
                'pending' => 'Pending',
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
                'completed' => 'Selesai',
            ];

            $statusClasses = [
                'pending' => 'status-pending',
                'approved' => 'status-approved',
                'rejected' => 'status-rejected',
                'completed' => 'status-completed',
            ];
        @endphp

        <div class="tna-simple">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <section class="simple-filter">
                <h6>Filter Data</h6>
                <form action="{{ route('training-needs.index') }}" method="GET">
                    <div class="filter-grid">
                        <label>
                            <span>Rumpun Jabatan</span>
                            <select name="job_family" class="form-select">
                                <option value="">Semua Rumpun</option>
                                @foreach($jobFamilies as $code => $label)
                                    <option value="{{ $code }}" @selected($filters['job_family'] === $code)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            <span>Jenis Pelatihan</span>
                            <select name="training_type" class="form-select">
                                <option value="">Semua</option>
                                @foreach($trainingTypes as $trainingType)
                                    <option value="{{ $trainingType }}" @selected($filters['training_type'] === $trainingType)>{{ $trainingType }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            <span>Periode</span>
                            <select name="period" class="form-select">
                                <option value="">Semua Periode</option>
                                @foreach($periods as $period)
                                    <option value="{{ $period['key'] }}" @selected($filters['period'] === $period['key'])>{{ $period['label'] }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-2"></i>
                            Tampilkan
                        </button>
                        @if(request()->hasAny(['job_family', 'training_type', 'period']))
                            <a href="{{ route('training-needs.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-rotate-left me-2"></i>
                                Reset
                            </a>
                        @endif
                    </div>
                </form>

                @if(\App\Support\Access::allows('analysis.run'))
                    <form action="{{ route('run-analysis') }}" method="POST" class="text-center mt-2" onsubmit="this.querySelector('button').disabled = true; this.querySelector('.btn-label').textContent = 'Memproses...';">
                        @csrf
                        <input type="hidden" name="period_year" value="{{ $periodYear }}">
                        <input type="hidden" name="period_semester" value="{{ $periodSemester }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-calculator me-2"></i>
                            <span class="btn-label">Proses Analisis {{ $periodYear }} Semester {{ $periodSemester }}</span>
                        </button>
                    </form>
                @endif
            </section>

            <section class="simple-summary">
                <div><span>Jumlah Pegawai</span><strong>{{ $summary['total_employees'] }}</strong></div>
                <div><span>Sudah Mengikuti Pelatihan</span><strong>{{ $summary['trained_employees'] }}</strong></div>
                <div><span>Belum Mengikuti Pelatihan</span><strong>{{ $summary['untrained_employees'] }}</strong></div>
                <div><span>Kuota Pelatihan</span><strong>{{ $summary['quota'] }} Orang</strong></div>
            </section>

            <section class="simple-group">
                <div class="group-title">
                    <h5>{{ $selectedGroupLabel }}</h5>
                    <span>{{ $trainingNeeds->total() }} rekomendasi pelatihan</span>
                </div>

                @if($trainingNeeds->count() > 0)
                    <div class="table-responsive simple-table-shell">
                        <table class="table align-middle simple-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Pegawai</th>
                                    <th>Jabatan</th>
                                    <th>Nilai SAW</th>
                                    <th>Prioritas</th>
                                    <th>Rekomendasi Pelatihan</th>
                                    <th>Status Kelayakan</th>
                                    <th>Tindak Lanjut</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trainingNeeds as $need)
                                    @php
                                        $priorityLabel = match (true) {
                                            (float) $need->saw_score >= 0.85 => 'Sangat Tinggi',
                                            (float) $need->saw_score >= 0.70 => 'Tinggi',
                                            (float) $need->saw_score >= 0.55 => 'Sedang',
                                            default => 'Rendah',
                                        };
                                    @endphp
                                    <tr>
                                        <td>{{ $trainingNeeds->firstItem() + $loop->index }}</td>
                                        <td>
                                            <strong>{{ $need->employee->name }}</strong>
                                            <small>{{ $need->employee->nip }}</small>
                                        </td>
                                        <td>{{ $need->employee->position->name }}</td>
                                        <td><strong>{{ number_format((float) $need->saw_score, 4) }}</strong></td>
                                        <td>{{ $priorityLabel }}</td>
                                        <td>{{ $need->training_type }}</td>
                                        <td>
                                            <span class="simple-status {{ $need->eligibility_label === 'Layak' ? 'status-eligible' : 'status-reserve' }}">
                                                {{ $need->eligibility_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="simple-status {{ $statusClasses[$need->status] ?? 'status-pending' }}">
                                                {{ $statusLabels[$need->status] ?? ucfirst($need->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="simple-actions">
                                                <a href="{{ route('training-needs.show', $need) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($need->status === 'pending' && \App\Support\Access::allows('training-needs.approve'))
                                                    <form action="{{ route('training-needs.update', $need) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Setujui">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('training-needs.update', $need) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Tolak">
                                                            <i class="fas fa-xmark"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($need->status === 'approved' && \App\Support\Access::allows('training-needs.manage'))
                                                    <form action="{{ route('training-needs.update', $need) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="completed">
                                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Selesai">
                                                            <i class="fas fa-flag-checkered"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if(\App\Support\Access::allows('training-needs.manage'))
                                                    <form action="{{ route('training-needs.destroy', $need) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="simple-pagination">
                        {{ $trainingNeeds->links() }}
                    </div>
                @else
                    <div class="simple-empty">
                        Belum ada hasil analisis sesuai filter. Jalankan proses analisis SAW atau ubah filter data.
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>

<details class="card mt-4 saw-details">
    <summary>
        <span>
            <i class="fas fa-square-root-variable me-2"></i>
            Tahapan Perhitungan SAW
        </span>
        <small>klik untuk tampil/sembunyikan</small>
    </summary>
    <div class="card-body">
            <div class="saw-stage-grid mb-4">
                <div class="saw-stage">
                    <span>1</span>
                    <strong>Kriteria dan Bobot</strong>
                    <p>C1-C5 disiapkan dengan atribut benefit/cost dan bobot preferensi.</p>
                </div>
                <div class="saw-stage">
                    <span>2</span>
                    <strong>Matriks Keputusan</strong>
                    <p>Setiap pegawai menjadi alternatif A1, A2, A3, dan seterusnya.</p>
                </div>
                <div class="saw-stage">
                    <span>3</span>
                    <strong>Normalisasi</strong>
                    <p>Benefit memakai x/max, cost memakai min/x sesuai metode SAW.</p>
                </div>
                <div class="saw-stage">
                    <span>4</span>
                    <strong>Preferensi dan Ranking</strong>
                    <p>Rumus V menjumlahkan hasil normalisasi yang sudah dikalikan bobot.</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-xl-5">
                    <div class="table-responsive data-table-shell h-100">
                        <div class="saw-mini-title">
                            <h6>Kriteria Perhitungan</h6>
                            <p>Bobot dan atribut yang dipakai sistem.</p>
                        </div>
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Kriteria</th>
                                    <th>Atribut</th>
                                    <th>Bobot</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($criteria as $criterion)
                                    <tr>
                                        <td class="fw-bold">{{ $criterion->code }}</td>
                                        <td>{{ $criterion->name }}</td>
                                        <td>{{ ucfirst($criterion->type) }}</td>
                                        <td>{{ number_format($criterion->weight, 3) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-xl-7">
                    <div class="table-responsive data-table-shell h-100">
                        <div class="saw-mini-title">
                            <h6>Preview Ranking dari Nilai V</h6>
                            <p>Menampilkan 5 alternatif teratas setelah normalisasi dan pembobotan.</p>
                        </div>
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Rank</th>
                                    <th>Alternatif</th>
                                    <th>Pegawai</th>
                                    <th>Perhitungan V</th>
                                    <th>Hasil</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($preferenceRows->take(5) as $index => $row)
                                    <tr>
                                        <td class="fw-bold">#{{ $index + 1 }}</td>
                                        <td>{{ $row['alternative'] }}</td>
                                        <td>{{ $row['employee']->name }}</td>
                                        <td><code>{{ $row['preference'] }} = {{ $row['formula'] }}</code></td>
                                        <td class="fw-bold text-primary">{{ number_format($row['score'], 4) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada hasil SAW. Jalankan analisis terlebih dahulu.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-3 d-flex flex-wrap gap-2">
                <a href="{{ route('training-needs.report') }}" class="btn btn-outline-primary">
                    <i class="fas fa-table me-2"></i>
                    Lihat Matriks X dan Normalisasi R
                </a>
                <a href="{{ route('system-flow') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-route me-2"></i>
                    Lihat Alur Sistem
                </a>
            </div>
    </div>
</details>

<style>
    .tna-simple {
        display: grid;
        gap: 1.5rem;
    }

    .simple-filter {
        padding-bottom: 1.25rem;
        border-bottom: 1px dashed var(--line);
    }

    .simple-filter h6 {
        margin: 0 0 1rem;
        font-weight: 800;
        color: var(--text-main);
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
    }

    .filter-grid label {
        display: grid;
        gap: 0.4rem;
    }

    .filter-grid span,
    .simple-summary span,
    .group-title span {
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .filter-actions {
        display: flex;
        justify-content: center;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }

    .simple-summary {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.85rem;
        padding-bottom: 1.25rem;
        border-bottom: 1px dashed var(--line);
    }

    .simple-summary div {
        display: grid;
        gap: 0.25rem;
        padding: 0.8rem 0.9rem;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #fbfcfd;
    }

    .simple-summary strong {
        color: var(--text-main);
        font-size: 1.25rem;
    }

    .simple-group {
        display: grid;
        gap: 0.8rem;
    }

    .group-title h5 {
        margin: 0;
        font-weight: 800;
        color: var(--text-main);
    }

    .simple-table-shell {
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #fff;
    }

    .simple-table {
        margin: 0;
    }

    .simple-table th {
        background: #f8fafc;
        color: var(--text-muted);
        font-size: 0.82rem;
        text-transform: uppercase;
        border-bottom: 1px solid var(--line);
        white-space: nowrap;
    }

    .simple-table td {
        border-bottom: 1px solid #eef2f6;
    }

    .simple-table td small {
        display: block;
        color: var(--text-muted);
        margin-top: 0.15rem;
    }

    .simple-status {
        display: inline-flex;
        align-items: center;
        padding: 0.3rem 0.58rem;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .status-pending {
        background: #fff3cd;
        color: #7a4d00;
    }

    .status-approved,
    .status-eligible {
        background: var(--ma-light-green);
        color: var(--ma-dark-green);
    }

    .status-reserve {
        background: var(--ma-light-yellow);
        color: #6f4e00;
    }

    .status-rejected {
        background: #fde2e2;
        color: #991b1b;
    }

    .status-completed {
        background: #e0f2fe;
        color: #075985;
    }

    .simple-actions {
        display: inline-flex;
        gap: 0.25rem;
    }

    .simple-actions form {
        margin: 0;
    }

    .simple-empty {
        border: 1px dashed var(--line);
        border-radius: 8px;
        padding: 1.5rem;
        color: var(--text-muted);
        text-align: center;
        background: #fbfcfd;
    }

    .simple-pagination {
        display: flex;
        justify-content: flex-end;
        margin-top: 0.75rem;
    }

    .saw-stage-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
    }

    .saw-details summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem;
        cursor: pointer;
        border-bottom: 1px solid transparent;
        list-style: none;
    }

    .saw-details summary::-webkit-details-marker {
        display: none;
    }

    .saw-details[open] summary {
        border-bottom-color: var(--line);
    }

    .saw-details summary span {
        font-weight: 800;
        color: var(--text-main);
    }

    .saw-details summary small {
        color: var(--text-muted);
        font-weight: 600;
    }

    .saw-stage {
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 1rem;
        background: #f8fbf9;
    }

    .saw-stage span {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: var(--ma-dark-green);
        color: white;
        font-weight: 800;
        margin-bottom: 0.75rem;
    }

    .saw-stage strong,
    .saw-mini-title h6 {
        display: block;
        margin: 0;
        font-weight: 800;
        color: var(--text-main);
    }

    .saw-stage p,
    .saw-mini-title p {
        margin: 0.35rem 0 0;
        color: var(--text-muted);
        line-height: 1.5;
    }

    .saw-mini-title {
        margin-bottom: 0.75rem;
    }

    @media (max-width: 1199.98px) {
        .saw-stage-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 991.98px) {
        .filter-grid,
        .simple-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575.98px) {
        .filter-grid,
        .simple-summary {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            flex-direction: column;
        }

        .filter-actions .btn {
            width: 100%;
        }

        .saw-stage-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
