<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(['email' => 'admin@pn-sleman.go.id'], [
            'name' => 'Admin SDM PN Sleman',
            'role' => 'admin',
            'password' => 'password',
        ]);

        User::query()->updateOrCreate(['email' => 'kepegawaian@pn-sleman.go.id'], [
            'name' => 'Petugas Kepegawaian',
            'role' => 'kepegawaian',
            'password' => 'password',
        ]);

        User::query()->updateOrCreate(['email' => 'pimpinan@pn-sleman.go.id'], [
            'name' => 'Pimpinan PN Sleman',
            'role' => 'pimpinan',
            'password' => 'password',
        ]);

        $this->call(TnaSawSeeder::class);
    }
}
