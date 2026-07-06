@extends('layouts.app')

@section('title', 'Perencanaan Pelatihan - Sistem TNA')
@section('page-title', 'Perencanaan Pelatihan')
@section('page-subtitle', 'Rencana tahunan, jadwal, peserta, anggaran, dan persetujuan pimpinan')

@section('content')
@php
    use App\Models\TrainingNeed;
    $needs = TrainingNeed::with('employee.position')->orderBy('priority_rank')->take(12)->get();
    $statuses = [
        ['name' => 'Draft Rencana', 'desc' => 'SDM menyusun daftar pelatihan dari rekomendasi SAW.', 'icon' => 'fa-file-lines'],
        ['name' => 'Review Unit', 'desc' => 'Atasan mengecek urgensi unit kerja dan target peserta.', 'icon' => 'fa-user-check'],
        ['name' => 'Persetujuan Pimpinan', 'desc' => 'Pimpinan menyetujui rencana, jadwal, dan estimasi anggaran.', 'icon' => 'fa-stamp'],
        ['name' => 'Realisasi', 'desc' => 'Pelatihan dilaksanakan dan status peserta diperbarui.', 'icon' => 'fa-circle-check'],
    ];
    $quarters = [
        ['period' => 'Triwulan I', 'focus' => 'Pelatihan wajib dan prioritas tertinggi', 'budget' => '35%'],
        ['period' => 'Triwulan II', 'focus' => 'Teknis jabatan dan sertifikasi utama', 'budget' => '30%'],
        ['period' => 'Triwulan III', 'focus' => 'Pengembangan kompetensi dan coaching', 'budget' => '20%'],
        ['period' => 'Triwulan IV', 'focus' => 'Evaluasi, penyegaran, dan laporan realisasi', 'budget' => '15%'],
    ];
@endphp

<section class="module-hero">
    <div>
        <h3>Hasil SAW berubah menjadi rencana pengembangan tahunan</h3>
        <p>Modul ini mengikuti dokumen: rencana pelatihan tahunan, penjadwalan kegiatan, penetapan peserta, estimasi anggaran, dan persetujuan pimpinan melalui approval workflow.</p>
    </div>
    <div class="module-hero-icon"><i class="fas fa-calendar-check"></i></div>
</section>

<div class="module-grid mb-4">
    @foreach($statuses as $status)
        <div class="module-card">
            <div class="module-card-head">
                <div class="module-card-icon"><i class="fas {{ $status['icon'] }}"></i></div>
                <div>
                    <h6>{{ $status['name'] }}</h6>
                    <p>{{ $status['desc'] }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar-days me-2"></i>Kerangka Rencana Tahunan</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="soft-table">
                    <thead>
                        <tr>
                            <th>Periode</th>
                            <th>Fokus</th>
                            <th>Anggaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quarters as $quarter)
                            <tr>
                                <td><span class="pill">{{ $quarter['period'] }}</span></td>
                                <td>{{ $quarter['focus'] }}</td>
                                <td>{{ $quarter['budget'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-users-viewfinder me-2"></i>Calon Peserta dari Ranking SAW</h5>
            </div>
            <div class="card-body table-responsive">
                @if($needs->count())
                    <table class="soft-table">
                        <thead>
                            <tr>
                                <th>Prioritas</th>
                                <th>Pegawai</th>
                                <th>Pelatihan</th>
                                <th>Estimasi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($needs as $need)
                                <tr>
                                    <td><span class="pill">#{{ $need->priority_rank }}</span></td>
                                    <td>
                                        <strong>{{ $need->employee->name }}</strong>
                                        <div class="small text-muted">{{ $need->employee->position->name }}</div>
                                    </td>
                                    <td>{{ $need->training_type }}</td>
                                    <td>{{ $need->priority_rank <= 5 ? '1-2 bulan' : '3-6 bulan' }}</td>
                                    <td>{{ ucfirst($need->status) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada bahan rencana</h5>
                        <p class="text-muted">Jalankan analisis SAW untuk mengisi daftar calon peserta.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
