<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Criteria;
use App\Models\Assessment;
use App\Models\TrainingNeed;
use Illuminate\Support\Collection;

class SAWService
{
    /**
     * Hitung analisis kebutuhan pelatihan menggunakan metode SAW
     */
    public function calculateTrainingNeeds(): Collection
    {
        $employees = Employee::with(['assessments.scores.criteria', 'position.jobFamily', 'workUnit'])->get();
        $criteria = Criteria::latestTna()->get();
        
        if ($employees->isEmpty() || $criteria->isEmpty()) {
            return collect();
        }

        $results = collect();

        foreach ($employees as $employee) {
            // Skip employees without assessments
            if ($employee->assessments->isEmpty()) {
                continue;
            }

            // Get the latest assessment for this employee
            $latestAssessment = $employee->assessments->sortByDesc('created_at')->first();
            
            if (!$latestAssessment || $latestAssessment->scores->isEmpty()) {
                continue;
            }

            $breakdown = $this->calculateEmployeeBreakdown($employee, $latestAssessment, $criteria);
            $sawScore = collect($breakdown)->sum('weighted_score');
            
            if ($sawScore > 0) {
                $results->push([
                    'employee' => $employee,
                    'assessment' => $latestAssessment,
                    'saw_score' => $sawScore,
                    'breakdown' => $breakdown,
                    'training_recommendation' => $this->generateTrainingRecommendation($employee, $sawScore)
                ]);
            }
        }

        // Urutkan berdasarkan skor SAW (tertinggi = prioritas tertinggi)
        return $results->sortByDesc('saw_score')->values();
    }

    /**
     * Hitung skor SAW untuk assessment tertentu
     */
    public function calculateEmployeeBreakdown(Employee $employee, Assessment $assessment, ?Collection $criteria = null): array
    {
        $criteria ??= Criteria::latestTna()->get();
        $assessmentScores = $assessment->scores->keyBy('criteria_id');
        $breakdown = [];

        foreach ($criteria as $criterion) {
            $rawScore = $this->resolveCriterionScore($employee, $criterion, $assessmentScores);

            if ($rawScore === null) {
                continue;
            }

            $normalizedScore = $this->normalizeScore($rawScore, $criterion);
            $weightedScore = $normalizedScore * (float) $criterion->weight;

            $breakdown[] = [
                'criteria' => $criterion,
                'raw_score' => $rawScore,
                'normalized_score' => $normalizedScore,
                'weighted_score' => $weightedScore,
                'source' => $this->criterionSource($criterion),
            ];
        }

        return $breakdown;
    }

    /**
     * Normalisasi skor berdasarkan jenis kriteria.
     * Skor akhir selalu dibaca sebagai prioritas kebutuhan pelatihan: semakin tinggi semakin prioritas.
     */
    private function normalizeScore(float $score, Criteria $criteria): float
    {
        $code = strtoupper((string) $criteria->code);

        if ($code === 'C5') {
            return $score / 5;
        }

        if ($criteria->type === 'benefit') {
            return $score / 5;
        }

        return (6 - $score) / 5;
    }

    private function resolveCriterionScore(Employee $employee, Criteria $criterion, Collection $assessmentScores): ?int
    {
        $code = strtoupper((string) $criterion->code);

        return match ($code) {
            'C1' => $assessmentScores->get($criterion->id)?->score,
            'C2' => $this->scoreTrainingGap($employee),
            'C3' => $this->scoreCurrentPositionTenure($employee),
            'C4' => $this->scorePromotionHistory($employee),
            'C5' => $this->scoreAge($employee),
            default => $assessmentScores->get($criterion->id)?->score,
        };
    }

    private function scoreTrainingGap(Employee $employee): int
    {
        $years = $employee->years_since_last_training;

        if ($years === null || $years > 5) {
            return 5;
        }

        if ($years >= 4) {
            return 4;
        }

        if ($years >= 2) {
            return 3;
        }

        if ($years >= 1) {
            return 2;
        }

        return 1;
    }

    private function scoreCurrentPositionTenure(Employee $employee): int
    {
        $years = $employee->current_position_years;

        if ($years > 8) {
            return 5;
        }

        if ($years >= 6) {
            return 4;
        }

        if ($years >= 4) {
            return 3;
        }

        if ($years >= 2) {
            return 2;
        }

        return 1;
    }

    private function scorePromotionHistory(Employee $employee): int
    {
        $years = $employee->years_since_last_promotion;

        if ($years === null) {
            return 1;
        }

        if ($years < 1) {
            return 5;
        }

        if ($years <= 3) {
            return 4;
        }

        if ($years <= 5) {
            return 3;
        }

        return 2;
    }

    private function scoreAge(Employee $employee): int
    {
        $age = $employee->age;

        if ($age === null || $age <= 30) {
            return 5;
        }

        if ($age <= 40) {
            return 4;
        }

        if ($age <= 50) {
            return 3;
        }

        if ($age <= 55) {
            return 2;
        }

        return 1;
    }

