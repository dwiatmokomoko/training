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
        $this->periodFilter = (string) now()->year;
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
        $sawService->saveTrainingNeeds($results);

        session()->flash('success', 'Analisis SAW berhasil dijalankan dan daftar prioritas diperbarui.');
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
            $query->where(function ($periodQuery) {
                $periodQuery->whereYear('recommended_date', $this->periodFilter)
                    ->orWhere(function ($fallbackQuery) {
                        $fallbackQuery->whereNull('recommended_date')
                            ->whereYear('created_at', $this->periodFilter);
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
                    $historyQuery->where(function ($dateQuery) {
                        $dateQuery->whereYear('start_date', $this->periodFilter)
                            ->orWhereYear('end_date', $this->periodFilter);
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

        return collect(range($currentYear, $currentYear - 4))
            ->map(fn ($year) => (string) $year);
    }
}
