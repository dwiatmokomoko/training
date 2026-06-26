@extends('layouts.app')

@section('title', 'Alur Sistem - Sistem TNA')
@section('page-title', 'Alur Sistem')
@section('page-subtitle', 'Urutan kerja Sistem Informasi Training Need Analysis')

@section('content')
@php
    $phases = [
        [
            'title' => 'Master Organisasi',
            'icon' => 'fa-sitemap',
            'desc' => 'Menyiapkan fondasi data PN Sleman sebelum pegawai dan penilaian dimasukkan.',
            'items' => ['Rumpun jabatan: Hakim, Kepaniteraan, Kesekretariatan', 'Unit kerja dan subbagian', 'Jabatan dan relasi rumpun jabatan'],
            'route' => null,
        ],
        [
            'title' => 'Data Pegawai',
            'icon' => 'fa-users',
            'desc' => 'Mencatat profil pegawai sebagai sumber usia, unit kerja, jabatan, dan masa jabatan.',
            'items' => ['NIP, nama, tanggal lahir, pendidikan', 'Jabatan dan unit kerja', 'TMT jabatan, promosi terakhir, pelatihan terakhir'],
            'route' => route('employees.index'),
        ],
        [
            'title' => 'Penilaian Kompetensi',
            'icon' => 'fa-clipboard-check',
            'desc' => 'Memasukkan capaian kinerja berbasis kompetensi sebagai kriteria C1.',
            'items' => ['Assessment per pegawai', 'Skor 1 sampai 5 per kriteria', 'Catatan/verifikasi penilaian'],
            'route' => route('assessments.index'),
        ],
        [
            'title' => 'Perhitungan SAW',
            'icon' => 'fa-calculator',
            'desc' => 'Mengubah data pegawai dan assessment menjadi nilai prioritas kebutuhan pelatihan.',
            'items' => ['Normalisasi benefit dan cost', 'Pembobotan C1 sampai C5', 'Ranking prioritas pelatihan'],
            'route' => route('training-needs.index'),
        ],
        [
            'title' => 'Rekomendasi dan Laporan',
            'icon' => 'fa-chart-line',
            'desc' => 'Menindaklanjuti ranking menjadi rekomendasi pelatihan dan bahan rencana tahunan.',
            'items' => ['Rekomendasi jenis pelatihan', 'Status persetujuan dan realisasi', 'Laporan rekap dan export'],
            'route' => route('training-needs.report'),
        ],
    ];

    $criteria = [
        ['code' => 'C1', 'name' => 'Capaian Kinerja Berbasis Kompetensi', 'type' => 'Cost', 'weight' => '33,3%', 'source' => 'Penilaian atasan langsung'],
        ['code' => 'C2', 'name' => 'Riwayat Pelatihan', 'type' => 'Benefit', 'weight' => '26,7%', 'source' => 'Tanggal pelatihan terakhir'],
        ['code' => 'C3', 'name' => 'Masa Jabatan Saat Ini', 'type' => 'Benefit', 'weight' => '20,0%', 'source' => 'TMT jabatan'],
        ['code' => 'C4', 'name' => 'Riwayat Promosi', 'type' => 'Benefit', 'weight' => '13,3%', 'source' => 'Tanggal promosi terakhir'],
        ['code' => 'C5', 'name' => 'Usia', 'type' => 'Cost', 'weight' => '6,7%', 'source' => 'Tanggal lahir'],
    ];
@endphp

<div class="flow-page">
    <div class="toolbar-panel mb-4">
        <div>
            <h5 class="toolbar-title">Peta Kerja TNA</h5>
            <p class="toolbar-subtitle">Ikuti urutan ini agar data siap dihitung dengan metode Simple Additive Weighting.</p>
        </div>
        <div class="toolbar-actions">
            <a href="{{ route('employees.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i>
                Input Pegawai
            </a>
            <a href="{{ route('assessments.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>
                Input Penilaian
            </a>
        </div>
    </div>

    <div class="flow-steps mb-4">
        @foreach($phases as $index => $phase)
            <div class="flow-step">
                <div class="step-marker">
                    <span>{{ $index + 1 }}</span>
                </div>
                <div class="step-panel">
                    <div class="step-head">
                        <div class="step-icon">
                            <i class="fas {{ $phase['icon'] }}"></i>
                        </div>
                        <div>
                            <h5>{{ $phase['title'] }}</h5>
                            <p>{{ $phase['desc'] }}</p>
                        </div>
                    </div>
                    <ul>
                        @foreach($phase['items'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                    @if($phase['route'])
                        <a href="{{ $phase['route'] }}" class="btn btn-outline-primary btn-sm">
                            Buka Modul
                            <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-xl-7">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-weight-hanging me-2"></i>
                        Kriteria SAW yang Digunakan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Kriteria</th>
                                    <th>Atribut</th>
                                    <th>Bobot</th>
                                    <th>Sumber Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($criteria as $item)
                                    <tr>
                                        <td><span class="criteria-code">{{ $item['code'] }}</span></td>
                                        <td>{{ $item['name'] }}</td>
                                        <td>{{ $item['type'] }}</td>
                                        <td><strong>{{ $item['weight'] }}</strong></td>
                                        <td>{{ $item['source'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list-check me-2"></i>
                        Urutan Input Data
                    </h5>
                </div>
                <div class="card-body">
                    <div class="input-order">
                        @foreach(['Master rumpun jabatan', 'Master unit kerja', 'Master jabatan', 'Master pegawai', 'Riwayat jabatan dan pelatihan', 'Indikator/penilaian kompetensi', 'Master kriteria dan bobot SAW', 'Perhitungan SAW', 'Ranking prioritas', 'Laporan rekomendasi'] as $index => $item)
                            <div class="input-order-item">
                                <span>{{ $index + 1 }}</span>
                                <p>{{ $item }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .flow-steps {
        display: grid;
        gap: 1rem;
    }

    .flow-step {
        display: grid;
        grid-template-columns: 44px 1fr;
        gap: 1rem;
        position: relative;
    }

    .flow-step:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 21px;
        top: 48px;
        bottom: -16px;
        width: 2px;
        background: var(--line);
    }

    .step-marker {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: var(--ma-dark-green);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        position: relative;
        z-index: 1;
    }

    .step-panel {
        background: white;
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 1rem;
        box-shadow: var(--shadow-sm);
    }

    .step-head {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
        margin-bottom: 0.75rem;
    }

    .step-icon {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        background: var(--ma-light-green);
        color: var(--ma-dark-green);
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
    }

    .step-head h5 {
        margin: 0;
        font-weight: 700;
    }

    .step-head p {
        margin: 0.2rem 0 0;
        color: var(--text-muted);
    }

    .step-panel ul {
        margin: 0 0 1rem;
        padding-left: 1.2rem;
        color: var(--text-main);
    }

    .criteria-code {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 30px;
        border-radius: 8px;
        background: var(--ma-light-yellow);
        color: #6f4d00;
        font-weight: 700;
    }

    .input-order {
        display: grid;
        gap: 0.75rem;
    }

    .input-order-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: var(--surface-muted);
    }

    .input-order-item span {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--ma-green);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        flex: 0 0 auto;
    }

    .input-order-item p {
        margin: 0;
        font-weight: 600;
    }

    @media (max-width: 575.98px) {
        .flow-step {
            grid-template-columns: 36px 1fr;
            gap: 0.75rem;
        }

        .step-marker {
            width: 36px;
            height: 36px;
        }

        .flow-step:not(:last-child)::before {
            left: 17px;
            top: 40px;
        }
    }
</style>
@endsection
