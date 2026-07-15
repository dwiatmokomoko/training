@extends('layouts.app')

@section('title', 'Penilaian Kinerja - Sistem TNA')
@section('page-title', 'Penilaian Kinerja')
@section('page-subtitle', 'Integrasi nilai SKP, IKU, KPI, dan penilaian capaian kompetensi')

@section('content')
@php
    use App\Models\Assessment;
    use App\Models\Employee;

    $totalEmployees = Employee::count();
    $assessedEmployees = Assessment::distinct('employee_id')->count('employee_id');
    $notAssessed = max($totalEmployees - $assessedEmployees, 0);
    $flows = [
        ['title' => 'Input Nilai SKP / IKU / KPI', 'desc' => 'Nilai kinerja dimasukkan oleh atasan atau SDM sebagai data pendukung C1.', 'icon' => 'fa-keyboard'],
        ['title' => 'Indikator per Rumpun', 'desc' => 'Hakim, kepaniteraan, dan kesekretariatan memakai indikator yang berbeda sesuai tugasnya.', 'icon' => 'fa-diagram-project'],
        ['title' => 'Pembobotan ke TNA', 'desc' => 'Nilai kinerja dikonversi menjadi skor C1 dan ikut dihitung dalam metode SAW.', 'icon' => 'fa-scale-balanced'],
        ['title' => 'Sinkronisasi Opsional', 'desc' => 'Sistem disiapkan agar bisa mengambil data dari aplikasi kepegawaian bila tersedia.', 'icon' => 'fa-arrows-rotate'],
    ];
    $scores = [
        ['range' => '91-100', 'label' => 'Sangat Baik', 'score' => '1'],
        ['range' => '81-90', 'label' => 'Baik', 'score' => '2'],
        ['range' => '71-80', 'label' => 'Cukup', 'score' => '3'],
        ['range' => '61-70', 'label' => 'Kurang', 'score' => '4'],
        ['range' => '<= 60', 'label' => 'Sangat Kurang', 'score' => '5'],
    ];
@endphp

<section class="module-hero">
    <div>
        <h3>Nilai kinerja menjadi dasar C1 dalam SAW</h3>
        <p>Dokumen menempatkan penilaian capaian kinerja berbasis kompetensi sebagai kriteria terpenting. Semakin rendah capaian kinerja, semakin tinggi kebutuhan pelatihan.</p>
    </div>
    <div class="module-hero-icon"><i class="fas fa-clipboard-check"></i></div>
</section>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="module-card">
            <span class="pill mb-2">Pegawai</span>
            <h3 class="mb-1">{{ $totalEmployees }}</h3>
            <p>Total pegawai dalam database.</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="module-card">
            <span class="pill mb-2">Sudah Dinilai</span>
            <h3 class="mb-1">{{ $assessedEmployees }}</h3>
            <p>Pegawai yang sudah memiliki assessment.</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="module-card">
            <span class="pill mb-2">Notifikasi</span>
            <h3 class="mb-1">{{ $notAssessed }}</h3>
            <p>Pegawai yang perlu diingatkan untuk assessment/verifikasi.</p>
        </div>
    </div>
</div>

<div class="module-card mb-4">
    <div class="module-card-head">
        <div class="module-card-icon"><i class="fas fa-arrow-right-to-bracket"></i></div>
        <div>
            <h6>Aksi Penilaian</h6>
            <p>Gunakan modul assessment yang sudah ada untuk input nilai pegawai satu per satu atau massal.</p>
        </div>
    </div>
    <div class="row g-2">
        <div class="col-md-4">
            <a href="{{ route('assessments.index') }}" class="btn btn-primary w-100">
                <i class="fas fa-list me-2"></i>Daftar Assessment
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('assessments.create') }}" class="btn btn-success w-100">
                <i class="fas fa-plus me-2"></i>Input Assessment
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('assessments.bulk-create') }}" class="btn btn-outline-primary w-100">
                <i class="fas fa-layer-group me-2"></i>Input Massal
            </a>
        </div>
    </div>
</div>

<div class="module-grid mb-4">
    @foreach($flows as $flow)
        <div class="module-card">
            <div class="module-card-head">
                <div class="module-card-icon"><i class="fas {{ $flow['icon'] }}"></i></div>
                <div>
                    <h6>{{ $flow['title'] }}</h6>
                    <p>{{ $flow['desc'] }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-table me-2"></i>Konversi Capaian Kinerja ke Skor C1</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="soft-table js-data-table" data-page-length="10" data-order="[[0,&quot;asc&quot;]]">
                    <thead>
                        <tr>
                            <th>Nilai Capaian</th>
                            <th>Kategori</th>
                            <th>Skor SAW</th>
                            <th>Makna</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scores as $row)
                            <tr>
                                <td>{{ $row['range'] }}</td>
                                <td>{{ $row['label'] }}</td>
                                <td><span class="pill">{{ $row['score'] }}</span></td>
                                <td>{{ $row['score'] >= 4 ? 'Prioritas pelatihan tinggi.' : 'Prioritas pelatihan lebih rendah.' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
