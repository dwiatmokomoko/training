@extends('layouts.app')

@section('title', 'Penilaian - Sistem TNA')
@section('page-title', 'Penilaian Pegawai')
@section('page-subtitle', 'Manajemen Penilaian Berdasarkan Kriteria SAW')

@section('content')
<div class="row mb-3">
    <div class="col-md-6">
        <a href="{{ route('assessments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Tambah Penilaian
        </a>
        <a href="{{ route('assessments.bulk-create') }}" class="btn btn-success ms-2">
            <i class="fas fa-layer-group me-2"></i>
            Penilaian Bulk
        </a>
    </div>
    <div class="col-md-6">
        <div class="input-group">
            <select class="form-select" id="filterEmployee">
                <option value="">Semua Pegawai</option>
                @foreach(\App\Models\Employee::all() as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                @endforeach
            </select>
            <button class="btn btn-outline-secondary" type="button">
                <i class="fas fa-filter"></i>
            </button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-clipboard-check me-2"></i>
            Daftar Penilaian
        </h5>
    </div>
    <div class="card-body">
        @if($assessments->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Pegawai</th>
                        <th>Jumlah Kriteria</th>
                        <th>Total Skor</th>
                        <th>Tanggal Penilaian</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assessments as $assessment)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div>
                                    <strong>{{ $assessment->employee->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $assessment->employee->nip }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $assessment->scores->count() }} Kriteria</span>
                            <br>
                            <small class="text-muted">{{ $assessment->employee->position->name }}</small>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="progress mb-1" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ ($assessment->total_score * 20) }}%"></div>
                                </div>
                                <strong class="text-success">{{ number_format($assessment->total_score, 2) }}</strong>
                            </div>
                        </td>
                        <td>{{ $assessment->assessment_date->format('d/m/Y') }}</td>
                        <td>
                            @if($assessment->notes)
                                {{ Str::limit($assessment->notes, 30) }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('assessments.edit', $assessment) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('assessments.destroy', $assessment) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus penilaian ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
            {{ $assessments->links() }}
        </div>
        @else
        <div class="text-center py-4">
            <i class="fas fa-clipboard-check fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Belum ada data penilaian</h5>
            <p class="text-muted">Klik tombol "Tambah Penilaian" untuk menambahkan penilaian pegawai.</p>
        </div>
        @endif
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-primary">{{ $assessments->total() }}</h4>
                <small class="text-muted">Total Penilaian</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-success">{{ number_format($assessments->avg('total_score'), 2) }}</h4>
                <small class="text-muted">Rata-rata Skor</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-info">{{ $assessments->groupBy('employee_id')->count() }}</h4>
                <small class="text-muted">Pegawai Dinilai</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-warning">{{ \App\Models\Criteria::count() }}</h4>
                <small class="text-muted">Kriteria Aktif</small>
            </div>
        </div>
    </div>
</div>
@endsection