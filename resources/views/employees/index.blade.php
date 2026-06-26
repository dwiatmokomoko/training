@extends('layouts.app')

@section('title', 'Data Pegawai - Sistem TNA')
@section('page-title', 'Data Pegawai')
@section('page-subtitle', 'Manajemen Data Pegawai Pengadilan Negeri Sleman')

@section('content')
<div class="toolbar-panel mb-4">
    <div>
        <h5 class="toolbar-title">Daftar Pegawai</h5>
        <p class="toolbar-subtitle">Kelola profil, unit kerja, masa jabatan, dan data pengembangan pegawai.</p>
    </div>
    <div class="toolbar-actions">
        <form action="{{ route('employees.index') }}" method="GET" class="toolbar-search">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIP, atau email">
                @if(request('search'))
                    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary" title="Reset pencarian">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
                <button class="btn btn-outline-primary" type="submit">Cari</button>
            </div>
        </form>
        <a href="{{ route('employees.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Tambah Pegawai
        </a>
    </div>
</div>

<div class="module-card mb-4">
    <div class="module-card-head">
        <div class="module-card-icon"><i class="fas fa-file-excel"></i></div>
        <div>
            <h6>Rujukan Format Data Pegawai PN Sleman</h6>
            <p>Halaman ini disesuaikan dengan file data pegawai: NIP, nama, jabatan, unit kerja, TMT jabatan, golongan, jenis kelamin, dan rumpun jabatan. NIP juga dapat menjadi dasar tanggal lahir untuk perhitungan usia pada C5 bila data lahir belum tersedia.</p>
        </div>
    </div>
    <div>
        @foreach(['NIP', 'Nama pegawai', 'Jabatan', 'Unit kerja', 'TMT jabatan', 'Golongan', 'Jenis kelamin', 'Rumpun'] as $column)
            <span class="pill me-1 mb-1">{{ $column }}</span>
        @endforeach
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
        <div class="table-responsive data-table-shell">
            <table class="table table-hover align-middle js-data-table" data-page-length="10" data-order="[[1,&quot;asc&quot;]]">
                <thead>
                    <tr>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Unit Kerja</th>
                        <th>Pendidikan</th>
                        <th>Masa Jabatan</th>
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
                            <small class="text-muted">{{ $employee->position->jobFamily?->name ?? ucfirst($employee->position->level) }}</small>
                        </td>
                        <td>{{ $employee->workUnit?->name ?? '-' }}</td>
                        <td>{{ $employee->education_level }}</td>
                        <td>{{ $employee->current_position_years }} tahun</td>
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

        @else
        <div class="text-center py-4">
            <i class="fas fa-users fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Belum ada data pegawai</h5>
            <p class="text-muted">
                @if(request('search'))
                    Tidak ada pegawai yang cocok dengan pencarian "{{ request('search') }}".
                @else
                    Klik tombol "Tambah Pegawai" untuk menambahkan data pegawai baru.
                @endif
            </p>
            @if(request('search'))
                <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>
                    Reset Pencarian
                </a>
            @endif
        </div>
        @endif
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-primary">{{ $employees->count() }}</h4>
                <small class="text-muted">Total Pegawai</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-info">{{ $employees->where('position.jobFamily.code', 'HK')->count() }}</h4>
                <small class="text-muted">Hakim</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-success">{{ $employees->where('position.jobFamily.code', 'KP')->count() }}</h4>
                <small class="text-muted">Kepaniteraan</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-warning">{{ $employees->where('position.jobFamily.code', 'KS')->count() }}</h4>
                <small class="text-muted">Kesekretariatan</small>
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
