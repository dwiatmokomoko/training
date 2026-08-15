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
            'plain' => 'Masukkan profil pegawai sebagai identitas dan data pendukung laporan, termasuk jabatan, unit kerja, riwayat promosi, dan riwayat pelatihan.',
            'prepare' => ['NIP, nama, tanggal lahir, jenis kelamin', 'Jabatan saat ini dan unit kerja', 'TMT jabatan, tanggal promosi terakhir, tanggal pelatihan terakhir'],
            'result' => 'Sistem memiliki profil pegawai yang akan dipilih saat input assessment dan ditampilkan pada laporan TNA.',
            'menu' => 'Data Pegawai',
            'route' => route('employees.index'),
        ],
        [
            'title' => 'Input Penilaian Kompetensi',
            'short' => 'Nilai kemampuan kerja',
            'icon' => 'fa-clipboard-check',
            'plain' => 'Petugas mengisi skor manual untuk semua kriteria C1 sampai C5 sesuai skala nilai masing-masing.',
            'prepare' => ['Pilih pegawai', 'Isi C1 nilai capaian kinerja', 'Isi C2 riwayat pelatihan, C3 masa jabatan, C4 promosi, dan C5 usia sesuai skala', 'Tambahkan catatan bila ada hal penting'],
            'result' => 'Sistem menyimpan assessment lengkap dengan 5 kriteria untuk bahan perhitungan SAW.',
            'menu' => 'Penilaian',
            'route' => route('assessments.index'),
        ],
        [
            'title' => 'Jalankan Analisis SAW',
            'short' => 'Hitung prioritas',
            'icon' => 'fa-calculator',
            'plain' => 'Sistem mengolah data dengan metode Simple Additive Weighting berdasarkan rumpun jabatan, jenis pelatihan, dan periode semester yang dipilih.',
            'prepare' => ['Assessment lengkap C1 sampai C5', 'Skala nilai sesuai atribut benefit atau cost', 'Rumpun jabatan dan periode semester analisis'],
            'result' => 'Muncul daftar pegawai, jenis pelatihan prioritas, status kelayakan, dan hasil tersimpan per periode.',
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
        ['code' => 'C1', 'name' => 'Capaian Kinerja Berbasis Kompetensi', 'simple' => 'Nilai rendah menjadi skor prioritas lebih tinggi karena atribut cost.', 'type' => 'Cost', 'weight' => '33,3%', 'source' => 'Input manual assessment'],
        ['code' => 'C2', 'name' => 'Riwayat Pelatihan', 'simple' => 'Semakin lama tidak ikut pelatihan, semakin tinggi prioritas.', 'type' => 'Benefit', 'weight' => '26,7%', 'source' => 'Input manual assessment'],
        ['code' => 'C3', 'name' => 'Masa Jabatan Saat Ini', 'simple' => 'Semakin lama di jabatan yang sama, semakin perlu penyegaran.', 'type' => 'Benefit', 'weight' => '20,0%', 'source' => 'Input manual assessment'],
        ['code' => 'C4', 'name' => 'Riwayat Promosi', 'simple' => 'Pegawai yang baru promosi perlu penyesuaian kompetensi.', 'type' => 'Benefit', 'weight' => '13,3%', 'source' => 'Input manual assessment'],
        ['code' => 'C5', 'name' => 'Usia', 'simple' => 'Usia digunakan sebagai faktor pendukung prioritas sesuai skala cost.', 'type' => 'Cost', 'weight' => '6,7%', 'source' => 'Input manual assessment'],
    ];

    $ipoRows = [
        ['input' => 'Data pegawai, jabatan, unit kerja, dan riwayat pendukung', 'process' => 'Validasi profil dan pemetaan rumpun jabatan', 'output' => 'Profil pegawai siap dipilih pada assessment'],
        ['input' => 'Skor manual C1 sampai C5 pada form assessment', 'process' => 'Penyimpanan assessment lengkap 5 kriteria', 'output' => 'Matriks keputusan X siap dihitung'],
        ['input' => 'Atribut benefit/cost dan bobot preferensi', 'process' => 'Normalisasi benefit/cost dan perhitungan V', 'output' => 'Ranking prioritas, rekomendasi pelatihan, dan status kelayakan'],
    ];

    $systemFlow = [
        'Login pengguna',
        'Buka Data Pegawai',
        'Lengkapi riwayat jabatan dan pelatihan',
        'Input skor manual C1-C5',
        'Pilih rumpun, jenis pelatihan, dan periode',
        'Ambil kriteria dan bobot SAW',
        'Normalisasi nilai benefit/cost',
        'Hitung nilai V',
        'Urutkan ranking',
        'Simpan hasil per periode semester',
        'Tentukan Layak atau Cadangan',
        'Tampilkan rekomendasi dan laporan',
    ];

    $sawFormula = [
        ['name' => 'Benefit', 'formula' => 'rij = xij / max(xij)', 'desc' => 'Dipakai untuk C2, C3, dan C4. Nilai makin besar berarti prioritas makin tinggi.'],
        ['name' => 'Cost', 'formula' => 'rij = min(xij) / xij', 'desc' => 'Dipakai untuk C1 dan C5. Nilai yang perlu perhatian akan dibaca dalam konteks prioritas pelatihan.'],
        ['name' => 'Preferensi', 'formula' => 'Vi = SUM(Wj x rij)', 'desc' => 'Hasil akhir digunakan untuk menentukan ranking prioritas pelatihan pegawai.'],
    ];

    $modules = [
        ['menu' => 'Dashboard', 'plain' => 'Ringkasan gap kompetensi, pegawai per level, prioritas pelatihan, status rencana/realisasi, dan notifikasi.', 'route' => route('dashboard'), 'icon' => 'fa-tachometer-alt'],
        ['menu' => 'Manajemen Pengguna', 'plain' => 'Akun admin, petugas kepegawaian, pimpinan/ketua, role RBAC, status aktif, dan hak akses modul.', 'route' => route('users-management'), 'icon' => 'fa-user-shield'],
        ['menu' => 'Data Pegawai', 'plain' => 'Profil NIP/NIK, jabatan, unit kerja, riwayat jabatan, pendidikan, pelatihan, dan dokumen pendukung.', 'route' => route('employees.index'), 'icon' => 'fa-users'],
        ['menu' => 'Jabatan & Standar Kompetensi', 'plain' => 'Master jabatan, kompetensi inti/manajerial/teknis/sosial kultural, level 1-5, dan bobot.', 'route' => route('positions-competencies'), 'icon' => 'fa-sitemap'],
        ['menu' => 'Penilaian Kinerja', 'plain' => 'Input skor manual C1 sampai C5 sesuai skala nilai masing-masing kriteria sebagai bahan SAW.', 'route' => route('performance'), 'icon' => 'fa-clipboard-check'],
        ['menu' => 'Analisis TNA', 'plain' => 'Filter rumpun jabatan, jenis pelatihan, periode semester, proses SAW, pagination hasil, dan status kelayakan Layak/Cadangan.', 'route' => route('training-needs.index'), 'icon' => 'fa-magnifying-glass-chart'],
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
        'Penilaian Manual C1-C5',
        'Master Kriteria SAW',
        'Bobot Kriteria',
        'Perhitungan SAW',
        'Ranking Prioritas Pelatihan',
        'Laporan dan Rekomendasi Pelatihan',
    ];

    $checklist = [
        'Semua pegawai sudah memiliki jabatan dan unit kerja.',
        'Tanggal lahir, TMT jabatan, promosi terakhir, dan pelatihan terakhir sudah diisi bila datanya ada.',
        'Assessment terbaru sudah berisi 5 kriteria C1 sampai C5 untuk pegawai yang akan dianalisis.',
        'Rumpun jabatan, jenis pelatihan, dan periode semester sudah dipilih sebelum menjalankan SAW.',
        'Analisis SAW sudah dijalankan ulang untuk periode yang sesuai setelah ada perubahan data.',
        'Status kelayakan sudah dicek: nilai SAW di atas 0.9000 menjadi Layak, selain itu Cadangan.',
        'Hasil ranking sudah diperiksa sebelum dicetak sebagai laporan.',
    ];

    $roles = [
        [
            'role' => 'Admin',
            'focus' => 'Mengelola sistem secara penuh.',
            'permissions' => ['Semua modul', 'Manajemen pengguna', 'Master data', 'Data pegawai', 'Analisis SAW', 'Laporan'],
            'note' => 'Dipakai untuk konfigurasi, koreksi data lintas modul, dan pengelolaan akun.',
            'icon' => 'fa-user-shield',
        ],
        [
            'role' => 'Petugas Kepegawaian',
            'focus' => 'Menginput dan memelihara data operasional TNA.',
            'permissions' => ['Data pegawai', 'Riwayat pelatihan', 'Penilaian', 'Jalankan SAW', 'Kelola rekomendasi', 'Laporan'],
            'note' => 'Aktor utama untuk input riwayat pelatihan, assessment C1-C5, dan menjalankan analisis.',
            'icon' => 'fa-users-gear',
        ],
        [
            'role' => 'Pimpinan/Ketua',
            'focus' => 'Meninjau hasil dan mengambil keputusan.',
            'permissions' => ['Dashboard', 'Lihat pegawai', 'Lihat hasil TNA', 'Approve/Reject rekomendasi', 'Laporan'],
            'note' => 'Tidak menginput data; fokus pada monitoring, persetujuan, dan bahan keputusan.',
            'icon' => 'fa-stamp',
        ],
    ];

    $analysisPeriodFlow = [
        [
            'title' => 'Filter Data',
            'icon' => 'fa-filter',
            'plain' => 'Pengguna memilih rumpun jabatan, jenis pelatihan, dan periode semester. Tombol Tampilkan hanya meninjau hasil sesuai filter, tanpa mengubah data.',
        ],
        [
            'title' => 'Proses Analisis',
            'icon' => 'fa-calculator',
            'plain' => 'Petugas kepegawaian atau admin menjalankan SAW untuk periode terpilih. Jika rumpun dipilih, sistem hanya menghitung rumpun tersebut.',
        ],
        [
            'title' => 'Simpan Per Periode',
            'icon' => 'fa-database',
            'plain' => 'Hasil disimpan dengan tahun dan semester, misalnya 2026 Semester 2. Proses ulang mengganti data pada periode dan rumpun yang sama saja.',
        ],
        [
            'title' => 'Review Hasil',
            'icon' => 'fa-table-list',
            'plain' => 'Hasil tetap memakai pagination, menampilkan skor SAW, prioritas, rekomendasi pelatihan, status kelayakan, dan tindak lanjut.',
        ],
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
            @if(\App\Support\Access::allows('employees.manage'))
            <a href="{{ route('employees.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i>
                Mulai Input Pegawai
            </a>
            @endif
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
            <p>Profil pegawai dan assessment manual C1 sampai C5 sesuai skala nilai masing-masing kriteria.</p>
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
                <i class="fas fa-calendar-days me-2"></i>
                Alur Analisis TNA Per Periode
            </h5>
        </div>
        <div class="card-body">
            <p class="section-subtitle mb-3">
                Halaman Analisis TNA memakai filter server-side agar stabil di server. Hasil analisis tidak hanya ditampilkan sementara, tetapi disimpan berdasarkan tahun dan semester yang dipilih.
            </p>
            <div class="analysis-flow-grid mb-3">
                @foreach($analysisPeriodFlow as $index => $item)
                    <div class="analysis-flow-card">
                        <div class="analysis-step-mark">
                            <span>{{ $index + 1 }}</span>
                            <i class="fas {{ $item['icon'] }}"></i>
                        </div>
                        <h6>{{ $item['title'] }}</h6>
                        <p>{{ $item['plain'] }}</p>
                    </div>
                @endforeach
            </div>
            <div class="eligibility-rule">
                <div class="eligibility-copy">
                    <strong>Aturan Status Kelayakan</strong>
                    <span>Status ini membantu pimpinan memilah peserta utama dan peserta cadangan setelah ranking SAW terbentuk.</span>
                </div>
                <div class="eligibility-items">
                    <div class="eligibility-item is-eligible">
                        <strong>Layak</strong>
                        <span>Nilai SAW &gt; 0.9000</span>
                    </div>
                    <div class="eligibility-item is-reserve">
                        <strong>Cadangan</strong>
                        <span>Nilai SAW &lt;= 0.9000</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-user-lock me-2"></i>
                Aktor, Login, dan Hak Akses
            </h5>
        </div>
        <div class="card-body">
            <p class="section-subtitle mb-3">
                Setiap pengguna wajib login. Setelah login, sistem membaca role pengguna dan hanya menampilkan menu serta aksi yang sesuai kewenangannya.
            </p>
            <div class="role-flow-grid">
                @foreach($roles as $role)
                    <div class="role-flow-card">
                        <div class="role-flow-head">
                            <div class="role-flow-icon"><i class="fas {{ $role['icon'] }}"></i></div>
                            <div>
                                <h6>{{ $role['role'] }}</h6>
                                <p>{{ $role['focus'] }}</p>
                            </div>
                        </div>
                        <div class="role-permission-list">
                            @foreach($role['permissions'] as $permission)
                                <span>{{ $permission }}</span>
                            @endforeach
                        </div>
                        <small>{{ $role['note'] }}</small>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-diagram-project me-2"></i>
                Alur Sistem SAW
            </h5>
        </div>
        <div class="card-body">
            <p class="section-subtitle mb-3">
                Alur ini menunjukkan bagaimana sistem mengubah assessment manual C1 sampai C5 menjadi ranking kebutuhan pelatihan. Pengguna mengisi skor tiap kriteria, sedangkan normalisasi serta perangkingan dihitung otomatis oleh aplikasi.
            </p>

            <div class="saw-process-grid mb-4">
                <div class="saw-process-card">
                    <span>1</span>
                    <h6>Input Data</h6>
                    <p>Profil pegawai dan skor manual C1 sampai C5 pada form assessment.</p>
                </div>
                <div class="saw-process-card">
                    <span>2</span>
                    <h6>Matriks Keputusan X</h6>
                    <p>Sistem memakai skor assessment C1 sampai C5 untuk setiap pegawai sebagai alternatif yang akan dibandingkan.</p>
                </div>
                <div class="saw-process-card">
                    <span>3</span>
                    <h6>Normalisasi R</h6>
                    <p>Kriteria benefit memakai nilai dibagi maksimum, sedangkan cost memakai minimum dibagi nilai pegawai.</p>
                </div>
                <div class="saw-process-card">
                    <span>4</span>
                    <h6>Nilai V dan Ranking</h6>
                    <p>Nilai normalisasi dikalikan bobot, dijumlahkan, lalu diurutkan dari prioritas tertinggi.</p>
                </div>
            </div>

            <div class="flow-strip mb-4">
                @foreach($systemFlow as $index => $item)
                    <div class="flow-node">
                        <span>{{ $index + 1 }}</span>
                        <strong>{{ $item }}</strong>
                    </div>
                @endforeach
            </div>

            <div class="saw-formula-flow mb-4">
                @foreach($sawFormula as $formula)
                    <div class="formula-tile">
                        <small>{{ $formula['name'] }}</small>
                        <strong>{{ $formula['formula'] }}</strong>
                        <p>{{ $formula['desc'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="table-responsive data-table-shell">
                <table class="table table-hover align-middle js-data-table" data-page-length="10" data-order="[[0,&quot;asc&quot;]]">
                    <thead class="table-light">
                        <tr>
                            <th>Input</th>
                            <th>Proses Sistem</th>
                            <th>Output</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ipoRows as $row)
                            <tr>
                                <td>{{ $row['input'] }}</td>
                                <td>{{ $row['process'] }}</td>
                                <td>{{ $row['output'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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

    .saw-process-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
    }

    .role-flow-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
    }

    .role-flow-card {
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: 1rem;
        background: #fff;
        box-shadow: var(--shadow-sm);
    }

    .role-flow-head {
        display: flex;
        align-items: flex-start;
        gap: 0.85rem;
        margin-bottom: 0.85rem;
    }

    .role-flow-icon {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: var(--ma-light-green);
        color: var(--ma-dark-green);
        flex: 0 0 auto;
    }

    .role-flow-head h6 {
        margin: 0;
        font-weight: 850;
    }

    .role-flow-head p,
    .role-flow-card small {
        margin: 0.3rem 0 0;
        color: var(--text-muted);
        line-height: 1.5;
    }

    .role-permission-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        margin-bottom: 0.8rem;
    }

    .role-permission-list span {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.55rem;
        border-radius: 999px;
        background: var(--ma-light-green);
        color: var(--ma-dark-green);
        font-size: 0.8rem;
        font-weight: 750;
    }

    .analysis-flow-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
    }

    .analysis-flow-card {
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: 1rem;
        background: #fff;
        box-shadow: var(--shadow-sm);
    }

    .analysis-step-mark {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.8rem;
    }

    .analysis-step-mark span,
    .analysis-step-mark i {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        font-weight: 800;
    }

    .analysis-step-mark span {
        background: var(--ma-dark-green);
        color: #fff;
    }

    .analysis-step-mark i {
        background: var(--ma-light-yellow);
        color: #6f4d00;
    }

    .analysis-flow-card h6 {
        margin: 0;
        font-weight: 850;
        color: var(--text-main);
    }

    .analysis-flow-card p {
        margin: 0.45rem 0 0;
        color: var(--text-muted);
        line-height: 1.55;
    }

    .eligibility-rule {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: 1rem;
        background: var(--flow-soft);
    }

    .eligibility-copy strong {
        display: block;
        color: var(--text-main);
        font-weight: 850;
        margin-bottom: 0.25rem;
    }

    .eligibility-copy span {
        color: var(--text-muted);
    }

    .eligibility-items {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        justify-content: flex-end;
        flex: 0 0 auto;
    }

    .eligibility-item {
        min-width: 160px;
        border-radius: 10px;
        padding: 0.75rem 0.9rem;
        background: #fff;
        border: 1px solid var(--line);
    }

    .eligibility-item strong {
        display: block;
        font-weight: 850;
        margin-bottom: 0.2rem;
    }

    .eligibility-item span {
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .eligibility-item.is-eligible {
        border-color: rgba(0, 112, 72, 0.24);
    }

    .eligibility-item.is-eligible strong {
        color: var(--ma-green);
    }

    .eligibility-item.is-reserve {
        border-color: rgba(220, 160, 36, 0.34);
    }

    .eligibility-item.is-reserve strong {
        color: #946200;
    }

    .saw-process-card {
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: 1rem;
        background: #fbfdfb;
        min-height: 100%;
    }

    .saw-process-card span {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        background: var(--ma-dark-green);
        color: white;
        font-weight: 800;
        margin-bottom: 0.85rem;
    }

    .saw-process-card h6,
    .formula-tile strong {
        margin: 0;
        font-weight: 800;
        color: var(--text-main);
    }

    .saw-process-card p,
    .formula-tile p {
        margin: 0.45rem 0 0;
        color: var(--text-muted);
        line-height: 1.5;
    }

    .flow-strip {
        display: grid;
        grid-template-columns: repeat(9, minmax(120px, 1fr));
        gap: 0.65rem;
        overflow-x: auto;
        padding-bottom: 0.25rem;
    }

    .flow-node {
        position: relative;
        display: grid;
        gap: 0.5rem;
        min-width: 120px;
        padding: 0.75rem;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: white;
        box-shadow: var(--shadow-sm);
    }

    .flow-node span {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: var(--ma-light-green);
        color: var(--ma-dark-green);
        font-weight: 800;
    }

    .flow-node strong {
        font-size: 0.86rem;
        line-height: 1.35;
    }

    .saw-formula-flow {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
    }

    .formula-tile {
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 1rem;
        background: var(--flow-soft);
    }

    .formula-tile small {
        display: block;
        color: var(--ma-green);
        font-weight: 800;
        text-transform: uppercase;
        margin-bottom: 0.35rem;
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

        .saw-process-grid,
        .saw-formula-flow,
        .role-flow-grid,
        .analysis-flow-grid {
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

        .saw-process-grid,
        .saw-formula-flow,
        .role-flow-grid,
        .analysis-flow-grid {
            grid-template-columns: 1fr;
        }

        .eligibility-rule {
            align-items: stretch;
            flex-direction: column;
        }

        .eligibility-items {
            justify-content: stretch;
        }

        .eligibility-item {
            flex: 1 1 180px;
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
