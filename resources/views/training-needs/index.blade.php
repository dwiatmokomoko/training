@extends('layouts.app')

@section('title', 'Kebutuhan Pelatihan - Sistem TNA')
@section('page-title', 'Kebutuhan Pelatihan')
@section('page-subtitle', 'Hasil Analisis Simple Additive Weighting (SAW)')

@section('content')
<div class="toolbar-panel mb-4">
    <div>
        <h5 class="toolbar-title">Prioritas Pelatihan</h5>
        <p class="toolbar-subtitle">Jalankan analisis, tinjau ranking SAW, lalu kelola status persetujuan pelatihan.</p>
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
            Prioritas Kebutuhan Pelatihan
        </h5>
    </div>
    <div class="card-body">
        <livewire:training-needs-list />
    </div>
</div>
@endsection
