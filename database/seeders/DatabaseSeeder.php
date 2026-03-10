<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Position;
use App\Models\Criteria;
use App\Models\Employee;
use App\Models\Assessment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Positions
        $positions = [
            ['name' => 'Hakim', 'description' => 'Hakim Pengadilan Negeri', 'level' => 'hakim'],
            ['name' => 'Panitera', 'description' => 'Panitera Pengadilan', 'level' => 'pegawai'],
            ['name' => 'Jurusita', 'description' => 'Jurusita Pengadilan', 'level' => 'pegawai'],
            ['name' => 'Sekretaris', 'description' => 'Sekretaris Pengadilan', 'level' => 'pegawai'],
            ['name' => 'Staff Administrasi', 'description' => 'Staff Administrasi Umum', 'level' => 'pegawai'],
        ];

        foreach ($positions as $position) {
            Position::create($position);
        }

        // Seed Criteria
        $criteria = [
            ['name' => 'Kompetensi Teknis', 'description' => 'Kemampuan teknis dalam bidang kerja', 'weight' => 0.30, 'type' => 'benefit'],
            ['name' => 'Kinerja', 'description' => 'Penilaian kinerja pegawai', 'weight' => 0.25, 'type' => 'benefit'],
            ['name' => 'Pengalaman Kerja', 'description' => 'Lama pengalaman kerja', 'weight' => 0.20, 'type' => 'benefit'],
            ['name' => 'Pendidikan', 'description' => 'Tingkat pendidikan', 'weight' => 0.15, 'type' => 'benefit'],
            ['name' => 'Usia', 'description' => 'Usia pegawai', 'weight' => 0.10, 'type' => 'cost'],
        ];

        foreach ($criteria as $criterion) {
            Criteria::create($criterion);
        }

        // Seed Sample Employees
        $employees = [
            [
                'nip' => '196801011990031001',
                'name' => 'Dr. Ahmad Santoso, S.H., M.H.',
                'email' => 'ahmad.santoso@pn-sleman.go.id',
                'position_id' => 1,
                'education_level' => 'S3',
                'work_experience' => 25,
                'birth_date' => '1968-01-01',
                'gender' => 'L',
                'phone' => '081234567890'
            ],
            [
                'nip' => '197505101998032001',
                'name' => 'Siti Nurhaliza, S.H.',
                'email' => 'siti.nurhaliza@pn-sleman.go.id',
                'position_id' => 2,
                'education_level' => 'S1',
                'work_experience' => 20,
                'birth_date' => '1975-05-10',
                'gender' => 'P',
                'phone' => '081234567891'
            ],
            [
                'nip' => '198203151999031002',
                'name' => 'Budi Prasetyo, S.H.',
                'email' => 'budi.prasetyo@pn-sleman.go.id',
                'position_id' => 3,
                'education_level' => 'S1',
                'work_experience' => 15,
                'birth_date' => '1982-03-15',
                'gender' => 'L',
                'phone' => '081234567892'
            ],
            [
                'nip' => '198507202005032003',
                'name' => 'Dewi Sartika, S.Sos.',
                'email' => 'dewi.sartika@pn-sleman.go.id',
                'position_id' => 4,
                'education_level' => 'S1',
                'work_experience' => 12,
                'birth_date' => '1985-07-20',
                'gender' => 'P',
                'phone' => '081234567893'
            ],
            [
                'nip' => '199001052010031003',
                'name' => 'Eko Wijaya, S.Kom.',
                'email' => 'eko.wijaya@pn-sleman.go.id',
                'position_id' => 5,
                'education_level' => 'S1',
                'work_experience' => 8,
                'birth_date' => '1990-01-05',
                'gender' => 'L',
                'phone' => '081234567894'
            ]
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }

        // Seed Sample Assessments
        $employees = Employee::all();
        $criteria = Criteria::all();

        foreach ($employees as $employee) {
            foreach ($criteria as $criterion) {
                Assessment::create([
                    'employee_id' => $employee->id,
                    'criteria_id' => $criterion->id,
                    'score' => rand(60, 95), // Random score between 60-95
                    'assessment_date' => now()->subDays(rand(1, 30)),
                    'notes' => 'Assessment untuk ' . $employee->name . ' pada kriteria ' . $criterion->name
                ]);
            }
        }

        // Create admin user
        User::factory()->create([
            'name' => 'Admin TNA',
            'email' => 'admin@pn-sleman.go.id',
        ]);
    }
}
