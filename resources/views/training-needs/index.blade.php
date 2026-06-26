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
@endsection
