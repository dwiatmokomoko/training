<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\JobFamily;
use App\Models\WorkUnit;
use App\Models\Position;
use App\Models\Criteria;
use App\Models\Employee;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Seed master organisasi
        $jobFamilies = collect([
            ['code' => 'HK', 'name' => 'Hakim'],
            ['code' => 'KP', 'name' => 'Kepaniteraan'],
            ['code' => 'KS', 'name' => 'Kesekretariatan'],
        ])->mapWithKeys(fn ($family) => [
            $family['code'] => JobFamily::updateOrCreate(['code' => $family['code']], $family),
        ]);

        $kepaniteraan = WorkUnit::updateOrCreate(['name' => 'Kepaniteraan'], ['parent_id' => null]);
        $kesekretariatan = WorkUnit::updateOrCreate(['name' => 'Kesekretariatan'], ['parent_id' => null]);

        $workUnits = collect([
            ['name' => 'Majelis Hakim', 'parent_id' => $kepaniteraan->id],
            ['name' => 'Tenaga Teknis', 'parent_id' => $kepaniteraan->id],
            ['name' => 'Kepaniteraan Pidana', 'parent_id' => $kepaniteraan->id],
            ['name' => 'Kepaniteraan Perdata', 'parent_id' => $kepaniteraan->id],
            ['name' => 'Kepaniteraan Hukum', 'parent_id' => $kepaniteraan->id],
            ['name' => 'Subbagian Kepegawaian, Organisasi dan Tata Laksana', 'parent_id' => $kesekretariatan->id],
            ['name' => 'Subbagian Umum dan Keuangan', 'parent_id' => $kesekretariatan->id],
            ['name' => 'Subbagian Perencanaan, Teknologi Informasi dan Pelaporan', 'parent_id' => $kesekretariatan->id],
        ])->mapWithKeys(fn ($unit) => [
            $unit['name'] => WorkUnit::updateOrCreate(['name' => $unit['name']], $unit),
        ]);

        // 2. Seed Positions
        $positions = [
            ['name' => 'Hakim', 'job_family_id' => $jobFamilies['HK']->id, 'description' => 'Hakim Pengadilan Negeri', 'level' => 'hakim'],
            ['name' => 'Ketua', 'job_family_id' => $jobFamilies['HK']->id, 'description' => 'Ketua Pengadilan Negeri', 'level' => 'hakim'],
            ['name' => 'Wakil Ketua', 'job_family_id' => $jobFamilies['HK']->id, 'description' => 'Wakil Ketua Pengadilan Negeri', 'level' => 'hakim'],
            ['name' => 'Panitera', 'job_family_id' => $jobFamilies['KP']->id, 'description' => 'Panitera Pengadilan', 'level' => 'pegawai'],
            ['name' => 'Panitera Muda', 'job_family_id' => $jobFamilies['KP']->id, 'description' => 'Panitera Muda', 'level' => 'pegawai'],
            ['name' => 'Panitera Pengganti', 'job_family_id' => $jobFamilies['KP']->id, 'description' => 'Panitera Pengganti', 'level' => 'pegawai'],
            ['name' => 'Jurusita', 'job_family_id' => $jobFamilies['KP']->id, 'description' => 'Jurusita Pengadilan', 'level' => 'pegawai'],
            ['name' => 'Jurusita Pengganti', 'job_family_id' => $jobFamilies['KP']->id, 'description' => 'Jurusita Pengganti', 'level' => 'pegawai'],
            ['name' => 'Staf Kepaniteraan', 'job_family_id' => $jobFamilies['KP']->id, 'description' => 'Staf Kepaniteraan', 'level' => 'pegawai'],
            ['name' => 'Sekretaris', 'job_family_id' => $jobFamilies['KS']->id, 'description' => 'Sekretaris Pengadilan', 'level' => 'pegawai'],
            ['name' => 'Analis Kepegawaian', 'job_family_id' => $jobFamilies['KS']->id, 'description' => 'Analis Kepegawaian', 'level' => 'pegawai'],
            ['name' => 'Pranata Komputer', 'job_family_id' => $jobFamilies['KS']->id, 'description' => 'Pranata Komputer', 'level' => 'pegawai'],
            ['name' => 'Pengelola Keuangan', 'job_family_id' => $jobFamilies['KS']->id, 'description' => 'Pengelola Keuangan', 'level' => 'pegawai'],
            ['name' => 'Pengelola BMN', 'job_family_id' => $jobFamilies['KS']->id, 'description' => 'Pengelola Barang Milik Negara', 'level' => 'pegawai'],
            ['name' => 'Pengolah Data & Informasi', 'job_family_id' => $jobFamilies['KS']->id, 'description' => 'Pengolah Data dan Informasi', 'level' => 'pegawai'],
        ];

        foreach ($positions as $position) {
            Position::updateOrCreate(['name' => $position['name']], $position);
        }

        // 3. Seed Criteria SAW alternatif terbaru
        $criteria = [
            [
                'code' => 'C1',
                'name' => 'Penilaian Capaian Kinerja Berbasis Kompetensi',
                'description' => 'Nilai kinerja berbasis kompetensi dari atasan langsung. Skor rendah menunjukkan kebutuhan pelatihan lebih tinggi.',
                'weight' => 0.333,
                'type' => 'cost',
                'importance_rating' => 5,
            ],
            [
                'code' => 'C2',
                'name' => 'Riwayat Pelatihan',
                'description' => 'Lama tidak mengikuti pelatihan. Belum pernah atau lebih dari 5 tahun menjadi prioritas tertinggi.',
                'weight' => 0.267,
                'type' => 'benefit',
                'importance_rating' => 4,
            ],
            [
                'code' => 'C3',
                'name' => 'Masa Jabatan Saat Ini',
                'description' => 'Lama menduduki jabatan saat ini sebagai kebutuhan penyegaran kompetensi.',
                'weight' => 0.200,
                'type' => 'benefit',
                'importance_rating' => 3,
            ],
            [
                'code' => 'C4',
                'name' => 'Jenjang Jabatan/Riwayat Promosi',
                'description' => 'Kebutuhan penyesuaian kompetensi terhadap perubahan jabatan atau promosi.',
                'weight' => 0.133,
                'type' => 'benefit',
                'importance_rating' => 2,
            ],
            [
                'code' => 'C5',
                'name' => 'Usia',
                'description' => 'Faktor pendukung perencanaan pengembangan kompetensi. Pegawai lebih muda diprioritaskan untuk pengembangan.',
                'weight' => 0.067,
                'type' => 'cost',
                'importance_rating' => 1,
            ],
        ];

        foreach ($criteria as $criterion) {
            Criteria::updateOrCreate(['code' => $criterion['code']], $criterion);
        }

        // 4. Seed Sample Employees
        $employeesData = [
            [
                'nip' => '196801011990031001',
                'name' => 'Dr. Ahmad Santoso, S.H., M.H.',
                'email' => 'ahmad.santoso@pn-sleman.go.id',
                'position_id' => Position::where('name', 'Hakim')->value('id'),
                'work_unit_id' => $workUnits['Majelis Hakim']->id,
                'education_level' => 'S3',
                'work_experience' => 25,
                'current_position_start_date' => '2016-01-01',
                'last_promotion_date' => '2014-01-01',
                'last_training_date' => '2020-05-10',
                'birth_date' => '1968-01-01',
                'gender' => 'L',
                'phone' => '081234567890'
            ],
            [
                'nip' => '197505101998032001',
                'name' => 'Siti Nurhaliza, S.H.',
                'email' => 'siti.nurhaliza@pn-sleman.go.id',
                'position_id' => Position::where('name', 'Panitera')->value('id'),
                'work_unit_id' => $workUnits['Kepaniteraan Perdata']->id,
                'education_level' => 'S1',
                'work_experience' => 20,
                'current_position_start_date' => '2022-02-01',
                'last_promotion_date' => '2022-02-01',
                'last_training_date' => '2021-08-12',
                'birth_date' => '1975-05-10',
                'gender' => 'P',
                'phone' => '081234567891'
            ],
            [
                'nip' => '198203151999031002',
                'name' => 'Budi Prasetyo, S.H.',
                'email' => 'budi.prasetyo@pn-sleman.go.id',
                'position_id' => Position::where('name', 'Jurusita')->value('id'),
                'work_unit_id' => $workUnits['Tenaga Teknis']->id,
                'education_level' => 'S1',
                'work_experience' => 15,
                'current_position_start_date' => '2019-03-15',
                'last_promotion_date' => '2019-03-15',
                'last_training_date' => '2018-09-01',
                'birth_date' => '1982-03-15',
                'gender' => 'L',
                'phone' => '081234567892'
            ],
            [
                'nip' => '198507202005032003',
                'name' => 'Dewi Sartika, S.Sos.',
                'email' => 'dewi.sartika@pn-sleman.go.id',
                'position_id' => Position::where('name', 'Analis Kepegawaian')->value('id'),
                'work_unit_id' => $workUnits['Subbagian Kepegawaian, Organisasi dan Tata Laksana']->id,
                'education_level' => 'S1',
                'work_experience' => 12,
                'current_position_start_date' => '2024-01-10',
                'last_promotion_date' => '2024-01-10',
                'last_training_date' => '2023-11-03',
                'birth_date' => '1985-07-20',
                'gender' => 'P',
                'phone' => '081234567893'
            ],
            [
                'nip' => '199001052010031003',
                'name' => 'Eko Wijaya, S.Kom.',
                'email' => 'eko.wijaya@pn-sleman.go.id',
                'position_id' => Position::where('name', 'Pranata Komputer')->value('id'),
                'work_unit_id' => $workUnits['Subbagian Perencanaan, Teknologi Informasi dan Pelaporan']->id,
                'education_level' => 'S1',
                'work_experience' => 8,
                'current_position_start_date' => '2020-07-01',
                'last_promotion_date' => null,
                'last_training_date' => null,
                'birth_date' => '1990-01-05',
                'gender' => 'L',
                'phone' => '081234567894'
            ]
        ];

        foreach ($employeesData as $emp) {
            Employee::updateOrCreate(['nip' => $emp['nip']], $emp);
        }

        // 5. Seed Sample Assessments
        $employees = Employee::all();
        $criteria = Criteria::latestTna()->get();

        foreach ($employees as $employee) {
            $assessment = Assessment::updateOrCreate([
                'employee_id'     => $employee->id,
                'assessment_date' => now()->subDays(rand(1, 30))->toDateString(),
            ], [
                'total_score'     => 0,
                'notes'           => 'Penilaian capaian kinerja berbasis kompetensi untuk ' . $employee->name,
            ]);

            foreach ($criteria as $criterion) {
                AssessmentScore::updateOrCreate([
                    'assessment_id' => $assessment->id,
                    'criteria_id' => $criterion->id,
                ], [
                    'score' => rand(2, 5),
                ]);
            }

            $totalScore = $assessment->scores()->with('criteria')->get()
                ->sum(fn ($score) => $score->score * $score->criteria->weight);

            $assessment->update([
                'total_score' => $totalScore,
            ]);
        }

        // 6. Create admin user
        User::updateOrCreate([
            'email' => 'admin@pn-sleman.go.id',
        ], [
            'name' => 'Admin TNA',
            'password' => Hash::make('password'),
        ]);
    }
}
