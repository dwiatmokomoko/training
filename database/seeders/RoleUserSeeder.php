<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Admin TNA', 'email' => 'admin@pn-sleman.go.id', 'role' => User::ROLE_ADMIN],
            ['name' => 'Petugas Kepegawaian', 'email' => 'sdm@pn-sleman.go.id', 'role' => User::ROLE_PETUGAS_KEPEGAWAIAN],
            ['name' => 'Pimpinan Pengadilan', 'email' => 'pimpinan@pn-sleman.go.id', 'role' => User::ROLE_PIMPINAN],
        ] as $user) {
            User::updateOrCreate(['email' => $user['email']], [
                'name' => $user['name'],
                'password' => Hash::make('password'),
                'role' => $user['role'],
                'permissions' => User::ROLE_PERMISSIONS[$user['role']],
                'is_active' => true,
            ]);
        }
    }
}
