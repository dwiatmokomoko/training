<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\Criteria;
use App\Models\Employee;
use App\Models\JobFamily;
use App\Models\Position;
use App\Models\User;
use App\Models\WorkUnit;
use App\Services\SAWService;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $jobFamilies = $this->seedJobFamilies();
        $workUnits = $this->seedWorkUnits();
        $positions = $this->seedPositions($jobFamilies);
        $criteria = $this->seedCriteria();
        $employees = $this->seedEmployees($positions, $workUnits);

        Position::whereNotIn('name', $positions->keys()->all())->delete();

        $this->seedAssessments($employees, $criteria);
        $this->seedUsers();
        $this->seedTrainingNeeds();
    }

    private function seedJobFamilies()
    {
        return collect([
            ['code' => 'HK', 'name' => 'Hakim'],
            ['code' => 'KP', 'name' => 'Kepaniteraan'],
            ['code' => 'KS', 'name' => 'Kesekretariatan'],
        ])->mapWithKeys(fn ($family) => [
            $family['code'] => JobFamily::updateOrCreate(['code' => $family['code']], $family),
        ]);
    }

    private function seedWorkUnits()
    {
        $court = WorkUnit::updateOrCreate(['name' => 'Pengadilan Negeri Sleman'], ['parent_id' => null]);
        $kepaniteraan = WorkUnit::updateOrCreate(['name' => 'Kepaniteraan'], ['parent_id' => $court->id]);
        $kesekretariatan = WorkUnit::updateOrCreate(['name' => 'Kesekretariatan'], ['parent_id' => $court->id]);

        $units = collect([
            ['name' => 'Majelis Hakim', 'parent_id' => $court->id],
            ['name' => 'Tenaga Teknis', 'parent_id' => $kepaniteraan->id],
            ['name' => 'Kepaniteraan Pidana', 'parent_id' => $kepaniteraan->id],
            ['name' => 'Kepaniteraan Perdata', 'parent_id' => $kepaniteraan->id],
            ['name' => 'Kepaniteraan Hukum', 'parent_id' => $kepaniteraan->id],
            ['name' => 'Kepaniteraan Khusus Hak Asasi Manusia', 'parent_id' => $kepaniteraan->id],
            ['name' => 'Subbagian Kepegawaian, Organisasi dan Tata Laksana', 'parent_id' => $kesekretariatan->id],
            ['name' => 'Subbagian Umum dan Keuangan', 'parent_id' => $kesekretariatan->id],
            ['name' => 'Subbagian Perencanaan, Teknologi Informasi dan Pelaporan', 'parent_id' => $kesekretariatan->id],
        ])->mapWithKeys(fn ($unit) => [
            $unit['name'] => WorkUnit::updateOrCreate(['name' => $unit['name']], $unit),
        ]);

        return $units->merge([
            'Pengadilan Negeri Sleman' => $court,
            'Kepaniteraan' => $kepaniteraan,
            'Kesekretariatan' => $kesekretariatan,
        ]);
    }

    private function seedPositions($jobFamilies)
    {
        $positions = [
            ['name' => 'Ketua', 'family' => 'HK', 'description' => 'Ketua Pengadilan Negeri', 'level' => 'hakim'],
            ['name' => 'Wakil Ketua', 'family' => 'HK', 'description' => 'Wakil Ketua Pengadilan Negeri', 'level' => 'hakim'],
            ['name' => 'Hakim', 'family' => 'HK', 'description' => 'Hakim Pengadilan Negeri', 'level' => 'hakim'],
            ['name' => 'Panitera', 'family' => 'KP', 'description' => 'Panitera Pengadilan', 'level' => 'pegawai'],
            ['name' => 'Panitera Muda', 'family' => 'KP', 'description' => 'Panitera Muda', 'level' => 'pegawai'],
            ['name' => 'Panitera Pengganti', 'family' => 'KP', 'description' => 'Panitera Pengganti', 'level' => 'pegawai'],
            ['name' => 'Jurusita', 'family' => 'KP', 'description' => 'Jurusita Pengadilan', 'level' => 'pegawai'],
            ['name' => 'Jurusita Pengganti', 'family' => 'KP', 'description' => 'Jurusita Pengganti', 'level' => 'pegawai'],
            ['name' => 'Staf Kepaniteraan', 'family' => 'KP', 'description' => 'Staf administrasi teknis perkara', 'level' => 'pegawai'],
            ['name' => 'Sekretaris', 'family' => 'KS', 'description' => 'Sekretaris Pengadilan', 'level' => 'pegawai'],
            ['name' => 'Kepala Sub Bagian', 'family' => 'KS', 'description' => 'Kepala subbagian kesekretariatan', 'level' => 'pegawai'],
            ['name' => 'Analis Kepegawaian', 'family' => 'KS', 'description' => 'Analis kepegawaian dan SDM aparatur', 'level' => 'pegawai'],
            ['name' => 'Pranata Komputer', 'family' => 'KS', 'description' => 'Pranata komputer dan teknologi informasi', 'level' => 'pegawai'],
            ['name' => 'Pengelola Keuangan', 'family' => 'KS', 'description' => 'Pengelola keuangan APBN dan anggaran', 'level' => 'pegawai'],
            ['name' => 'Pengelola BMN', 'family' => 'KS', 'description' => 'Pengelola barang milik negara', 'level' => 'pegawai'],
            ['name' => 'Pengolah Data & Informasi', 'family' => 'KS', 'description' => 'Pengolah data, informasi, dan pelaporan', 'level' => 'pegawai'],
        ];

        return collect($positions)->mapWithKeys(function ($position) use ($jobFamilies) {
            $model = Position::updateOrCreate(['name' => $position['name']], [
                'job_family_id' => $jobFamilies[$position['family']]->id,
                'description' => $position['description'],
                'level' => $position['level'],
            ]);

            return [$position['name'] => $model];
        });
    }

    private function seedCriteria()
    {
        return collect([
            [
                'code' => 'C1',
                'name' => 'Penilaian Capaian Kinerja Berbasis Kompetensi',
                'description' => 'Nilai kinerja berbasis kompetensi dari atasan langsung. Nilai rendah menunjukkan kebutuhan pelatihan lebih tinggi.',
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
                'description' => 'Faktor pendukung perencanaan pengembangan kompetensi.',
                'weight' => 0.067,
                'type' => 'cost',
                'importance_rating' => 1,
            ],
        ])->mapWithKeys(fn ($criterion) => [
            $criterion['code'] => Criteria::updateOrCreate(['code' => $criterion['code']], $criterion),
        ]);
    }

    private function seedEmployees($positions, $workUnits)
    {
        $rows = collect($this->employeeRows());

        Employee::whereNotIn('nip', $rows->pluck('nip')->all())->delete();

        return $rows->map(function ($row, $index) use ($positions, $workUnits) {
            $positionName = $this->resolvePositionName($row['job']);
            $unitName = $this->resolveWorkUnitName($row['unit'], $positionName);
            $birthDate = $this->birthDateFromNip($row['nip']);
            $positionStart = $this->parseIndonesianDate($row['tmt']) ?? $this->appointmentDateFromNip($row['nip']);
            $lastTrainingDate = $this->trainingDateFor($index, $positionName);

            return Employee::updateOrCreate(['nip' => $row['nip']], [
                'name' => $row['name'],
                'email' => strtolower($row['nip']) . '@pn-sleman.go.id',
                'position_id' => $positions[$positionName]->id,
                'work_unit_id' => $workUnits[$unitName]->id,
                'education_level' => $this->educationLevelFromName($row['name']),
                'work_experience' => $this->workExperienceFromNip($row['nip']),
                'current_position_start_date' => $positionStart?->toDateString(),
                'last_promotion_date' => $positionStart?->toDateString(),
                'last_training_date' => $lastTrainingDate?->toDateString(),
                'birth_date' => $birthDate?->toDateString() ?? '1990-01-01',
                'gender' => str_contains(strtolower($row['gender']), 'perempuan') ? 'P' : 'L',
                'phone' => '08' . substr($row['nip'], -10),
                'address' => 'Pengadilan Negeri Sleman',
            ]);
        });
    }

    private function seedAssessments($employees, $criteria): void
    {
        $assessmentDate = Carbon::create(2026, 6, 16);

        Assessment::whereNotIn('employee_id', $employees->pluck('id')->all())->delete();

        foreach ($employees->values() as $index => $employee) {
            $assessment = Assessment::updateOrCreate([
                'employee_id' => $employee->id,
                'assessment_date' => $assessmentDate->toDateString(),
            ], [
                'total_score' => 0,
                'notes' => 'Penilaian awal berbasis indikator rumpun jabatan sesuai dokumen TNA PN Sleman.',
            ]);

            foreach ($criteria as $criterion) {
                AssessmentScore::updateOrCreate([
                    'assessment_id' => $assessment->id,
                    'criteria_id' => $criterion->id,
                ], [
                    'score' => $this->seedScoreFor($employee, $criterion->code, $index),
                ]);
            }

            $totalScore = $assessment->scores()->with('criteria')->get()
                ->sum(fn ($score) => $score->score * (float) $score->criteria->weight);

            $assessment->update(['total_score' => $totalScore]);
        }
    }

    private function seedUsers(): void
    {
        foreach ([
            ['name' => 'Admin SDM TNA', 'email' => 'admin@pn-sleman.go.id'],
            ['name' => 'Operator Kepegawaian', 'email' => 'sdm@pn-sleman.go.id'],
            ['name' => 'Atasan Langsung', 'email' => 'atasan@pn-sleman.go.id'],
            ['name' => 'Pimpinan Pengadilan', 'email' => 'pimpinan@pn-sleman.go.id'],
        ] as $user) {
            User::updateOrCreate(['email' => $user['email']], [
                'name' => $user['name'],
                'password' => Hash::make('password'),
            ]);
        }
    }

    private function seedTrainingNeeds(): void
    {
        $sawService = app(SAWService::class);
        $sawService->saveTrainingNeeds($sawService->calculateTrainingNeeds());
    }

    private function employeeRows(): array
    {
        return [
            ['nip' => '196906121996032000', 'name' => 'WARI JUNIATI, S.H.,M.H.', 'job' => 'Ketua Pengadilan Tingkat Pertama Klas IA', 'unit' => 'Pengadilan Negeri Sleman', 'tmt' => '05 Mei 2023', 'gender' => 'Perempuan'],
            ['nip' => '197203072000031000', 'name' => 'AGUNG NUGROHO, S.H.,M.H', 'job' => 'Wakil Ketua Tingkat Pertama', 'unit' => 'Pengadilan Negeri Sleman', 'tmt' => '29 Desember 2023', 'gender' => 'Laki-Laki'],
            ['nip' => '197908222005022000', 'name' => 'YUYUN FITHRIYAH, SE, Ak', 'job' => 'Sekretaris Tingkat Pertama Klas IA', 'unit' => 'Sekretaris', 'tmt' => '45411', 'gender' => 'Perempuan'],
            ['nip' => '196905101994031000', 'name' => 'HERI HARJANTO, S.H.', 'job' => 'Panitera Tingkat Pertama Klas IA', 'unit' => 'Panitera', 'tmt' => '23 Agustus 2025', 'gender' => 'Laki-Laki'],
            ['nip' => '197611252001121000', 'name' => 'ARIEF WINARSO, S.H.', 'job' => 'Hakim Tingkat Pertama', 'unit' => 'Pengadilan Negeri Sleman', 'tmt' => '13 Oktober 2025', 'gender' => 'Laki-Laki'],
            ['nip' => '197209131999032000', 'name' => 'IRMA WAHYUNINGSIH, S.H.,M.H', 'job' => 'Hakim Tingkat Pertama', 'unit' => 'Pengadilan Negeri Sleman', 'tmt' => '09 Januari 2023', 'gender' => 'Perempuan'],
            ['nip' => '197610292001121000', 'name' => 'ARI PRABAWA, S.H., M.H.', 'job' => 'Hakim Tingkat Pertama', 'unit' => 'Pengadilan Negeri Sleman', 'tmt' => '13 Oktober 2025', 'gender' => 'Laki-Laki'],
            ['nip' => '197901092000122000', 'name' => 'EKA RATNAWIDIASTUTI, S.H., M.Hum.', 'job' => 'Hakim Tingkat Pertama', 'unit' => 'Pengadilan Negeri Sleman', 'tmt' => '13 Oktober 2025', 'gender' => 'Perempuan'],
            ['nip' => '197108181993031000', 'name' => 'SURYODIYONO, S.H.', 'job' => 'Hakim Tingkat Pertama', 'unit' => 'Pengadilan Negeri Sleman', 'tmt' => '28 Februari 2023', 'gender' => 'Laki-Laki'],
            ['nip' => '197609052001121000', 'name' => 'IMRON ROSYADI, S.H.', 'job' => 'Hakim Tingkat Pertama', 'unit' => 'Pengadilan Negeri Sleman', 'tmt' => '16 Maret 2026', 'gender' => 'Laki-Laki'],
            ['nip' => '198010082003122000', 'name' => 'RESA OKTARIA, S.H., M.H.', 'job' => 'Hakim Tingkat Pertama', 'unit' => 'Pengadilan Negeri Sleman', 'tmt' => '13 Oktober 2025', 'gender' => 'Perempuan'],
            ['nip' => '197901202007041000', 'name' => 'JAYADI HUSAIN, S.H., M.H.', 'job' => 'Hakim Tingkat Pertama', 'unit' => 'Pengadilan Negeri Sleman', 'tmt' => '13 Oktober 2025', 'gender' => 'Laki-Laki'],
            ['nip' => '197603302003121000', 'name' => 'ARIE HAZAIRIN, S.H., M.H.', 'job' => 'Hakim Tingkat Pertama', 'unit' => 'Pengadilan Negeri Sleman', 'tmt' => '30 Oktober 2025', 'gender' => 'Laki-Laki'],
            ['nip' => '197611242005022000', 'name' => 'NOVITA ARIE DWI RATNANINGRUM, S.H.Sp.Not M.H.', 'job' => 'Hakim Tingkat Pertama', 'unit' => 'Pengadilan Negeri Sleman', 'tmt' => '21 Juni 2021', 'gender' => 'Perempuan'],
            ['nip' => '197811012003122000', 'name' => 'RADEN RORO ANDY NURVITA, S.H., M.H.', 'job' => 'Hakim Tingkat Pertama', 'unit' => 'Pengadilan Negeri Sleman', 'tmt' => '13 Oktober 2025', 'gender' => 'Perempuan'],
            ['nip' => '197811192002122000', 'name' => 'AIDA NOVITA, S.H., M.H.', 'job' => 'Hakim Tingkat Pertama', 'unit' => 'Pengadilan Negeri Sleman', 'tmt' => '12 Februari 2026', 'gender' => 'Perempuan'],
            ['nip' => '197903142002122000', 'name' => 'INTAN TRI KUMALASARI, S.H.,M.H', 'job' => 'Hakim Tingkat Pertama', 'unit' => 'Pengadilan Negeri Sleman', 'tmt' => '02 Januari 2023', 'gender' => 'Perempuan'],
            ['nip' => '198012112007042000', 'name' => 'FITRIANI, S.H., M.H.', 'job' => 'Hakim Tingkat Pertama', 'unit' => 'Pengadilan Negeri Sleman', 'tmt' => '13 Oktober 2025', 'gender' => 'Perempuan'],
            ['nip' => '198106072007042000', 'name' => 'DIAN ANGGRAINI MEKSOWATI, S.H., M.H.', 'job' => 'Hakim Tingkat Pertama', 'unit' => 'Pengadilan Negeri Sleman', 'tmt' => '13 Oktober 2025', 'gender' => 'Perempuan'],
            ['nip' => '197911092008052000', 'name' => 'NIKENTARI, S.H., M.H.', 'job' => 'Hakim Tingkat Pertama', 'unit' => 'Pengadilan Negeri Sleman', 'tmt' => '13 Oktober 2025', 'gender' => 'Perempuan'],
            ['nip' => '198505092009041000', 'name' => 'ADITYA WAHYUADRIANTO, S.H.', 'job' => 'Panitera Muda Tingkat Pertama Klas IA', 'unit' => 'Panitera Muda Pidana', 'tmt' => '45929', 'gender' => 'Laki-Laki'],
            ['nip' => '196911241994031000', 'name' => 'HAMMAM HARIS, S.H.', 'job' => 'Panitera Muda Tingkat Pertama Klas IA', 'unit' => 'Panitera Muda Perdata', 'tmt' => '45929', 'gender' => 'Laki-Laki'],
            ['nip' => '196906111991031000', 'name' => 'R RUDI HARSOJO, S.H.', 'job' => 'Panitera Muda Tingkat Pertama Klas IA', 'unit' => 'Panitera Muda Hukum', 'tmt' => '45929', 'gender' => 'Laki-Laki'],
            ['nip' => '197101071998031000', 'name' => 'RADEN MOHAMMAD NOER JAHYA, SS.', 'job' => 'Kepala Subbagian', 'unit' => 'Subbagian Perencanaan, Teknologi Informasi, dan Pelaporan', 'tmt' => '01 Februari 2023', 'gender' => 'Laki-Laki'],
            ['nip' => '198305262002122000', 'name' => 'NIKEN KUSUMARATRI SUDARMAJI, S.H.', 'job' => 'Kepala Subbagian', 'unit' => 'Subbagian Kepegawaian, Organisasi, dan Tata Laksana', 'tmt' => '01 Februari 2023', 'gender' => 'Perempuan'],
            ['nip' => '198205012009041000', 'name' => 'WIRAWAN DWI ASMARA, S.IP.', 'job' => 'Kepala Subbagian', 'unit' => 'Subbagian Umum dan Keuangan', 'tmt' => '45411', 'gender' => 'Laki-Laki'],
            ['nip' => '197607112008052000', 'name' => 'YULINA NGESTI HANDAYANI, S.H., M.H.', 'job' => 'Panitera Pengganti Tingkat Pertama', 'unit' => 'Panitera', 'tmt' => '04 Maret 2019', 'gender' => 'Perempuan'],
            ['nip' => '198703292009122000', 'name' => 'ARAH ATI SUGIANTO, S.H., M.H.', 'job' => 'Panitera Pengganti Tingkat Pertama', 'unit' => 'Panitera', 'tmt' => '42853', 'gender' => 'Perempuan'],
            ['nip' => '196802291991031000', 'name' => 'SUYITNA, S.H.', 'job' => 'Panitera Pengganti Tingkat Pertama', 'unit' => 'Panitera', 'tmt' => '38981', 'gender' => 'Laki-Laki'],
            ['nip' => '197604112009122000', 'name' => 'DWI INDIASTUTI, S.H.', 'job' => 'Panitera Pengganti Tingkat Pertama', 'unit' => 'Panitera', 'tmt' => '02 Januari 2024', 'gender' => 'Perempuan'],
            ['nip' => '196705251991032000', 'name' => 'TITIK HARIYANTI, S.H.', 'job' => 'Panitera Pengganti Tingkat Pertama', 'unit' => 'Panitera', 'tmt' => '16 Januari 2014', 'gender' => 'Perempuan'],
            ['nip' => '198209272006042000', 'name' => 'RAHMI AROFAH AZIZ, S.H.', 'job' => 'Panitera Pengganti Tingkat Pertama', 'unit' => 'Panitera', 'tmt' => '16 Juni 2015', 'gender' => 'Perempuan'],
            ['nip' => '196810251990031000', 'name' => 'DARMAJI, S.H.', 'job' => 'Panitera Pengganti Tingkat Pertama', 'unit' => 'Panitera', 'tmt' => '28 Februari 2011', 'gender' => 'Laki-Laki'],
            ['nip' => '196712131989031000', 'name' => 'IWAN SULISTYANTO, S.H.', 'job' => 'Panitera Pengganti Tingkat Pertama', 'unit' => 'Panitera', 'tmt' => '41946', 'gender' => 'Laki-Laki'],
            ['nip' => '196706301987032000', 'name' => 'RINI WIDAYATI, S.H.', 'job' => 'Panitera Pengganti Tingkat Pertama', 'unit' => 'Panitera', 'tmt' => '01 Juli 2000', 'gender' => 'Perempuan'],
            ['nip' => '198401252006041000', 'name' => 'ANGGORO SETYAWAN, S.Sos, S.H.', 'job' => 'Panitera Pengganti Tingkat Pertama', 'unit' => 'Panitera', 'tmt' => '14 Maret 2022', 'gender' => 'Laki-Laki'],
            ['nip' => '196612011996031000', 'name' => 'JOKO HARIWAHYUNO, S.H.', 'job' => 'Panitera Pengganti Tingkat Pertama', 'unit' => 'Panitera', 'tmt' => '03 Agustus 2002', 'gender' => 'Laki-Laki'],
            ['nip' => '197002201990031000', 'name' => 'HARSONO, S.H.', 'job' => 'Panitera Pengganti Tingkat Pertama', 'unit' => 'Panitera', 'tmt' => '04 Maret 2019', 'gender' => 'Laki-Laki'],
            ['nip' => '198204262008051000', 'name' => 'ALBERTUS PRIYO INDARTO, S.H.', 'job' => 'Panitera Pengganti Tingkat Pertama', 'unit' => 'Panitera', 'tmt' => '05 Mei 2017', 'gender' => 'Laki-Laki'],
            ['nip' => '198209042011011000', 'name' => 'DWI KRISYANTO, S.E., S.H., M.H.', 'job' => 'Panitera Pengganti Tingkat Pertama', 'unit' => 'Panitera', 'tmt' => '', 'gender' => 'Laki-Laki'],
            ['nip' => '198412252014031000', 'name' => 'ALOYSIUS YUDO KRISTANTO, S.H.', 'job' => 'Panitera Pengganti Tingkat Pertama', 'unit' => 'Panitera', 'tmt' => '05 Juni 2023', 'gender' => 'Laki-Laki'],
            ['nip' => '198608272009042000', 'name' => 'NURAINI AGUSTINA MUDJITO, S.H.', 'job' => 'Panitera Pengganti Tingkat Pertama', 'unit' => 'Panitera', 'tmt' => '28 Agustus 2019', 'gender' => 'Perempuan'],
            ['nip' => '198408072009121000', 'name' => 'RONNI ADY PRADHANA, S.T., MBA.', 'job' => 'Juru Sita', 'unit' => 'Panitera', 'tmt' => '19 Mei 2020', 'gender' => 'Laki-Laki'],
            ['nip' => '198001222012122000', 'name' => 'SITI MUNAWAROH, S.H.', 'job' => 'Klerek - Analis Perkara Peradilan', 'unit' => 'Panitera Muda Pidana', 'tmt' => '02 Oktober 2023', 'gender' => 'Perempuan'],
            ['nip' => '198207212009122000', 'name' => 'DYAH RAHAJENG INDRESWARI, S.E., MBA.', 'job' => 'Analis Pengelolaan Keuangan APBN Ahli Muda', 'unit' => 'Pengadilan Negeri Sleman', 'tmt' => '08 Februari 2023', 'gender' => 'Perempuan'],
            ['nip' => '197707222006041000', 'name' => 'ANTON ICHTIARSO, S.S., M.H.', 'job' => 'Juru Sita', 'unit' => 'Panitera', 'tmt' => '22 Februari 2019', 'gender' => 'Laki-Laki'],
            ['nip' => '198104102009042000', 'name' => 'HARLINNA SURBAKTI, S.I.P.,M.M.', 'job' => 'Analis Sumber Daya Manusia Aparatur Ahli Muda', 'unit' => 'Subbagian Kepegawaian, Organisasi, dan Tata Laksana', 'tmt' => '01 Desember 2023', 'gender' => 'Perempuan'],
            ['nip' => '197411112006041000', 'name' => 'ABI DZARIN, S.H., M.H.', 'job' => 'Juru Sita', 'unit' => 'Panitera', 'tmt' => '03 Agustus 2018', 'gender' => 'Laki-Laki'],
            ['nip' => '196812141993031000', 'name' => 'SLAMET PARYANTA', 'job' => 'Juru Sita', 'unit' => 'Panitera', 'tmt' => '05 Januari 2011', 'gender' => 'Laki-Laki'],
            ['nip' => '198707102011012000', 'name' => 'GIRINDRA RASIKA LISTUNIMITTA, S.H.', 'job' => 'Pranata Keuangan APBN Mahir', 'unit' => 'Sekretaris', 'tmt' => '44287', 'gender' => 'Laki-Laki'],
            ['nip' => '199211102019031000', 'name' => 'AHMAD ALAMSYAH, S.T.', 'job' => 'Pranata Komputer Ahli Pertama', 'unit' => 'Sekretaris', 'tmt' => '15 Maret 2021', 'gender' => 'Laki-Laki'],
            ['nip' => '199801282024052000', 'name' => 'ADE WULAN FITRIANA, S.H.', 'job' => 'Klerek - Analis Perkara Peradilan', 'unit' => 'Panitera Muda Pidana', 'tmt' => '02 Mei 2025', 'gender' => 'Perempuan'],
            ['nip' => '200011012024052000', 'name' => 'HASNA NUR ADILA AGMA, S.H.', 'job' => 'Klerek - Analis Perkara Peradilan', 'unit' => 'Panitera Muda Perdata', 'tmt' => '02 Mei 2025', 'gender' => 'Perempuan'],
            ['nip' => '199703312025062000', 'name' => 'REGINA SONDANG CLARA PARDEDE, S.H.', 'job' => 'Analis Perkara Peradilan', 'unit' => 'Panitera Muda Perdata', 'tmt' => '01 Juni 2026', 'gender' => 'Perempuan'],
            ['nip' => '199809262024052000', 'name' => 'MONIKA SEPTIA KHOZAAIN, S.H.', 'job' => 'Klerek - Analis Perkara Peradilan', 'unit' => 'Panitera Muda Hukum', 'tmt' => '02 Mei 2025', 'gender' => 'Perempuan'],
            ['nip' => '199812182025062000', 'name' => 'SAGITA MUTIARA SARI, S.H.', 'job' => 'Analis Perkara Peradilan', 'unit' => 'Panitera Muda Hukum', 'tmt' => '01 Juni 2026', 'gender' => 'Perempuan'],
            ['nip' => '200006032024052000', 'name' => 'ANGELA UTARI CAHYANINGTYAS, S.H.', 'job' => 'Klerek - Analis Perkara Peradilan', 'unit' => 'Panitera Muda Khusus Hak Asasi Manusia', 'tmt' => '02 Mei 2025', 'gender' => 'Perempuan'],
            ['nip' => '199803242022032000', 'name' => 'HENI ASMAWANTI, A.Md.A.B.', 'job' => 'Pengelola Penanganan Perkara', 'unit' => 'Panitera Muda Pidana', 'tmt' => '02 Oktober 2023', 'gender' => 'Perempuan'],
            ['nip' => '199309152020122000', 'name' => 'ANITA RAHMAWATI, A.Md.', 'job' => 'Pengolah Data dan Informasi', 'unit' => 'Subbagian Perencanaan, Teknologi Informasi, dan Pelaporan', 'tmt' => '01 Oktober 2025', 'gender' => 'Perempuan'],
            ['nip' => '199206122020122000', 'name' => 'ZUNI ATMAWATI, A.Md.', 'job' => 'Pengolah Data dan Informasi', 'unit' => 'Subbagian Umum dan Keuangan', 'tmt' => '02 Oktober 2023', 'gender' => 'Perempuan'],
        ];
    }

    private function resolvePositionName(string $job): string
    {
        $job = strtolower($job);

        return match (true) {
            str_contains($job, 'wakil ketua') => 'Wakil Ketua',
            str_contains($job, 'ketua') => 'Ketua',
            str_contains($job, 'hakim') => 'Hakim',
            str_contains($job, 'sekretaris') => 'Sekretaris',
            str_contains($job, 'panitera muda') => 'Panitera Muda',
            str_contains($job, 'panitera pengganti') => 'Panitera Pengganti',
            str_contains($job, 'panitera') => 'Panitera',
            str_contains($job, 'juru sita') || str_contains($job, 'jurusita') => 'Jurusita',
            str_contains($job, 'kepala subbagian') => 'Kepala Sub Bagian',
            str_contains($job, 'sumber daya manusia') || str_contains($job, 'kepegawaian') => 'Analis Kepegawaian',
            str_contains($job, 'komputer') => 'Pranata Komputer',
            str_contains($job, 'keuangan') || str_contains($job, 'apbn') => 'Pengelola Keuangan',
            str_contains($job, 'bmn') => 'Pengelola BMN',
            str_contains($job, 'data') || str_contains($job, 'informasi') => 'Pengolah Data & Informasi',
            default => 'Staf Kepaniteraan',
        };
    }

    private function resolveWorkUnitName(string $unit, string $positionName): string
    {
        $unit = strtolower($unit);

        return match (true) {
            str_contains($unit, 'pidana') => 'Kepaniteraan Pidana',
            str_contains($unit, 'perdata') => 'Kepaniteraan Perdata',
            str_contains($unit, 'hukum') && ! str_contains($unit, 'hak asasi') => 'Kepaniteraan Hukum',
            str_contains($unit, 'hak asasi') => 'Kepaniteraan Khusus Hak Asasi Manusia',
            str_contains($unit, 'kepegawaian') => 'Subbagian Kepegawaian, Organisasi dan Tata Laksana',
            str_contains($unit, 'umum') || str_contains($unit, 'keuangan') => 'Subbagian Umum dan Keuangan',
            str_contains($unit, 'perencanaan') || str_contains($unit, 'teknologi') || str_contains($unit, 'pelaporan') => 'Subbagian Perencanaan, Teknologi Informasi dan Pelaporan',
            str_contains($unit, 'sekretaris') => 'Kesekretariatan',
            str_contains($unit, 'panitera') => 'Kepaniteraan',
            $positionName === 'Hakim' || $positionName === 'Ketua' || $positionName === 'Wakil Ketua' => 'Majelis Hakim',
            default => 'Pengadilan Negeri Sleman',
        };
    }

    private function educationLevelFromName(string $name): string
    {
        return match (true) {
            str_contains($name, 'A.Md') => 'D3',
            str_contains($name, 'Dr.') => 'S3',
            str_contains($name, 'M.H') || str_contains($name, 'M.M') || str_contains($name, 'MBA') || str_contains($name, 'M.Hum') => 'S2',
            default => 'S1',
        };
    }

    private function birthDateFromNip(string $nip): ?Carbon
    {
        $date = substr($nip, 0, 8);

        if (! preg_match('/^\d{8}$/', $date)) {
            return null;
        }

        return Carbon::createFromFormat('Ymd', $date);
    }

    private function appointmentDateFromNip(string $nip): ?Carbon
    {
        $date = substr($nip, 8, 6);

        if (! preg_match('/^\d{6}$/', $date)) {
            return null;
        }

        return Carbon::createFromFormat('Ym', $date)->startOfMonth();
    }

    private function workExperienceFromNip(string $nip): int
    {
        $appointment = $this->appointmentDateFromNip($nip);

        return $appointment ? $appointment->diffInYears(Carbon::create(2026, 6, 16)) : 0;
    }

    private function parseIndonesianDate(?string $value): ?Carbon
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        if (is_numeric($value)) {
            return Carbon::create(1899, 12, 30)->addDays((int) $value);
        }

        $months = [
            'januari' => 'January',
            'februari' => 'February',
            'maret' => 'March',
            'april' => 'April',
            'mei' => 'May',
            'juni' => 'June',
            'juli' => 'July',
            'agustus' => 'August',
            'september' => 'September',
            'oktober' => 'October',
            'november' => 'November',
            'desember' => 'December',
        ];

        $normalized = strtolower(trim($value));
        foreach ($months as $id => $en) {
            $normalized = str_replace($id, $en, $normalized);
        }

        return Carbon::parse($normalized);
    }

    private function trainingDateFor(int $index, string $positionName): ?Carbon
    {
        if ($index % 9 === 0) {
            return null;
        }

        $baseYear = match ($positionName) {
            'Hakim', 'Ketua', 'Wakil Ketua' => 2021,
            'Panitera', 'Panitera Muda', 'Panitera Pengganti', 'Jurusita' => 2020,
            'Pengelola Keuangan', 'Pranata Komputer' => 2022,
            default => 2019,
        };

        return Carbon::create($baseYear + ($index % 4), (($index % 12) + 1), 15);
    }

    private function seedScoreFor(Employee $employee, string $criterionCode, int $index): int
    {
        return match ($criterionCode) {
            'C1' => [2, 3, 4, 3, 5, 2][$index % 6],
            'C2' => $employee->last_training_date === null ? 5 : min(5, max(1, $employee->last_training_date->diffInYears(Carbon::create(2026, 6, 16)))),
            'C3' => min(5, max(1, (int) ceil($employee->current_position_years / 2))),
            'C4' => $employee->last_promotion_date && $employee->last_promotion_date->diffInYears(Carbon::create(2026, 6, 16)) < 1 ? 5 : 3,
            'C5' => $employee->age <= 30 ? 5 : ($employee->age <= 40 ? 4 : ($employee->age <= 50 ? 3 : ($employee->age <= 55 ? 2 : 1))),
            default => 3,
        };
    }
}
