@extends('layouts.app')

@section('title', 'Jabatan & Standar Kompetensi - Sistem TNA')
@section('page-title', 'Jabatan & Standar Kompetensi')
@section('page-subtitle', 'Master jabatan, standar kompetensi, level 1-5, dan bobot kompetensi')

@section('content')
@php
    use App\Models\Position;
    use App\Models\JobFamily;

    $families = JobFamily::with('positions')->orderBy('code')->get();
    $competencyTypes = [
        ['name' => 'Kompetensi Inti', 'desc' => 'Integritas, disiplin, pelayanan, dan komitmen kerja sebagai aparatur peradilan.'],
        ['name' => 'Manajerial', 'desc' => 'Kemampuan mengelola pekerjaan, koordinasi, pengawasan, dan pengambilan keputusan.'],
        ['name' => 'Teknis', 'desc' => 'Kemampuan teknis sesuai jabatan, seperti putusan, SIPP, e-Court, keuangan, BMN, atau TI.'],
        ['name' => 'Sosial Kultural', 'desc' => 'Komunikasi, kerja sama, pelayanan publik, dan adaptasi lingkungan kerja.'],
    ];
    $levels = [
        ['level' => '1', 'label' => 'Dasar', 'desc' => 'Perlu pendampingan intensif.'],
        ['level' => '2', 'label' => 'Pemula', 'desc' => 'Mampu menjalankan tugas rutin sederhana.'],
        ['level' => '3', 'label' => 'Cukup', 'desc' => 'Mampu bekerja mandiri pada tugas umum.'],
        ['level' => '4', 'label' => 'Baik', 'desc' => 'Mampu menyelesaikan tugas kompleks dan membimbing.'],
        ['level' => '5', 'label' => 'Mahir', 'desc' => 'Menjadi rujukan dan mampu meningkatkan kualitas unit.'],
    ];
    $indicatorGroups = [
        'Hakim' => ['Kualitas putusan', 'Ketepatan penyelesaian perkara', 'Penguasaan hukum', 'Integritas dan disiplin'],
        'Kepaniteraan' => ['Ketepatan administrasi perkara', 'Penguasaan SIPP dan e-Court', 'Ketelitian dokumen perkara', 'Pelayanan kepada masyarakat'],
        'Kesekretariatan' => ['Ketepatan penyelesaian pekerjaan', 'Kualitas hasil kerja', 'Penguasaan aplikasi kerja', 'Disiplin dan kerja sama'],
    ];
@endphp

<section class="module-hero">
    <div>
        <h3>Standar kompetensi menjadi pembanding utama TNA</h3>
        <p>Dokumen meminta sistem membandingkan kompetensi aktual pegawai dengan standar jabatan. Halaman ini merangkum jabatan, rumpun, jenis kompetensi, level 1-5, dan indikator kinerja per rumpun.</p>
    </div>
    <div class="module-hero-icon"><i class="fas fa-sitemap"></i></div>
</section>

<div class="module-grid mb-4">
    @foreach($competencyTypes as $type)
        <div class="module-card">
            <div class="module-card-head">
                <div class="module-card-icon"><i class="fas fa-layer-group"></i></div>
                <div>
                    <h6>{{ $type['name'] }}</h6>
                    <p>{{ $type['desc'] }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>Master Jabatan per Rumpun</h5>
            </div>
            <div class="card-body">
                @forelse($families as $family)
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="mb-0">{{ $family->code }} - {{ $family->name }}</h6>
                            <span class="pill">{{ $family->positions->count() }} jabatan</span>
                        </div>
                        <div>
                            @foreach($family->positions as $position)
                                <span class="pill me-1 mb-1">{{ $position->name }}</span>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Master jabatan belum tersedia. Jalankan seeder database untuk mengisi data awal.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-signal me-2"></i>Level Kompetensi</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="soft-table">
                        <thead>
                            <tr>
                                <th>Level</th>
                                <th>Makna</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($levels as $level)
                                <tr>
                                    <td><span class="pill">{{ $level['level'] }} - {{ $level['label'] }}</span></td>
                                    <td>{{ $level['desc'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list-check me-2"></i>Indikator Kinerja Berbasis Kompetensi per Rumpun</h5>
    </div>
    <div class="card-body">
        <div class="module-grid">
            @foreach($indicatorGroups as $group => $items)
                <div class="module-card">
                    <div class="module-card-head">
                        <div class="module-card-icon"><i class="fas fa-bullseye"></i></div>
                        <div>
                            <h6>{{ $group }}</h6>
                            <p>Indikator ini menjadi dasar nilai C1.</p>
                        </div>
                    </div>
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
@endsection
