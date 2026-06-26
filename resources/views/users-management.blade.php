@extends('layouts.app')

@section('title', 'Manajemen Pengguna - Sistem TNA')
@section('page-title', 'Manajemen Pengguna')
@section('page-subtitle', 'Akun, role, hak akses, aktivasi, reset password, dan audit log')

@section('content')
@php
    $roles = [
        ['role' => 'Pegawai', 'focus' => 'Melihat profil, mengisi assessment, melihat rekomendasi pribadi', 'access' => ['Profil pegawai', 'Assessment mandiri', 'Riwayat pelatihan']],
        ['role' => 'Atasan', 'focus' => 'Memverifikasi nilai assessment dan memberi catatan kebutuhan unit', 'access' => ['Verifikasi assessment', 'Penilaian indikator', 'Catatan urgensi']],
        ['role' => 'SDM', 'focus' => 'Mengelola master data, pegawai, pelatihan, dan proses SAW', 'access' => ['Data pegawai', 'Master data', 'Analisis SAW', 'Laporan']],
        ['role' => 'Pimpinan', 'focus' => 'Melihat ringkasan, menyetujui rencana pelatihan, dan memantau realisasi', 'access' => ['Dashboard', 'Approval rencana', 'Laporan pimpinan']],
    ];
@endphp

<section class="module-hero">
    <div>
        <h3>Hak akses dibuat sesuai alur kerja TNA</h3>
        <p>Modul ini menyiapkan struktur RBAC agar pegawai, atasan, SDM, dan pimpinan bekerja pada ruang akses yang tepat. Fitur ini mengikuti rancangan: data user, role, aktivasi/nonaktif akun, reset password, dan audit log.</p>
    </div>
    <div class="module-hero-icon"><i class="fas fa-user-shield"></i></div>
</section>

<div class="module-grid two mb-4">
    <div class="module-card">
        <div class="module-card-head">
            <div class="module-card-icon"><i class="fas fa-users-gear"></i></div>
            <div>
                <h6>Data User</h6>
                <p>Akun dihubungkan dengan data pegawai agar aktivitas assessment, verifikasi, dan persetujuan selalu jelas pemiliknya.</p>
            </div>
        </div>
        <ul class="feature-list">
            <li><i class="fas fa-check"></i><span>Pegawai, atasan langsung, SDM, dan pimpinan.</span></li>
            <li><i class="fas fa-check"></i><span>Status akun aktif/nonaktif untuk menjaga akses aplikasi.</span></li>
            <li><i class="fas fa-check"></i><span>Reset password disiapkan untuk bantuan administrator.</span></li>
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

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Matriks Role & Hak Akses</h5>
    </div>
    <div class="card-body table-responsive">
        <table class="soft-table">
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
