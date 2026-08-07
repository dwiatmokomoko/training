<?php

namespace App\Livewire;

use App\Models\Employee;
use Livewire\Component;
use App\Models\TrainingNeed;
use App\Services\SAWService;
use App\Support\Access;

class TrainingNeedsList extends Component
{
    public string $jobFamilyFilter = 'HK';
    public string $trainingTypeFilter = '';
    public string $periodFilter = '';
    public int $quota = 10;

    protected $listeners = ['refreshTrainingNeeds' => '$refresh'];

    public function mount(): void
    {
        $this->periodFilter = $this->periodKey((int) now()->year, now()->month <= 6 ? 1 : 2);
    }

    public function showFilteredData(): void
    {
        $this->dispatch('trainingNeedsFiltered');
    }

    public function updateStatus($trainingNeedId, $status, $notes = null)
    {
        Access::denyIfCannot(in_array($status, ['approved', 'rejected'], true) ? 'training-needs.approve' : 'training-needs.manage');

        $trainingNeed = TrainingNeed::find($trainingNeedId);
        
        if ($trainingNeed) {
            $trainingNeed->update([
                'status' => $status,
                'notes' => $notes
            ]);
            
            session()->flash('success', 'Status pelatihan berhasil diperbarui!');
            $this->dispatch('statusUpdated');
        }
    }

    public function deleteTrainingNeed($trainingNeedId)
    {
        Access::denyIfCannot('training-needs.manage');

        $trainingNeed = TrainingNeed::find($trainingNeedId);
        
        if ($trainingNeed) {
            $trainingNeed->delete();
            session()->flash('success', 'Data kebutuhan pelatihan berhasil dihapus!');
            $this->dispatch('trainingNeedDeleted');
        }
    }

    public function runAnalysis()
    {
        Access::denyIfCannot('analysis.run');

        $sawService = app(SAWService::class);
        $results = $sawService->calculateTrainingNeeds();
        [$periodYear, $periodSemester] = $this->selectedPeriodParts();
        $sawService->saveTrainingNeeds($results, $periodYear, $periodSemester);

        session()->flash('success', "Analisis SAW {$periodYear} Semester {$periodSemester} berhasil dijalankan dan disimpan.");
    }

    public function render()
    {
        $query = TrainingNeed::with(['employee.position.jobFamily'])
            ->orderBy('priority_rank');

        $this->applyTrainingNeedFilters($query);

        $trainingNeeds = $query->get();
        $jobFamilies = $this->jobFamilyOptions();
        $groups = $this->buildGroups($trainingNeeds, $jobFamilies);
        $summary = $this->buildSummary();
        $trainingTypes = TrainingNeed::query()
            ->select('training_type')
            ->distinct()
            ->orderBy('training_type')
            ->pluck('training_type')
            ->values();
        $periods = $this->periodOptions();

        return view('livewire.training-needs-list', [
            'trainingNeeds' => $trainingNeeds,
            'groups' => $groups,
            'jobFamilies' => $jobFamilies,
            'summary' => $summary,
            'trainingTypes' => $trainingTypes,
            'periods' => $periods,
        ]);
    }

    private function applyTrainingNeedFilters($query): void
    {
        if ($this->jobFamilyFilter !== '') {
            $query->whereHas('employee.position.jobFamily', function ($jobFamilyQuery) {
                $jobFamilyQuery->where('code', $this->jobFamilyFilter);
            });
        }

        if ($this->trainingTypeFilter !== '') {
            $query->where('training_type', $this->trainingTypeFilter);
        }

        if ($this->periodFilter !== '') {
            [$periodYear, $periodSemester] = $this->selectedPeriodParts();
            $startMonth = $periodSemester === 1 ? 1 : 7;
            $endMonth = $periodSemester === 1 ? 6 : 12;

            $query->where(function ($periodQuery) use ($periodYear, $periodSemester, $startMonth, $endMonth) {
                $periodQuery->where(function ($storedPeriodQuery) use ($periodYear, $periodSemester) {
                    $storedPeriodQuery->where('period_year', $periodYear)
                        ->where('period_semester', $periodSemester);
                })
                    ->orWhere(function ($fallbackQuery) use ($periodYear, $startMonth, $endMonth) {
                        $fallbackQuery->whereNull('period_year')
                            ->whereYear('recommended_date', $periodYear)
                            ->whereMonth('recommended_date', '>=', $startMonth)
                            ->whereMonth('recommended_date', '<=', $endMonth);
                    });
            });
        }
    }

