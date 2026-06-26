@extends('layouts.app')

@section('title', 'Alur Sistem - Sistem TNA')
@section('page-title', 'Alur Sistem')
@section('page-subtitle', 'Panduan mudah memahami proses Training Need Analysis')

@section('content')
@php
    $steps = [
        [
            'title' => 'Siapkan Data Dasar Organisasi',
            'short' => 'Fondasi sistem',
            'icon' => 'fa-sitemap',
            'plain' => 'Sistem perlu tahu struktur kantor terlebih dahulu: rumpun jabatan, unit kerja, dan jabatan pegawai.',
            'prepare' => ['Rumpun jabatan: Hakim, Kepaniteraan, Kesekretariatan', 'Unit kerja dan subbagian', 'Daftar jabatan yang berlaku di PN Sleman'],
            'result' => 'Pegawai dapat ditempatkan pada jabatan dan unit kerja yang benar.',
            'menu' => 'Master data / data awal sistem',
            'route' => null,
        ],
        [
            'title' => 'Input Data Pegawai',
            'short' => 'Siapa yang dinilai',
            'icon' => 'fa-users',
            'plain' => 'Masukkan profil pegawai agar sistem bisa membaca usia, masa jabatan, riwayat promosi, dan riwayat pelatihan.',
            'prepare' => ['NIP, nama, tanggal lahir, jenis kelamin', 'Jabatan saat ini dan unit kerja', 'TMT jabatan, tanggal promosi terakhir, tanggal pelatihan terakhir'],
            'result' => 'Sistem memiliki bahan untuk menghitung C2, C3, C4, dan C5.',
            'menu' => 'Data Pegawai',
            'route' => route('employees.index'),
        ],
        [
            'title' => 'Input Penilaian Kompetensi',
            'short' => 'Nilai kemampuan kerja',
            'icon' => 'fa-clipboard-check',
            'plain' => 'Atasan atau pengelola SDM memberikan nilai capaian kinerja berbasis kompetensi dengan skala 1 sampai 5.',
            'prepare' => ['Pilih pegawai', 'Isi nilai setiap kriteria', 'Tambahkan catatan bila ada hal penting'],
            'result' => 'Sistem memiliki nilai C1 sebagai ukuran capaian kinerja berbasis kompetensi.',
            'menu' => 'Penilaian',
            'route' => route('assessments.index'),
        ],
        [
            'title' => 'Jalankan Analisis SAW',
            'short' => 'Hitung prioritas',
            'icon' => 'fa-calculator',
            'plain' => 'Sistem mengolah semua data dengan metode Simple Additive Weighting untuk membuat ranking kebutuhan pelatihan.',
            'prepare' => ['C1 dari penilaian kompetensi', 'C2 dari lama tidak mengikuti pelatihan', 'C3 dari masa jabatan', 'C4 dari riwayat promosi', 'C5 dari usia pegawai'],
            'result' => 'Muncul daftar pegawai dan jenis pelatihan yang diprioritaskan.',
            'menu' => 'Kebutuhan Pelatihan',
            'route' => route('training-needs.index'),
        ],
        [
            'title' => 'Tindak Lanjut Rekomendasi',
            'short' => 'Persetujuan dan rencana',
            'icon' => 'fa-check-double',
            'plain' => 'Hasil ranking dapat ditinjau, disetujui, ditolak, atau ditandai selesai sesuai rencana pelatihan.',
            'prepare' => ['Cek ranking dan skor SAW', 'Baca rekomendasi pelatihan', 'Ubah status sesuai keputusan pimpinan/SDM'],
            'result' => 'Prioritas pelatihan berubah menjadi rencana tindak lanjut yang bisa dipantau.',
            'menu' => 'Detail Kebutuhan Pelatihan',
            'route' => route('training-needs.index'),
        ],
        [
            'title' => 'Cetak Laporan',
            'short' => 'Bahan keputusan',
            'icon' => 'fa-file-alt',
            'plain' => 'Laporan digunakan sebagai rekap kebutuhan pelatihan pegawai, unit kerja, dan bahan penyusunan rencana tahunan.',
            'prepare' => ['Laporan prioritas pelatihan', 'Status pending, disetujui, selesai, atau ditolak', 'Export CSV atau cetak PDF melalui browser'],
            'result' => 'Pimpinan dan SDM memiliki dasar objektif untuk menyusun program pelatihan.',
            'menu' => 'Laporan',
            'route' => route('training-needs.report'),
        ],
    ];

    $criteria = [
        ['code' => 'C1', 'name' => 'Capaian Kinerja Berbasis Kompetensi', 'simple' => 'Semakin rendah nilai kompetensi, semakin besar kebutuhan pelatihan.', 'type' => 'Cost', 'weight' => '33,3%', 'source' => 'Penilaian atasan langsung'],
        ['code' => 'C2', 'name' => 'Riwayat Pelatihan', 'simple' => 'Semakin lama tidak ikut pelatihan, semakin tinggi prioritas.', 'type' => 'Benefit', 'weight' => '26,7%', 'source' => 'Tanggal pelatihan terakhir'],
        ['code' => 'C3', 'name' => 'Masa Jabatan Saat Ini', 'simple' => 'Semakin lama di jabatan yang sama, semakin perlu penyegaran.', 'type' => 'Benefit', 'weight' => '20,0%', 'source' => 'TMT jabatan'],
        ['code' => 'C4', 'name' => 'Riwayat Promosi', 'simple' => 'Pegawai yang baru promosi perlu penyesuaian kompetensi.', 'type' => 'Benefit', 'weight' => '13,3%', 'source' => 'Tanggal promosi terakhir'],
        ['code' => 'C5', 'name' => 'Usia', 'simple' => 'Dipakai sebagai faktor pendukung perencanaan pengembangan.', 'type' => 'Cost', 'weight' => '6,7%', 'source' => 'Tanggal lahir'],
    ];

    $modules = [
        ['menu' => 'Dashboard', 'plain' => 'Ringkasan gap kompetensi, pegawai per level, prioritas pelatihan, status rencana/realisasi, dan notifikasi.', 'route' => route('dashboard'), 'icon' => 'fa-tachometer-alt'],
        ['menu' => 'Manajemen Pengguna', 'plain' => 'Akun pegawai, atasan, SDM, pimpinan, role RBAC, aktivasi, reset password, dan audit log.', 'route' => route('users-management'), 'icon' => 'fa-user-shield'],
        ['menu' => 'Data Pegawai', 'plain' => 'Profil NIP/NIK, jabatan, unit kerja, riwayat jabatan, pendidikan, pelatihan, dan dokumen pendukung.', 'route' => route('employees.index'), 'icon' => 'fa-users'],
        ['menu' => 'Jabatan & Standar Kompetensi', 'plain' => 'Master jabatan, kompetensi inti/manajerial/teknis/sosial kultural, level 1-5, dan bobot.', 'route' => route('positions-competencies'), 'icon' => 'fa-sitemap'],
        ['menu' => 'Penilaian Kinerja', 'plain' => 'Input nilai SKP, IKU, KPI, indikator per rumpun, dan pembobotan kinerja terhadap TNA.', 'route' => route('performance'), 'icon' => 'fa-clipboard-check'],
        ['menu' => 'Analisis TNA', 'plain' => 'Perbandingan kompetensi aktual vs standar, gap otomatis, klasifikasi wajib/prioritas/pengembangan, dan pemetaan individu/unit.', 'route' => route('training-needs.index'), 'icon' => 'fa-magnifying-glass-chart'],
        ['menu' => 'Rekomendasi Pelatihan', 'plain' => 'Jenis pelatihan, metode klasikal/e-learning/coaching, target peserta, estimasi waktu, urgensi, dan mapping gap.', 'route' => route('training-recommendations'), 'icon' => 'fa-graduation-cap'],
        ['menu' => 'Perencanaan Pelatihan', 'plain' => 'Rencana tahunan, jadwal kegiatan, peserta, estimasi anggaran, dan approval workflow pimpinan.', 'route' => route('training-plans'), 'icon' => 'fa-calendar-check'],
        ['menu' => 'Laporan', 'plain' => 'Laporan TNA per pegawai, jabatan/unit, rekap gap, rencana vs realisasi, export PDF/Excel.', 'route' => route('training-needs.report'), 'icon' => 'fa-chart-bar'],
        ['menu' => 'Master Data', 'plain' => 'Jenis kompetensi, level kompetensi, jenis pelatihan, metode pelatihan, dan tahun anggaran.', 'route' => route('master-data'), 'icon' => 'fa-database'],
    ];

    $importOrder = [
        'Master Rumpun Jabatan',
        'Master Unit Kerja',
        'Master Jabatan',
        'Master Pegawai',
        'Riwayat Jabatan',
        'Master Pelatihan',
        'Riwayat Pelatihan Pegawai',
        'Indikator Kinerja per Rumpun',
        'Periode Penilaian',
        'Penilaian Capaian Kinerja',
        'Master Kriteria SAW',
        'Bobot Kriteria',
        'Perhitungan SAW',
        'Ranking Prioritas Pelatihan',
        'Laporan dan Rekomendasi Pelatihan',
    ];

    $checklist = [
        'Semua pegawai sudah memiliki jabatan dan unit kerja.',
        'Tanggal lahir, TMT jabatan, promosi terakhir, dan pelatihan terakhir sudah diisi bila datanya ada.',
        'Penilaian kompetensi terbaru sudah dibuat untuk pegawai yang akan dianalisis.',
        'Analisis SAW sudah dijalankan ulang setelah ada perubahan data.',
        'Hasil ranking sudah diperiksa sebelum dicetak sebagai laporan.',
    ];