    private function criterionSource(Criteria $criterion): string
    {
        return match (strtoupper((string) $criterion->code)) {
            'C1' => 'Penilaian atasan langsung',
            'C2' => 'Riwayat pelatihan pegawai',
            'C3' => 'TMT jabatan saat ini',
            'C4' => 'Riwayat promosi/jabatan',
            'C5' => 'Tanggal lahir pegawai',
            default => 'Assessment',
        };
    }

    /**
     * Generate rekomendasi pelatihan berdasarkan skor dan profil pegawai
     */
    private function generateTrainingRecommendation(Employee $employee, float $sawScore): array
    {
        $recommendations = [];

        // Analisis berdasarkan posisi
        $positionName = strtolower($employee->position->name);
        $familyCode = $employee->position->jobFamily?->code;

        if ($familyCode === 'HK' || str_contains($positionName, 'hakim')) {
            $recommendations[] = 'Pelatihan Teknis Yudisial';
            $recommendations[] = 'Bimbingan Teknis Penyusunan Putusan';
            $recommendations[] = 'Sertifikasi Hakim Mediator atau Sertifikasi Teknis Khusus';
        } elseif (str_contains($positionName, 'jurusita') || str_contains($positionName, 'juru sita')) {
            $recommendations[] = 'Pelatihan Teknis Pemanggilan dan Pemberitahuan';
            $recommendations[] = 'Pelatihan e-Court dan e-Summons';
            $recommendations[] = 'Sertifikasi Jurusita/Jurusita Pengganti';
        } elseif ($familyCode === 'KP' || str_contains($positionName, 'panitera')) {
            $recommendations[] = 'Pelatihan Administrasi Peradilan';
            $recommendations[] = 'Bimbingan Teknis SIPP dan E-Court';
            $recommendations[] = 'Pelatihan Minutasi dan Arsip Perkara';
        } elseif (str_contains($positionName, 'keuangan')) {
            $recommendations[] = 'Pelatihan SAKTI, DIPA, dan Pengelolaan APBN';
            $recommendations[] = 'Pelatihan Evaluasi Kinerja Anggaran IKPA';
        } elseif (str_contains($positionName, 'komputer') || str_contains($positionName, 'data')) {
            $recommendations[] = 'Pelatihan Teknologi Informasi dan Pengelolaan Data';
            $recommendations[] = 'Pelatihan Aplikasi Kerja dan Pelaporan';
        } else {
            $recommendations[] = 'Pelatihan Administrasi Kesekretariatan';
            $recommendations[] = 'Pelatihan Aplikasi Kerja dan Layanan Internal';
            $recommendations[] = 'Pelatihan Reformasi Birokrasi dan Zona Integritas';
        }

        if ($sawScore >= 0.75) {
            $recommendations[] = 'Pelatihan Wajib Prioritas';
            $priority = 'Tinggi';
        } elseif ($sawScore >= 0.55) {
            $recommendations[] = 'Pelatihan Pengembangan Kompetensi';
            $priority = 'Sedang';
        } else {
            $recommendations[] = 'Coaching atau E-Learning Penyegaran';
            $priority = 'Rendah';
        }

        return [
            'training_types' => $recommendations,
            'priority' => $priority,
            'urgency_level' => $this->determineUrgencyLevel($sawScore)
        ];
    }

    /**
     * Tentukan tingkat urgensi berdasarkan skor SAW
     */
    private function determineUrgencyLevel(float $sawScore): string
    {
        if ($sawScore >= 0.85) {
            return 'Sangat Mendesak';
        } elseif ($sawScore >= 0.70) {
            return 'Mendesak';
        } elseif ($sawScore >= 0.55) {
            return 'Perlu Perhatian';
        } else {
            return 'Terjadwal';
        }
    }

    /**
     * Simpan hasil analisis ke database
     */
    public function saveTrainingNeeds(Collection $results): void
    {
        // Hapus data lama
        TrainingNeed::truncate();

        foreach ($results as $index => $result) {
            $employee = $result['employee'];
            $recommendation = $result['training_recommendation'];

            foreach ($recommendation['training_types'] as $trainingType) {
                TrainingNeed::create([
                    'employee_id' => $employee->id,
                    'training_type' => $trainingType,
                    'training_description' => "Rekomendasi pelatihan untuk {$employee->name} berdasarkan analisis SAW",
                    'saw_score' => $result['saw_score'],
                    'priority_rank' => $index + 1,
                    'status' => 'pending',
                    'recommended_date' => now()->addDays(30),
                    'notes' => "Prioritas: {$recommendation['priority']}, Urgensi: {$recommendation['urgency_level']}"
                ]);
            }
        }
    }
}