    private function buildSummary(): array
    {
        $employeeQuery = Employee::query();

        if ($this->jobFamilyFilter !== '') {
            $employeeQuery->whereHas('position.jobFamily', function ($jobFamilyQuery) {
                $jobFamilyQuery->where('code', $this->jobFamilyFilter);
            });
        }

        $totalEmployees = (clone $employeeQuery)->count();
        $trainedEmployees = (clone $employeeQuery)
            ->whereHas('trainingHistories', function ($historyQuery) {
                if ($this->trainingTypeFilter !== '') {
                    $historyQuery->where('training_name', $this->trainingTypeFilter);
                }

                if ($this->periodFilter !== '') {
                    [$periodYear, $periodSemester] = $this->selectedPeriodParts();
                    $startMonth = $periodSemester === 1 ? 1 : 7;
                    $endMonth = $periodSemester === 1 ? 6 : 12;

                    $historyQuery->where(function ($dateQuery) use ($periodYear, $startMonth, $endMonth) {
                        $dateQuery->where(function ($startQuery) use ($periodYear, $startMonth, $endMonth) {
                            $startQuery->whereYear('start_date', $periodYear)
                                ->whereMonth('start_date', '>=', $startMonth)
                                ->whereMonth('start_date', '<=', $endMonth);
                        })->orWhere(function ($endQuery) use ($periodYear, $startMonth, $endMonth) {
                            $endQuery->whereYear('end_date', $periodYear)
                                ->whereMonth('end_date', '>=', $startMonth)
                                ->whereMonth('end_date', '<=', $endMonth);
                        });
                    });
                }
            })
            ->count();

        return [
            'total_employees' => $totalEmployees,
            'trained_employees' => $trainedEmployees,
            'untrained_employees' => max($totalEmployees - $trainedEmployees, 0),
            'quota' => $this->quota,
        ];
    }

    private function buildGroups($trainingNeeds, array $jobFamilies)
    {
        $labels = collect($jobFamilies);

        if ($this->jobFamilyFilter !== '') {
            return collect([
                $this->jobFamilyFilter => [
                    'label' => $labels->get($this->jobFamilyFilter, 'Rumpun Terpilih'),
                    'items' => $trainingNeeds->values(),
                ],
            ]);
        }

        $groups = $labels->map(fn ($label, $code) => [
            'label' => $label,
            'items' => $trainingNeeds
                ->filter(fn ($need) => $need->employee->position->jobFamily?->code === $code)
                ->values(),
        ]);

        $knownCodes = $labels->keys()->all();
        $otherItems = $trainingNeeds
            ->filter(fn ($need) => ! in_array($need->employee->position->jobFamily?->code, $knownCodes, true))
            ->values();

        if ($otherItems->isNotEmpty()) {
            $groups->put('OTHER', ['label' => 'Lainnya', 'items' => $otherItems]);
        }

        return $groups;
    }

    private function jobFamilyOptions(): array
    {
        return [
            'HK' => 'Hakim',
            'KP' => 'Kepaniteraan',
            'KS' => 'Kesekretariatan',
        ];
    }

    private function periodOptions()
    {
        $currentYear = now()->year;
        $options = collect();

        foreach (range($currentYear, $currentYear - 4) as $year) {
            $options->push([
                'key' => $this->periodKey($year, 2),
                'label' => $year . ' Semester 2',
            ]);
            $options->push([
                'key' => $this->periodKey($year, 1),
                'label' => $year . ' Semester 1',
            ]);
        }

        return $options;
    }

    private function periodKey(int $year, int $semester): string
    {
        return $year . '-S' . $semester;
    }

    private function selectedPeriodParts(): array
    {
        if (! preg_match('/^(\\d{4})-S([12])$/', $this->periodFilter, $matches)) {
            return [(int) now()->year, now()->month <= 6 ? 1 : 2];
        }

        return [(int) $matches[1], (int) $matches[2]];
    }
}
