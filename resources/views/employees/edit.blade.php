@extends('layouts.app')

@section('title', 'Edit Pegawai')
@section('page-title', 'Edit Pegawai')
@section('page-subtitle', 'Ubah data pegawai')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Form Edit Pegawai
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('employees.update', $employee) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nip" class="form-label">NIP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nip') is-invalid @enderror" 
                                       id="nip" name="nip" value="{{ old('nip', $employee->nip) }}" required>
                                @error('nip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $employee->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $employee->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="position_id" class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <select class="form-select @error('position_id') is-invalid @enderror" 
                                        id="position_id" name="position_id" required>
                                    <option value="">Pilih Jabatan</option>
                                    @foreach($positions as $position)
                                        <option value="{{ $position->id }}" 
                                                {{ old('position_id', $employee->position_id) == $position->id ? 'selected' : '' }}>
                                            {{ $position->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('position_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="birth_date" class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control @error('birth_date') is-invalid @enderror" 
                                       id="birth_date" name="birth_date" 
                                       value="{{ old('birth_date', $employee->birth_date ? $employee->birth_date->format('Y-m-d') : '') }}">
                                @error('birth_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="education_level" class="form-label">Tingkat Pendidikan</label>
                                <select class="form-select @error('education_level') is-invalid @enderror" 
                                        id="education_level" name="education_level">
                                    <option value="">Pilih Tingkat Pendidikan</option>
                                    <option value="SMA" {{ old('education_level', $employee->education_level) == 'SMA' ? 'selected' : '' }}>SMA</option>
                                    <option value="D3" {{ old('education_level', $employee->education_level) == 'D3' ? 'selected' : '' }}>D3</option>
                                    <option value="S1" {{ old('education_level', $employee->education_level) == 'S1' ? 'selected' : '' }}>S1</option>
                                    <option value="S2" {{ old('education_level', $employee->education_level) == 'S2' ? 'selected' : '' }}>S2</option>
                                    <option value="S3" {{ old('education_level', $employee->education_level) == 'S3' ? 'selected' : '' }}>S3</option>
                                </select>
                                @error('education_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="work_experience" class="form-label">Pengalaman Kerja (tahun)</label>
                                <input type="number" class="form-control @error('work_experience') is-invalid @enderror" 
                                       id="work_experience" name="work_experience" min="0" 
                                       value="{{ old('work_experience', $employee->work_experience) }}">
                                @error('work_experience')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('employees.show', $employee) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection