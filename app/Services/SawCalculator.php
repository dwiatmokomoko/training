<?php

namespace App\Services;

use App\Models\AssessmentPeriod;
use App\Models\Employee;
use App\Models\SawCriterion;
use App\Models\Training;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class SawCalculator
{
    public function rankings(?AssessmentPeriod $period = null): Collection
    {
        $period ??= AssessmentPeriod::query()->latest('started_at')->first();
        $criteria = SawCriterion::query()->where('is_active', true)->orderBy('code')->get();
        $employees = Employee::query()
            ->with(['group', 'unit', 'position', 'trainingHistories.training', 'performanceScores.indicator', 'sawScores.criterion'])
            ->where('status', 'Aktif')
            ->orderBy('name')
            ->get();

        $matrix = $employees->map(function (Employee $employee) use ($period, $criteria) {
            $automatic = $this->automaticScores($employee, $period);
            $manualScores = $employee->sawScores
                ->when($period, fn (Collection $scores) => $scores->where('assessment_period_id', $period->id))
                ->keyBy('saw_criterion_id');
            $raw = [];
            $sources = [];
            $missing = [];

            foreach ($criteria as $criterion) {
                $manual = $manualScores->get($criterion->id);

                if ($manual) {
                    $raw[$criterion->code] = (float) $manual->value;
                    $sources[$criterion->code] = 'Manual';

                    continue;
                }

                $raw[$criterion->code] = $automatic[$criterion->code] ?? $this->neutralScore($criterion);
                $sources[$criterion->code] = array_key_exists($criterion->code, $automatic) ? 'Otomatis' : 'Default';

                if ($criterion->code === 'C1' && ! $this->hasPerformanceScore($employee, $period)) {
                    $missing[] = 'C1 belum punya nilai kinerja/assessment';
                }
            }

            return [
                'employee' => $employee,
                'raw' => $raw,
                'sources' => $sources,
                'missing' => $missing,
            ];
        });

        $normalizers = $criteria->mapWithKeys(function (SawCriterion $criterion) use ($matrix) {
            $values = $matrix->pluck("raw.{$criterion->code}")->filter(fn ($value) => $value > 0);

            return [$criterion->code => [
                'min' => max(1, (float) ($values->min() ?: 1)),
                'max' => max(1, (float) ($values->max() ?: 1)),
            ]];
        });

        return $matrix->map(function (array $row) use ($criteria, $normalizers) {
            $normalized = [];
            $score = 0.0;

            foreach ($criteria as $criterion) {
                $value = (float) ($row['raw'][$criterion->code] ?? 1);
                $range = $normalizers[$criterion->code] ?? ['min' => 1, 'max' => 1];

                $normalizedValue = $criterion->attribute === 'Cost'
                    ? ($range['min'] / max($value, 1))
                    : ($value / max($range['max'], 1));

                $normalized[$criterion->code] = round($normalizedValue, 4);
                $score += $normalizedValue * (float) $criterion->weight;
            }

            $training = $this->recommendedTraining($row['employee']);

            return [
                'employee' => $row['employee'],
                'raw' => $row['raw'],
                'sources' => $row['sources'],
                'normalized' => $normalized,
                'score' => round($score, 4),
                'priority' => $this->priorityClass($score),
                'training' => $training,
                'missing' => $row['missing'],
            ];
        })
            ->sortByDesc('score')
            ->values()
            ->map(function (array $row, int $index) {
                $row['rank'] = $index + 1;

                return $row;
            });
    }

    private function automaticScores(Employee $employee, ?AssessmentPeriod $period): array
    {
        return [
            'C1' => $this->performanceScore($employee, $period),
            'C2' => $this->trainingRecencyScore($employee),
            'C3' => $this->positionTenureScore($employee->position_started_at),
            'C4' => $this->promotionUrgencyScore($employee->position_started_at),
            'C5' => $this->ageCostScore($employee->birth_date),
        ];
    }

    private function performanceScore(Employee $employee, ?AssessmentPeriod $period): float
    {
        $scores = $employee->performanceScores;

        if ($period) {
            $scores = $scores->where('assessment_period_id', $period->id);
        }

        if ($scores->isEmpty()) {
            return 3.0;
        }

        return round((float) $scores->avg('score'), 2);
    }

    private function hasPerformanceScore(Employee $employee, ?AssessmentPeriod $period): bool
    {
        $scores = $employee->performanceScores;

        if ($period) {
            $scores = $scores->where('assessment_period_id', $period->id);
        }

        return $scores->isNotEmpty();
    }

    private function trainingRecencyScore(Employee $employee): int
    {
        $latest = $employee->trainingHistories
            ->filter(fn ($history) => $history->training?->employee_group_id === $employee->employee_group_id)
            ->sortByDesc(fn ($history) => $history->ended_at ?? $history->started_at)
            ->first();

        if (! $latest || (! $latest->ended_at && ! $latest->started_at)) {
            return 5;
        }

        $years = ($latest->ended_at ?? $latest->started_at)->diffInYears(now());

        return match (true) {
            $years < 1 => 1,
            $years < 2 => 2,
            $years < 4 => 3,
            $years < 6 => 4,
            default => 5,
        };
    }

    private function positionTenureScore(?CarbonInterface $startedAt): int
    {
        if (! $startedAt) {
            return 3;
        }

        $years = $startedAt->diffInYears(now());

        return match (true) {
            $years < 2 => 1,
            $years < 4 => 2,
            $years < 6 => 3,
            $years < 9 => 4,
            default => 5,
        };
    }

    private function promotionUrgencyScore(?CarbonInterface $startedAt): int
    {
        if (! $startedAt) {
            return 2;
        }

        $years = $startedAt->diffInYears(now());

        return match (true) {
            $years < 1 => 5,
            $years < 3 => 4,
            $years < 5 => 3,
            default => 2,
        };
    }

    private function ageCostScore(?CarbonInterface $birthDate): int
    {
        if (! $birthDate) {
            return 3;
        }

        $age = $birthDate->diffInYears(now());

        return match (true) {
            $age <= 30 => 1,
            $age <= 40 => 2,
            $age <= 50 => 3,
            $age <= 55 => 4,
            default => 5,
        };
    }

    private function recommendedTraining(Employee $employee): ?Training
    {
        return Training::query()
            ->where('is_active', true)
            ->where(function ($query) use ($employee) {
                $query->where('employee_group_id', $employee->employee_group_id)
                    ->orWhereNull('employee_group_id');
            })
            ->orderByRaw('employee_group_id is null')
            ->orderBy('name')
            ->first();
    }

    private function neutralScore(SawCriterion $criterion): float
    {
        return round(((float) $criterion->scale_min + (float) $criterion->scale_max) / 2, 2);
    }

    private function priorityClass(float $score): string
    {
        return match (true) {
            $score >= 0.78 => 'Wajib',
            $score >= 0.62 => 'Prioritas',
            default => 'Pengembangan',
        };
    }
}
