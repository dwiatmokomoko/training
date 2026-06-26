@extends('layouts.app')

@section('title', 'Analisis TNA - Sistem TNA')
@section('page-title', 'Analisis Kebutuhan Pelatihan (TNA)')
@section('page-subtitle', 'Perbandingan kompetensi aktual, gap, klasifikasi, dan ranking SAW')

@section('content')
<div class="toolbar-panel mb-4">
    <div>
        <h5 class="toolbar-title">Analisis Gap dan Prioritas Pelatihan</h5>
        <p class="toolbar-subtitle">Jalankan analisis, tinjau gap kompetensi otomatis, lihat klasifikasi wajib/prioritas/pengembangan, lalu kelola status tindak lanjut.</p>
    </div>
    <div class="toolbar-actions">
        <form action="{{ route('run-analysis') }}" method="POST" class="d-inline" onsubmit="this.querySelector('button').disabled = true; this.querySelector('.btn-label').textContent = 'Menganalisis...';">
            @csrf
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-calculator me-2"></i>
                <span class="btn-label">Jalankan Analisis SAW</span>
            </button>
        </form>
        <a href="{{ route('training-needs.report') }}" class="btn btn-success">
            <i class="fas fa-download me-2"></i>
            Buka Laporan
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-square-root-variable me-2"></i>
            Tahapan Perhitungan SAW
        </h5>
    </div>
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
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-graduation-cap me-2"></i>
            Hasil Analisis TNA
        </h5>
    </div>
    <div class="card-body">
        <livewire:training-needs-list />
    </div>
</div>

<style>
    .saw-stage-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
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

    @media (max-width: 575.98px) {
        .saw-stage-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
