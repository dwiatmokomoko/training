@extends('layouts.app')

@section('title', 'Manajemen Pengguna - Sistem TNA')
@section('page-title', 'Manajemen Pengguna')
@section('page-subtitle', 'Akun, role, hak akses, aktivasi, reset password, dan audit log')

@section('content')
@php
    $roles = [
        ['role' => 'Admin', 'focus' => 'Mengelola seluruh konfigurasi sistem, user, master data, dan proses TNA.', 'access' => ['Semua modul', 'Manajemen pengguna', 'Master data', 'Analisis SAW', 'Laporan']],
        ['role' => 'Petugas Kepegawaian', 'focus' => 'Menginput dan memelihara data pegawai, riwayat pelatihan, assessment, dan rekomendasi.', 'access' => ['Data pegawai', 'Riwayat pelatihan', 'Penilaian', 'Jalankan SAW', 'Kelola rekomendasi']],
        ['role' => 'Pimpinan/Ketua', 'focus' => 'Meninjau hasil analisis, menyetujui prioritas, dan membaca laporan tindak lanjut.', 'access' => ['Dashboard', 'Hasil analisis', 'Persetujuan', 'Laporan']],
    ];

    $users = \App\Models\User::orderBy('role')->orderBy('name')->get();
@endphp

<section class="module-hero">
    <div>
        <h3>Hak akses dibuat sesuai alur kerja TNA</h3>
        <p>Admin memegang konfigurasi penuh, petugas kepegawaian menginput data operasional termasuk riwayat pelatihan, sedangkan pimpinan/ketua fokus pada monitoring, persetujuan, dan laporan.</p>
    </div>
    <div class="module-hero-icon"><i class="fas fa-user-shield"></i></div>
</section>

<div class="module-grid two mb-4">
    <div class="module-card">
        <div class="module-card-head">
            <div class="module-card-icon"><i class="fas fa-users-gear"></i></div>
            <div>
                <h6>Data User</h6>
                <p>Akun diberi role dan daftar permission agar aksi input, analisis, approval, dan laporan bisa dibatasi sesuai tugas.</p>
            </div>
        </div>
        <ul class="feature-list">
            <li><i class="fas fa-check"></i><span>Admin, petugas kepegawaian, dan pimpinan/ketua.</span></li>
            <li><i class="fas fa-check"></i><span>Status akun aktif/nonaktif untuk menjaga akses aplikasi.</span></li>
            <li><i class="fas fa-check"></i><span>Permission disiapkan di model user dan bisa dipakai pada controller atau Blade.</span></li>
        </ul>
    </div>
    <div class="module-card">
        <div class="module-card-head">
            <div class="module-card-icon"><i class="fas fa-clipboard-list"></i></div>
            <div>
                <h6>Audit Log</h6>
                <p>Setiap aksi penting dicatat agar proses TNA lebih transparan dan mudah ditelusuri saat laporan diuji.</p>
            </div>
        </div>
        <ul class="feature-list">
            <li><i class="fas fa-check"></i><span>Login dan perubahan akun.</span></li>
            <li><i class="fas fa-check"></i><span>Input atau perubahan assessment.</span></li>
            <li><i class="fas fa-check"></i><span>Approval, penolakan, dan realisasi rencana pelatihan.</span></li>
        </ul>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Daftar User & Role</h5>
    </div>
    <div class="card-body table-responsive">
        <table class="soft-table js-data-table" data-page-length="10" data-order="[[2,&quot;asc&quot;]]">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="pill">{{ $user->role_label }}</span></td>
                        <td>
                            <span class="pill">
                                <i class="fas fa-{{ $user->is_active ? 'check' : 'times' }}"></i>
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">Belum ada user. Jalankan seeder untuk membuat akun awal.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Matriks Role & Hak Akses</h5>
    </div>
    <div class="card-body table-responsive">
        <table class="soft-table js-data-table" data-page-length="10" data-order="[[0,&quot;asc&quot;]]">
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Fokus Pengguna</th>
                    <th>Hak Akses Utama</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td><span class="pill">{{ $role['role'] }}</span></td>
                        <td>{{ $role['focus'] }}</td>
                        <td>
                            @foreach($role['access'] as $access)
                                <span class="pill me-1 mb-1">{{ $access }}</span>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
