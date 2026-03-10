@extends('layouts.app')

@section('title', 'Data Pegawai - Sistem TNA')
@section('page-title', 'Data Pegawai')
@section('page-subtitle', 'Manajemen Data Pegawai Pengadilan Negeri Sleman')

@section('content')
<div class="row mb-3">
    <div class="col-md-6">
        <a href="{{ route('employees.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Tambah Pegawai
        </a>
    </div>
    <div class="col-md-6">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Cari pegawai..." id="searchEmployee">
            <button class="btn btn-outline-secondary" type="button">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-users me-2"></i>
            Daftar Pegawai
        </h5>
    </div>
    <div class="card-body">
        @if($employees->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Pendidikan</th>
                        <th>Pengalaman</th>
                        <th>Email</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                    <tr>
                        <td>{{ $employee->nip }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div>
                                    <strong>{{ $employee->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $employee->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $employee->position->name }}</span>
                            <br>
                            <small class="text-muted">{{ ucfirst($employee->position->level) }}</small>
                        </td>
                        <td>{{ $employee->education_level }}</td>
                        <td>{{ $employee->work_experience }} tahun</td>
                        <td>{{ $employee->email }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
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
            {{ $employees->links() }}
        </div>
        @else
        <div class="text-center py-4">
            <i class="fas fa-users fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Belum ada data pegawai</h5>
            <p class="text-muted">Klik tombol "Tambah Pegawai" untuk menambahkan data pegawai baru.</p>
        </div>
        @endif
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-primary">{{ $employees->total() }}</h4>
                <small class="text-muted">Total Pegawai</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-info">{{ $employees->where('position.level', 'hakim')->count() }}</h4>
                <small class="text-muted">Hakim</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-success">{{ $employees->where('position.level', 'pegawai')->count() }}</h4>
                <small class="text-muted">Pegawai</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-warning">{{ number_format($employees->avg('work_experience'), 1) }}</h4>
                <small class="text-muted">Rata-rata Pengalaman</small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
}
</style>
@endpush