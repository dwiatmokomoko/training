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
        $employees = Employee::with(['assessments.scores.criteria', 'position'])->get();
        $criteria = Criteria::all();
        
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

            $sawScore = $this->calculateSAWScore($latestAssessment, $criteria);
            
            if ($sawScore > 0) {
                $results->push([
                    'employee' => $employee,
                    'assessment' => $latestAssessment,
                    'saw_score' => $sawScore,
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
    private function calculateSAWScore(Assessment $assessment, Collection $criteria): float
    {
        $normalizedScores = [];
        $assessmentScores = $assessment->scores->keyBy('criteria_id');

        // Normalisasi nilai untuk setiap kriteria
        foreach ($criteria as $criterion) {
            $score = $assessmentScores->get($criterion->id);
            
            if (!$score) {
                continue; // Skip jika tidak ada score untuk kriteria ini
            }

            $normalizedScore = $this->normalizeScore(
                $score->score,
                $criterion,
                $this->getMinMaxValues($criterion->id)
            );

            $normalizedScores[] = $normalizedScore * $criterion->weight;
        }

        return array_sum($normalizedScores);
    }

    /**
     * Normalisasi skor berdasarkan jenis kriteria
     */
    private function normalizeScore(float $score, Criteria $criteria, array $minMax): float
    {
        $min = $minMax['min'];
        $max = $minMax['max'];

        if ($max == $min) {
            return 1; // Jika semua nilai sama
        }

        if ($criteria->type === 'benefit') {
            // Untuk kriteria benefit: semakin tinggi semakin baik
            // Normalisasi ke skala 0-1 dari skala 1-5
            return $score / 5;
        } else {
            // Untuk kriteria cost: semakin rendah semakin baik
            return (6 - $score) / 5;
        }
    }

    /**
     * Dapatkan nilai min dan max untuk kriteria tertentu dari assessment_scores
     */
    private function getMinMaxValues(int $criteriaId): array
    {
        $scores = \App\Models\AssessmentScore::where('criteria_id', $criteriaId)->get();
        
        return [
            'min' => $scores->min('score') ?? 1,
            'max' => $scores->max('score') ?? 5
        ];
    }

    /**
     * Generate rekomendasi pelatihan berdasarkan skor dan profil pegawai
     */
    private function generateTrainingRecommendation(Employee $employee, float $sawScore): array
    {
        $recommendations = [];

        // Analisis berdasarkan posisi
        $positionName = strtolower($employee->position->name);
        if (str_contains($positionName, 'hakim')) {
            $recommendations[] = 'Pelatihan Teknis Yudisial';
            $recommendations[] = 'Workshop Hukum Terbaru';
        } elseif (str_contains($positionName, 'panitera')) {
            $recommendations[] = 'Pelatihan Administrasi Peradilan';
            $recommendations[] = 'Manajemen Berkas Perkara';
        } else {
            $recommendations[] = 'Pelatihan Administrasi Peradilan';
            $recommendations[] = 'Peningkatan Kompetensi Pelayanan';
        }

        // Analisis berdasarkan skor SAW
        if ($sawScore < 0.5) {
            $recommendations[] = 'Pelatihan Dasar Kompetensi';
            $priority = 'Tinggi';
        } elseif ($sawScore < 0.7) {
            $recommendations[] = 'Pelatihan Pengembangan Lanjutan';
            $priority = 'Sedang';
        } else {
            $recommendations[] = 'Pelatihan Spesialisasi';
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
        if ($sawScore < 0.3) {
            return 'Sangat Mendesak';
        } elseif ($sawScore < 0.5) {
            return 'Mendesak';
        } elseif ($sawScore < 0.7) {
            return 'Perlu Perhatian';
        } else {
            return 'Dapat Ditunda';
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