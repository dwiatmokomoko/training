<?php

namespace Database\Seeders;

use App\Models\AssessmentPeriod;
use App\Models\Employee;
use App\Models\EmployeeGroup;
use App\Models\PerformanceIndicator;
use App\Models\PerformanceScore;
use App\Models\Position;
use App\Models\PositionHistory;
use App\Models\SawCriterion;
use App\Models\Training;
use App\Models\TrainingHistory;
use App\Models\WorkUnit;
use App\Support\SimpleXlsxReader;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TnaSawSeeder extends Seeder
{
    private SimpleXlsxReader $template;

    private SimpleXlsxReader $employeesWorkbook;

    public function run(): void
    {
        $this->template = new SimpleXlsxReader(database_path('imports/template_import_database_tna_saw_pn_sleman.xlsx'));
        $this->employeesWorkbook = new SimpleXlsxReader(database_path('imports/data_pegawai_pn.xlsx'));

        $this->seedGroups();
        $this->seedUnits();
        $this->seedPositions();
        $this->seedEmployees();
        $this->seedTrainings();
        $this->seedTrainingHistories();
        $this->seedIndicators();
        $this->seedCriteria();
        $this->seedPeriods();
        $this->seedPerformanceScores();
    }

    private function seedGroups(): void
    {
        foreach ($this->template->records('01_Rumpun', ['kode_rumpun', 'nama_rumpun']) as $row) {
            EmployeeGroup::query()->updateOrCreate(
                ['code' => $row['kode_rumpun']],
                [
                    'name' => $row['nama_rumpun'],
                    'description' => $row['deskripsi'] ?? null,
                    'is_active' => $this->isActive($row['status'] ?? 'Aktif'),
                ]
            );
        }
    }

    private function seedUnits(): void
    {
        foreach ($this->template->records('02_Unit_Kerja', ['kode_unit', 'nama_unit']) as $row) {
            WorkUnit::query()->updateOrCreate(
                ['code' => $row['kode_unit']],
                [
                    'name' => $row['nama_unit'],
                    'description' => $row['keterangan'] ?? null,
                    'is_active' => $this->isActive($row['status'] ?? 'Aktif'),
                ]
            );
        }

        foreach ([
            'UK-004' => 'Majelis Hakim',
            'UK-005' => 'Kepaniteraan Pidana',
            'UK-006' => 'Kepaniteraan Perdata',
            'UK-007' => 'Kepaniteraan Hukum',
            'UK-008' => 'Subbagian Kepegawaian, Organisasi dan Tata Laksana',
            'UK-009' => 'Subbagian Umum dan Keuangan',
            'UK-010' => 'Subbagian Perencanaan, Teknologi Informasi dan Pelaporan',
        ] as $code => $name) {
            WorkUnit::query()->updateOrCreate(['code' => $code], ['name' => $name, 'is_active' => true]);
        }
    }

    private function seedPositions(): void
    {
        foreach ($this->template->records('03_Jabatan', ['kode_jabatan', 'nama_jabatan']) as $row) {
            Position::query()->updateOrCreate(
                ['code' => $row['kode_jabatan']],
                [
                    'name' => $row['nama_jabatan'],
                    'employee_group_id' => EmployeeGroup::query()->where('code', $row['kode_rumpun'] ?? null)->value('id'),
                    'work_unit_id' => WorkUnit::query()->where('code', $row['kode_unit'] ?? null)->value('id'),
                    'level' => $row['level_jabatan'] ?? null,
                    'type' => $row['jenis_jabatan'] ?? null,
                    'is_active' => $this->isActive($row['status'] ?? 'Aktif'),
                ]
            );
        }
    }

    private function seedEmployees(): void
    {
        $unitCache = WorkUnit::query()->get()->keyBy('name');
        $positionCounter = Position::query()->count() + 1;

        foreach ($this->employeesWorkbook->records('Sheet1', ['nip', 'nama', 'jabatan']) as $row) {
            $nip = preg_replace('/\D+/', '', (string) $row['nip']);
            $groupCode = $this->guessGroupCode($row['jabatan'] ?? '', $row['unit_kerja'] ?? '');
            $unit = $this->guessUnit($row['unit_kerja'] ?? '', $unitCache);
            $group = EmployeeGroup::query()->where('code', $groupCode)->first();
            $positionName = trim((string) ($row['jabatan'] ?? 'Pegawai'));
            $position = Position::query()->where('name', $positionName)->first();

            if (! $position) {
                $position = Position::query()->create([
                    'code' => 'JAB-AUTO-'.str_pad((string) $positionCounter++, 3, '0', STR_PAD_LEFT),
                    'name' => $positionName,
                    'employee_group_id' => $group?->id,
                    'work_unit_id' => $unit?->id,
                    'level' => str_contains(strtolower($positionName), 'hakim') ? 'Fungsional' : 'Struktural/Fungsional',
                    'type' => $group?->name,
                    'is_active' => true,
                ]);
            }

            $employee = Employee::query()->updateOrCreate(
                ['nip' => $nip],
                [
                    'name' => $row['nama'],
                    'gender' => $this->normalizeGender($row['jenis_kelamin'] ?? null),
                    'birth_date' => $this->parseDate($row['tanggal_lahir'] ?? null) ?? $this->birthDateFromNip($nip),
                    'employee_group_id' => $group?->id,
                    'work_unit_id' => $unit?->id,
                    'position_id' => $position->id,
                    'position_started_at' => $this->parseDate($row['tmt_jabatan_mulai'] ?? null),
                    'rank_class' => $row['gol'] ?? null,
                    'status' => 'Aktif',
                ]
            );

            PositionHistory::query()->updateOrCreate(
                ['employee_id' => $employee->id, 'position_id' => $position->id, 'started_at' => $employee->position_started_at],
                ['history_type' => 'Jabatan Saat Ini', 'notes' => 'Import data pegawai PN Sleman']
            );
        }
    }

    private function seedTrainings(): void
    {
        foreach ($this->template->records('06_Pelatihan', ['kode_pelatihan', 'nama_pelatihan']) as $row) {
            Training::query()->updateOrCreate(
                ['code' => $row['kode_pelatihan']],
                [
                    'name' => $row['nama_pelatihan'],
                    'employee_group_id' => EmployeeGroup::query()->where('code', $row['kode_rumpun'] ?? null)->value('id'),
                    'category' => $row['kategori_pelatihan'] ?? null,
                    'target_competency' => $row['kompetensi_tujuan'] ?? null,
                    'provider' => $row['penyelenggara'] ?? 'Mahkamah Agung/Balai Diklat',
                    'duration_hours' => $row['durasi_jam'] ?? 24,
                    'is_active' => $this->isActive($row['status'] ?? 'Aktif'),
                ]
            );
        }

        foreach ($this->additionalTrainings() as $index => $training) {
            Training::query()->updateOrCreate(
                ['code' => 'PL-REF-'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT)],
                [
                    'name' => $training[1],
                    'employee_group_id' => EmployeeGroup::query()->where('code', $training[0])->value('id'),
                    'category' => $training[2],
                    'target_competency' => $training[2],
                    'provider' => 'Mahkamah Agung/Balai Diklat',
                    'duration_hours' => 24,
                    'method' => $training[3] ?? 'Klasikal',
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedTrainingHistories(): void
    {
        foreach ($this->template->records('07_Riwayat_Pelatihan', ['nip', 'kode_pelatihan']) as $row) {
            $employee = Employee::query()->where('nip', preg_replace('/\D+/', '', (string) $row['nip']))->first();
            $training = Training::query()->where('code', $row['kode_pelatihan'])->first();

            if (! $employee || ! $training) {
                continue;
            }

            TrainingHistory::query()->updateOrCreate(
                ['employee_id' => $employee->id, 'training_id' => $training->id, 'certificate_number' => $row['nomor_sertifikat'] ?? null],
                [
                    'started_at' => $this->parseDate($row['tanggal_mulai'] ?? null),
                    'ended_at' => $this->parseDate($row['tanggal_selesai'] ?? null),
                    'provider' => $row['penyelenggara'] ?? null,
                    'result' => $row['hasil'] ?? null,
                    'notes' => $row['keterangan'] ?? null,
                ]
            );
        }

        Employee::query()->with('group')->get()->each(function (Employee $employee) {
            $bucket = crc32($employee->nip) % 5;
            if ($bucket === 0) {
                return;
            }

            $training = Training::query()->where('employee_group_id', $employee->employee_group_id)->orderBy('id')->skip($bucket - 1)->first()
                ?: Training::query()->where('employee_group_id', $employee->employee_group_id)->first();

            if (! $training) {
                return;
            }

            $endedAt = now()->subYears($bucket)->subMonths($bucket * 2);
            TrainingHistory::query()->updateOrCreate(
                ['employee_id' => $employee->id, 'training_id' => $training->id, 'certificate_number' => 'AUTO-'.$employee->nip],
                [
                    'started_at' => $endedAt->copy()->subDays(3)->toDateString(),
                    'ended_at' => $endedAt->toDateString(),
                    'provider' => 'Balai Diklat',
                    'result' => 'Lulus',
                    'notes' => 'Data contoh deterministik untuk demo sistem',
                ]
            );
        });
    }

    private function seedIndicators(): void
    {
        foreach ($this->template->records('08_Indikator_Kinerja', ['kode_indikator', 'kode_rumpun']) as $row) {
            PerformanceIndicator::query()->updateOrCreate(
                ['code' => $row['kode_indikator']],
                [
                    'employee_group_id' => EmployeeGroup::query()->where('code', $row['kode_rumpun'] ?? null)->value('id'),
                    'name' => $row['nama_indikator'],
                    'description' => $row['deskripsi'] ?? null,
                    'weight' => (float) ($row['bobot_indikator'] ?? 1),
                    'scale_min' => (int) ($row['skala_min'] ?? 1),
                    'scale_max' => (int) ($row['skala_max'] ?? 5),
                    'is_active' => $this->isActive($row['status'] ?? 'Aktif'),
                ]
            );
        }
    }

    private function seedCriteria(): void
    {
        foreach ($this->template->records('09_Kriteria_SAW', ['kode_kriteria', 'nama_kriteria']) as $row) {
            if (! preg_match('/^C\d+$/', (string) $row['kode_kriteria'])) {
                continue;
            }

            SawCriterion::query()->updateOrCreate(
                ['code' => $row['kode_kriteria']],
                [
                    'name' => $row['nama_kriteria'],
                    'attribute' => Str::title($row['atribut'] ?? 'Benefit'),
                    'weight' => (float) ($row['bobot'] ?? 0),
                    'scale_min' => (int) ($row['skala_min'] ?? 1),
                    'scale_max' => (int) ($row['skala_max'] ?? 5),
                    'description' => $row['keterangan'] ?? null,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedPeriods(): void
    {
        foreach ($this->template->records('10_Periode_Penilaian', ['kode_periode', 'nama_periode']) as $row) {
            AssessmentPeriod::query()->updateOrCreate(
                ['code' => $row['kode_periode']],
                [
                    'name' => $row['nama_periode'],
                    'started_at' => $this->parseDate($row['tanggal_mulai'] ?? null),
                    'ended_at' => $this->parseDate($row['tanggal_selesai'] ?? null),
                    'status' => $row['status_periode'] ?? 'Draft',
                    'notes' => $row['keterangan'] ?? null,
                ]
            );
        }
    }

    private function seedPerformanceScores(): void
    {
        foreach ($this->template->records('11_Penilaian_Kinerja', ['kode_periode', 'nip', 'kode_indikator']) as $row) {
            $period = AssessmentPeriod::query()->where('code', $row['kode_periode'])->first();
            $employee = Employee::query()->where('nip', preg_replace('/\D+/', '', (string) $row['nip']))->first();
            $indicator = PerformanceIndicator::query()->where('code', $row['kode_indikator'])->first();

            if ($period && $employee && $indicator) {
                $this->score($period, $employee, $indicator, (int) $row['nilai_indikator'], $row['catatan_penilai'] ?? null);
            }
        }

        $period = AssessmentPeriod::query()->orderBy('started_at')->first();
        if (! $period) {
            return;
        }

        Employee::query()->get()->each(function (Employee $employee) use ($period) {
            PerformanceIndicator::query()
                ->where('employee_group_id', $employee->employee_group_id)
                ->get()
                ->each(function (PerformanceIndicator $indicator) use ($period, $employee) {
                    $score = (crc32($employee->nip.$indicator->code) % 5) + 1;
                    $this->score($period, $employee, $indicator, $score, 'Nilai awal demo berbasis data pegawai dan indikator rumpun.');
                });
        });
    }

    private function score(AssessmentPeriod $period, Employee $employee, PerformanceIndicator $indicator, int $score, ?string $notes): void
    {
        PerformanceScore::query()->updateOrCreate(
            [
                'assessment_period_id' => $period->id,
                'employee_id' => $employee->id,
                'performance_indicator_id' => $indicator->id,
            ],
            [
                'score' => max(1, min(5, $score)),
                'notes' => $notes,
                'assessed_at' => now()->toDateString(),
            ]
        );
    }

    private function additionalTrainings(): array
    {
        return [
            ['HK', 'Sertifikasi Hakim Tipikor', 'Teknis Yudisial', 'Klasikal'],
            ['HK', 'Sertifikasi Hakim Lingkungan Hidup', 'Teknis Yudisial', 'Klasikal'],
            ['HK', 'Sertifikasi Hakim Anak', 'Teknis Yudisial', 'Klasikal'],
            ['HK', 'Sertifikasi Hakim Mediator', 'Teknis Yudisial', 'Coaching'],
            ['KP', 'Pelatihan Administrasi Perkara Perdata dan Pidana', 'Teknis Kepaniteraan', 'Klasikal'],
            ['KP', 'Pelatihan SIPP, e-Court, dan e-Litigation', 'Teknologi Informasi Peradilan', 'E-learning'],
            ['KP', 'Pelatihan Minutasi dan Arsip Perkara', 'Teknis Kepaniteraan', 'Klasikal'],
            ['KP', 'Pelatihan Pelayanan Terpadu Satu Pintu', 'Pelayanan Publik', 'Coaching'],
            ['KS', 'Pelatihan Pengelolaan Keuangan dan Anggaran SAKTI/DIPA', 'Keuangan', 'Klasikal'],
            ['KS', 'Pelatihan Pengadaan Barang/Jasa Pemerintah', 'Administrasi Umum', 'Klasikal'],
            ['KS', 'Pelatihan Pengelolaan BMN', 'Administrasi Umum', 'Klasikal'],
            ['KS', 'Pelatihan SAKIP, RB, dan Zona Integritas', 'Manajerial', 'E-learning'],
            ['KS', 'Pelatihan Manajemen SDM dan Kearsipan', 'Administrasi SDM', 'Coaching'],
        ];
    }

    private function guessGroupCode(string $position, string $unit): string
    {
        $text = strtolower($position.' '.$unit);

        return match (true) {
            str_contains($text, 'hakim'), str_contains($text, 'ketua') => 'HK',
            str_contains($text, 'panitera'), str_contains($text, 'jurusita'), str_contains($text, 'perkara') => 'KP',
            default => 'KS',
        };
    }

    private function guessUnit(string $unitName, $unitCache): ?WorkUnit
    {
        $unitName = trim($unitName ?: 'Pengadilan Negeri Sleman');

        if ($unitCache->has($unitName)) {
            return $unitCache[$unitName];
        }

        $needle = strtolower($unitName);
        foreach ($unitCache as $unit) {
            if (str_contains(strtolower($unitName), strtolower($unit->name)) || str_contains(strtolower($unit->name), $needle)) {
                return $unit;
            }
        }

        return WorkUnit::query()->where('code', 'UK-001')->first();
    }

    private function normalizeGender(?string $gender): ?string
    {
        if (! $gender) {
            return null;
        }

        return str_starts_with(strtolower($gender), 'l') ? 'Laki-Laki' : 'Perempuan';
    }

    private function birthDateFromNip(string $nip): ?string
    {
        if (! preg_match('/^\d{8}/', $nip)) {
            return null;
        }

        $date = substr($nip, 0, 4).'-'.substr($nip, 4, 2).'-'.substr($nip, 6, 2);

        try {
            return Carbon::parse($date)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return Carbon::create(1899, 12, 30)->addDays((int) $value)->toDateString();
        }

        $value = trim((string) $value);
        if (preg_match('/^\d+$/', $value)) {
            return Carbon::create(1899, 12, 30)->addDays((int) $value)->toDateString();
        }

        $months = [
            'januari' => 'January', 'februari' => 'February', 'maret' => 'March', 'april' => 'April',
            'mei' => 'May', 'juni' => 'June', 'juli' => 'July', 'agustus' => 'August',
            'september' => 'September', 'oktober' => 'October', 'november' => 'November', 'desember' => 'December',
        ];
        $translated = str_ireplace(array_keys($months), array_values($months), strtolower($value));

        try {
            return Carbon::parse($translated)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function isActive(?string $status): bool
    {
        return strtolower((string) $status) !== 'tidak aktif';
    }
}
