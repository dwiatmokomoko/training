@extends('layouts.app')

@section('title', 'Master Data - Sistem TNA')
@section('page-title', 'Master Data')
@section('page-subtitle', 'Jenis kompetensi, level, pelatihan, metode, tahun anggaran, dan template import')

@section('content')
@php
    use App\Models\Criteria;
    use App\Models\JobFamily;
    use App\Models\Position;
    use App\Models\WorkUnit;

    $masters = [
        ['name' => 'Jenis Kompetensi', 'items' => ['Kompetensi inti', 'Manajerial', 'Teknis', 'Sosial kultural']],
        ['name' => 'Level Kompetensi', 'items' => ['1 Dasar', '2 Pemula', '3 Cukup', '4 Baik', '5 Mahir']],
        ['name' => 'Jenis Pelatihan', 'items' => ['Teknis yudisial', 'Teknis kepaniteraan', 'Manajerial', 'Administrasi umum', 'Sertifikasi']],
        ['name' => 'Metode Pelatihan', 'items' => ['Klasikal', 'E-learning', 'Coaching', 'Bimtek', 'Sertifikasi']],
        ['name' => 'Tahun Anggaran', 'items' => ['2026', '2027', '2028']],
    ];
    $importOrder = [
        '01_Rumpun', '02_Unit_Kerja', '03_Jabatan', '04_Pegawai', '05_Riwayat_Jabatan', '06_Pelatihan',
        '07_Riwayat_Pelatihan', '08_Indikator_Kinerja', '09_Kriteria_SAW', '10_Periode_Penilaian',
        '11_Penilaian_Kinerja', '12_Panduan_Konversi_SAW'
    ];
    $stats = [
        ['label' => 'Rumpun', 'value' => JobFamily::count()],
        ['label' => 'Unit Kerja', 'value' => WorkUnit::count()],
        ['label' => 'Jabatan', 'value' => Position::count()],
        ['label' => 'Kriteria SAW', 'value' => Criteria::latestTna()->count()],
    ];
@endphp

<section class="module-hero">
    <div>
        <h3>Master data menjaga perhitungan SAW tetap rapi</h3>
        <p>Template Excel meminta import data dilakukan bertahap dari rumpun, unit, jabatan, pegawai, riwayat, pelatihan, indikator, periode, sampai penilaian. Halaman ini menjadi peta kontrol data referensi.</p>
    </div>
    <div class="module-hero-icon"><i class="fas fa-database"></i></div>
</section>

<div class="row g-4 mb-4">
    @foreach($stats as $stat)
        <div class="col-md-3">
            <div class="module-card">
                <span class="pill mb-2">{{ $stat['label'] }}</span>
                <h3>{{ $stat['value'] }}</h3>
                <p>Data aktif dalam database aplikasi.</p>
            </div>
        </div>
    @endforeach
</div>

<div class="module-grid mb-4">
    @foreach($masters as $master)
        <div class="module-card">
            <div class="module-card-head">
                <div class="module-card-icon"><i class="fas fa-tags"></i></div>
                <div>
                    <h6>{{ $master['name'] }}</h6>
                    <p>Referensi yang digunakan modul TNA.</p>
                </div>
            </div>
            @foreach($master['items'] as $item)
                <span class="pill me-1 mb-1">{{ $item }}</span>
            @endforeach
        </div>
    @endforeach
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-file-import me-2"></i>Urutan Import Database TNA SAW PN Sleman</h5>
    </div>
    <div class="card-body">
        <div class="module-grid">
            @foreach($importOrder as $index => $sheet)
                <div class="module-card">
                    <div class="d-flex align-items-center gap-3">
                        <span class="pill">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <strong>{{ $sheet }}</strong>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="alert alert-success mt-4 mb-0">
            <i class="fas fa-circle-info me-2"></i>
            Format template menggunakan kode relasi seperti kode_rumpun, kode_unit, kode_jabatan, kode_pelatihan, kode_indikator, kode_periode, dan NIP agar aman untuk PostgreSQL.
        </div>
    </div>
</div>
@endsection