@endphp

<div class="system-flow-page">
    <section class="flow-hero mb-4">
        <div class="flow-hero-copy">
            <span class="eyebrow">Panduan untuk pengguna baru</span>
            <h3>Dari data pegawai menjadi prioritas pelatihan</h3>
            <p>
                Halaman ini menjelaskan cara kerja sistem TNA dengan bahasa sederhana. Intinya, sistem mengumpulkan data pegawai,
                menilai kompetensi, menghitung prioritas dengan SAW, lalu menghasilkan rekomendasi pelatihan.
            </p>
        </div>
        <div class="flow-hero-actions">
            <a href="{{ route('employees.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i>
                Mulai Input Pegawai
            </a>
            <a href="{{ route('training-needs.index') }}" class="btn btn-success">
                <i class="fas fa-calculator me-2"></i>
                Lihat Analisis
            </a>
        </div>
    </section>

    <div class="quick-explain mb-4">
        <div class="explain-card">
            <i class="fas fa-database"></i>
            <h6>Data Masuk</h6>
            <p>Profil pegawai, riwayat pelatihan, masa jabatan, promosi, usia, dan nilai kompetensi.</p>
        </div>
        <div class="explain-card">
            <i class="fas fa-scale-balanced"></i>
            <h6>Diproses SAW</h6>
            <p>Setiap kriteria diberi skor, dinormalisasi, lalu dikalikan bobot.</p>
        </div>
        <div class="explain-card">
            <i class="fas fa-ranking-star"></i>
            <h6>Hasil Keluar</h6>
            <p>Sistem menampilkan ranking pegawai dan rekomendasi pelatihan prioritas.</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-table-columns me-2"></i>
                10 Menu Utama Sesuai Rancangan
            </h5>
        </div>
        <div class="card-body">
            <div class="module-grid two">
                @foreach($modules as $index => $module)
                    <div class="module-card">
                        <div class="module-card-head">
                            <div class="module-card-icon"><i class="fas {{ $module['icon'] }}"></i></div>
                            <div>
                                <span class="pill mb-2">{{ $index + 1 }}</span>
                                <h6>{{ $module['menu'] }}</h6>
                                <p>{{ $module['plain'] }}</p>
                            </div>
                        </div>
                        <a href="{{ $module['route'] }}" class="btn btn-outline-primary btn-sm">
                            Buka Menu
                            <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="flow-roadmap">
                @foreach($steps as $index => $step)
                    <article class="roadmap-step">
                        <div class="roadmap-number">{{ $index + 1 }}</div>
                        <div class="roadmap-card">
                            <div class="roadmap-card-head">
                                <div class="roadmap-icon">
                                    <i class="fas {{ $step['icon'] }}"></i>
                                </div>
                                <div>
                                    <span>{{ $step['short'] }}</span>
                                    <h5>{{ $step['title'] }}</h5>
                                    <p>{{ $step['plain'] }}</p>
                                </div>
                            </div>

                            <div class="roadmap-detail-grid">
                                <div>
                                    <h6>Yang perlu disiapkan</h6>
                                    <ul>
                                        @foreach($step['prepare'] as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div>
                                    <h6>Hasil dari tahap ini</h6>
                                    <p>{{ $step['result'] }}</p>
                                    <div class="menu-chip">
                                        <i class="fas fa-location-dot"></i>
                                        {{ $step['menu'] }}
                                    </div>
                                </div>
                            </div>

                            @if($step['route'])
                                <a href="{{ $step['route'] }}" class="btn btn-outline-primary btn-sm">
                                    Buka Modul
                                    <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

        <div class="col-xl-4">
            <div class="sticky-guide">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-circle-question me-2"></i>
                            Istilah Sederhana
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="glossary-item">
                            <strong>SAW</strong>
                            <p>Metode untuk menjumlahkan nilai beberapa kriteria yang sudah diberi bobot.</p>
                        </div>
                        <div class="glossary-item">
                            <strong>Benefit</strong>
                            <p>Semakin besar nilainya, semakin tinggi prioritas.</p>
                        </div>
                        <div class="glossary-item">
                            <strong>Cost</strong>
                            <p>Dalam sistem ini dipakai untuk membaca kondisi yang perlu perhatian khusus.</p>
                        </div>
                        <div class="glossary-item">
                            <strong>Ranking</strong>
                            <p>Urutan pegawai dari prioritas pelatihan tertinggi ke terendah.</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i>
                            Checklist Sebelum Analisis
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="checklist">
                            @foreach($checklist as $item)
                                <div class="checklist-item">
                                    <i class="fas fa-check"></i>
                                    <span>{{ $item }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-weight-hanging me-2"></i>
                Kriteria Penilaian yang Dipakai Sistem
            </h5>
        </div>
        <div class="card-body">
            <div class="criteria-grid">
                @foreach($criteria as $item)
                    <div class="criteria-card">
                        <div class="criteria-topline">
                            <span class="criteria-code">{{ $item['code'] }}</span>
                            <span class="criteria-weight">{{ $item['weight'] }}</span>
                        </div>
                        <h6>{{ $item['name'] }}</h6>
                        <p>{{ $item['simple'] }}</p>
                        <div class="criteria-meta">
                            <span>{{ $item['type'] }}</span>
                            <span>{{ $item['source'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-file-import me-2"></i>
                Urutan Data Masuk sampai Laporan
            </h5>
        </div>
        <div class="card-body">
            <p class="section-subtitle mb-3">
                Urutan ini mengikuti template import database TNA SAW PN Sleman. Pengguna awam cukup membacanya sebagai tangga kerja: data organisasi dulu, pegawai dan riwayatnya, penilaian, baru perhitungan SAW dan laporan.
            </p>
            <div class="import-flow">
                @foreach($importOrder as $index => $item)
                    <div class="import-step">
                        <span>{{ $index + 1 }}</span>
                        <strong>{{ $item }}</strong>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
    .system-flow-page {
        --flow-soft: #f6fbf7;
    }

    .flow-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        padding: 1.5rem;
        border-radius: 12px;
        background: linear-gradient(135deg, #ffffff 0%, #edf7f0 100%);
        border: 1px solid var(--line);
        box-shadow: var(--shadow-sm);
    }

    .eyebrow {
        display: inline-flex;
        margin-bottom: 0.65rem;
        padding: 0.25rem 0.6rem;
        border-radius: 999px;
        background: var(--ma-light-yellow);
        color: #6f4d00;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .flow-hero h3 {
        margin: 0;
        font-weight: 800;
        color: var(--text-main);
    }

    .flow-hero p {
        margin: 0.65rem 0 0;
        color: var(--text-muted);
        max-width: 760px;
        line-height: 1.65;
    }

    .flow-hero-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        justify-content: flex-end;
        flex: 0 0 auto;
    }

    .quick-explain {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
    }

    .explain-card {
        background: white;
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: 1rem;
        box-shadow: var(--shadow-sm);
    }

    .explain-card i {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: var(--ma-light-green);
        color: var(--ma-dark-green);
        margin-bottom: 0.75rem;
    }

    .explain-card h6,
    .criteria-card h6 {
        margin: 0;
        font-weight: 800;
    }

    .explain-card p,
    .criteria-card p,
    .glossary-item p {
        margin: 0.4rem 0 0;
        color: var(--text-muted);
        line-height: 1.5;
    }

    .flow-roadmap {
        display: grid;
        gap: 1rem;
    }

    .roadmap-step {
        display: grid;
        grid-template-columns: 48px 1fr;
        gap: 1rem;
        position: relative;
    }

    .roadmap-step:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 23px;
        top: 56px;
        bottom: -16px;
        width: 2px;
        background: linear-gradient(180deg, var(--ma-green), var(--line));
    }

    .roadmap-number {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--ma-dark-green);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        box-shadow: var(--shadow-sm);
        position: relative;
        z-index: 1;
    }

    .roadmap-card {
        background: white;
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: 1rem;
        box-shadow: var(--shadow-sm);
    }

    .roadmap-card-head {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .roadmap-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: var(--ma-light-green);
        color: var(--ma-dark-green);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
    }

    .roadmap-card-head span {
        color: var(--ma-green);
        font-weight: 800;
        font-size: 0.82rem;
    }

    .roadmap-card-head h5 {
        margin: 0.15rem 0 0;
        font-weight: 800;
    }

    .roadmap-card-head p {
        margin: 0.35rem 0 0;
        color: var(--text-muted);
        line-height: 1.55;
    }

    .roadmap-detail-grid {
        display: grid;
        grid-template-columns: 1.25fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .roadmap-detail-grid h6 {
        font-weight: 800;
        margin-bottom: 0.45rem;
    }

    .roadmap-detail-grid ul {
        margin: 0;
        padding-left: 1.1rem;
        color: var(--text-main);
    }

    .roadmap-detail-grid li {
        margin-bottom: 0.3rem;
    }

    .roadmap-detail-grid p {
        margin: 0;
        color: var(--text-muted);
        line-height: 1.55;
    }

    .menu-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        margin-top: 0.75rem;
        padding: 0.35rem 0.65rem;
        border-radius: 999px;
        background: var(--flow-soft);
        color: var(--ma-dark-green);
        font-weight: 700;
        font-size: 0.86rem;
    }

    .sticky-guide {
        position: sticky;
        top: 1rem;
    }

    .glossary-item:not(:last-child) {
        padding-bottom: 0.85rem;
        margin-bottom: 0.85rem;
        border-bottom: 1px solid var(--line);
    }

    .checklist {
        display: grid;
        gap: 0.75rem;
    }

    .checklist-item {
        display: flex;
        gap: 0.65rem;
        align-items: flex-start;
    }

    .checklist-item i {
        margin-top: 0.15rem;
        color: var(--ma-green);
    }

    .criteria-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 1rem;
    }

    .criteria-card {
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: 1rem;
        background: var(--surface);
    }

    .criteria-topline {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .criteria-code {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 30px;
        border-radius: 8px;
        background: var(--ma-light-yellow);
        color: #6f4d00;
        font-weight: 800;
    }

    .criteria-weight {
        color: var(--ma-dark-green);
        font-weight: 800;
    }

    .criteria-meta {
        display: grid;
        gap: 0.35rem;
        margin-top: 0.9rem;
        color: var(--text-muted);
        font-size: 0.86rem;
    }

    .import-flow {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 0.75rem;
    }

    .import-step {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.75rem;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: white;
    }

    .import-step span {
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: var(--ma-light-green);
        color: var(--ma-dark-green);
        font-weight: 800;
        flex: 0 0 auto;
    }

    .import-step strong {
        font-size: 0.92rem;
        line-height: 1.3;
    }

    @media (max-width: 1199.98px) {
        .criteria-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .import-flow {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 991.98px) {
        .flow-hero,
        .flow-hero-actions {
            align-items: stretch;
            flex-direction: column;
        }

        .quick-explain {
            grid-template-columns: 1fr;
        }

        .sticky-guide {
            position: static;
        }

        .import-flow {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575.98px) {
        .flow-hero,
        .roadmap-card {
            padding: 1rem;
        }

        .roadmap-step {
            grid-template-columns: 38px 1fr;
            gap: 0.75rem;
        }

        .roadmap-number {
            width: 38px;
            height: 38px;
        }

        .roadmap-step:not(:last-child)::before {
            left: 18px;
            top: 46px;
        }

        .roadmap-card-head,
        .roadmap-detail-grid {
            grid-template-columns: 1fr;
        }

        .roadmap-card-head {
            flex-direction: column;
        }

        .criteria-grid {
            grid-template-columns: 1fr;
        }

        .import-flow {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
