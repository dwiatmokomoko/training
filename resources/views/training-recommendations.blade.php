@extends('layouts.app')

@section('title', 'Rekomendasi Pelatihan - Sistem TNA')
@section('page-title', 'Rekomendasi Pelatihan')
@section('page-subtitle', 'Jenis pelatihan, metode, target peserta, urgensi, dan mapping gap kompetensi')

@section('content')
@php
    $catalog = \App\Support\TrainingCatalog::grouped();
    $methods = ['Klasikal', 'E-learning', 'Coaching', 'Bimbingan teknis', 'Sertifikasi'];
@endphp

<section class="module-hero">
    <div>
        <h3>Rekomendasi menghubungkan gap kompetensi dengan jenis pelatihan</h3>
        <p>Halaman ini memisahkan hasil analisis TNA menjadi rekomendasi yang mudah ditindaklanjuti: jenis pelatihan, metode, target peserta, estimasi waktu, urgensi, dan mapping pelatihan terhadap gap kompetensi.</p>
    </div>
    <div class="module-hero-icon"><i class="fas fa-graduation-cap"></i></div>
</section>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="module-card"><span class="pill mb-2">Rekomendasi</span><h3>{{ $summary['total_recommendations'] }}</h3><p>Total hasil rekomendasi dari analisis SAW.</p></div>
    </div>
    <div class="col-md-3">
        <div class="module-card"><span class="pill mb-2">Jenis</span><h3>{{ $summary['training_types'] }}</h3><p>Jenis pelatihan yang muncul dari hasil analisis.</p></div>
    </div>
    <div class="col-md-3">
        <div class="module-card"><span class="pill mb-2">Top 10</span><h3>{{ $summary['priority_participants'] }}</h3><p>Target peserta prioritas tinggi.</p></div>
    </div>
    <div class="col-md-3">
        <div class="module-card"><span class="pill mb-2">Urgensi</span><h3>{{ $summary['urgent'] }}</h3><p>Rekomendasi dengan catatan mendesak.</p></div>
    </div>
</div>

<div class="module-grid two mb-4">
    <div class="module-card">
        <div class="module-card-head">
            <div class="module-card-icon"><i class="fas fa-chalkboard-user"></i></div>
            <div>
                <h6>Metode Pelatihan</h6>
                <p>Dokumen meminta metode klasikal, e-learning, dan coaching. Sistem juga menampung bimtek dan sertifikasi.</p>
            </div>
        </div>
        @foreach($methods as $method)
            <span class="pill me-1 mb-1">{{ $method }}</span>
        @endforeach
    </div>
    <div class="module-card">
        <div class="module-card-head">
            <div class="module-card-icon"><i class="fas fa-link"></i></div>
            <div>
                <h6>Mapping Gap Kompetensi</h6>
                <p>Pelatihan teknis diarahkan ke gap teknis jabatan, pelatihan manajerial ke kebutuhan koordinasi/pimpinan, dan coaching untuk penyegaran cepat.</p>
            </div>
        </div>
        <ul class="feature-list">
            <li><i class="fas fa-check"></i><span>Teknis jabatan: SIPP, e-Court, putusan, minutasi, keuangan, BMN.</span></li>
            <li><i class="fas fa-check"></i><span>Manajerial: kepemimpinan, perencanaan, pengawasan, pengambilan keputusan.</span></li>
            <li><i class="fas fa-check"></i><span>Sosial kultural: pelayanan publik, komunikasi, integritas, disiplin.</span></li>
        </ul>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-certificate me-2"></i>Katalog Pelatihan dari Dokumen Sertifikasi</h5>
    </div>
    <div class="card-body">
        <div class="module-grid">
            @foreach($catalog as $group => $items)
                <div class="module-card">
                    <h6 class="mb-2">{{ $group }}</h6>
                    <ul class="feature-list">
                        @foreach($items as $item)
                            <li><i class="fas fa-check"></i><span>{{ $item }}</span></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-ranking-star me-2"></i>Ringkasan Rekomendasi dari Hasil SAW</h5>
    </div>
    <div class="card-body table-responsive">
        @if($groupedByTraining->count())
            <table class="soft-table js-data-table" data-page-length="10" data-order="[[3,&quot;desc&quot;]]">
                <thead>
                    <tr>
                        <th>Jenis Pelatihan</th>
                        <th>Target Peserta</th>
                        <th>Rumpun</th>
                        <th>Skor Rata-rata</th>
                        <th>Urgensi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groupedByTraining as $trainingType => $group)
                        <tr>
                            <td><strong>{{ $trainingType }}</strong></td>
                            <td>
                                <span class="pill">{{ $group['total'] }} rekomendasi</span>
                                @foreach($group['participants'] as $need)
                                    <div class="small text-muted mt-1">#{{ $need->priority_rank }} {{ $need->employee->name }}</div>
                                @endforeach
                            </td>
                            <td>
                                @foreach($group['families'] as $family)
                                    <span class="pill me-1 mb-1">{{ $family }}</span>
                                @endforeach
                            </td>
                            <td>{{ number_format($group['avg_score'], 4) }}</td>
                            <td>{{ $group['top_rank'] <= 10 ? 'Prioritas tinggi' : 'Terjadwal' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center py-4">
                <i class="fas fa-calculator fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Belum ada rekomendasi</h5>
                <p class="text-muted">Jalankan analisis SAW terlebih dahulu agar rekomendasi pelatihan muncul.</p>
                <form action="{{ route('run-analysis') }}" method="POST">
                    @csrf
                    <button class="btn btn-primary"><i class="fas fa-play me-2"></i>Jalankan Analisis SAW</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
