@extends('layouts.app')

@section('title', 'Kebutuhan Pelatihan - Sistem TNA')
@section('page-title', 'Kebutuhan Pelatihan')
@section('page-subtitle', 'Hasil Analisis Simple Additive Weighting (SAW)')

@section('content')
<div class="row mb-3">
    <div class="col-md-6">
        <form action="{{ route('run-analysis') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-calculator me-2"></i>
                Jalankan Analisis SAW
            </button>
        </form>
        <a href="{{ route('training-needs.report') }}" class="btn btn-success ms-2">
            <i class="fas fa-download me-2"></i>
            Unduh Laporan
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