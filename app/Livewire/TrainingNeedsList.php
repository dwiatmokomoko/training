<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TrainingNeed;
use App\Services\SAWService;
use App\Support\Access;

class TrainingNeedsList extends Component
{
    use WithPagination;

    public $statusFilter = '';
    public $search = '';

    protected $listeners = ['refreshTrainingNeeds' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
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
        $this->resetPage();
    }

    public function render()
    {
        $query = TrainingNeed::with(['employee.position.jobFamily'])
            ->orderBy('priority_rank');

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('employee', function ($employeeQuery) {
                    $employeeQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('nip', 'like', '%' . $this->search . '%');
                })->orWhere('training_type', 'like', '%' . $this->search . '%');
            });
        }

        $trainingNeeds = $query->get();
        $groups = collect([
            'HK' => ['label' => 'Hakim', 'items' => $trainingNeeds->filter(fn ($need) => $need->employee->position->jobFamily?->code === 'HK')->values()],
            'KP' => ['label' => 'Kepaniteraan', 'items' => $trainingNeeds->filter(fn ($need) => $need->employee->position->jobFamily?->code === 'KP')->values()],
            'KS' => ['label' => 'Kesekretariatan', 'items' => $trainingNeeds->filter(fn ($need) => $need->employee->position->jobFamily?->code === 'KS')->values()],
        ]);

        $knownCodes = ['HK', 'KP', 'KS'];
        $otherItems = $trainingNeeds
            ->filter(fn ($need) => ! in_array($need->employee->position->jobFamily?->code, $knownCodes, true))
            ->values();

        if ($otherItems->isNotEmpty()) {
            $groups->put('OTHER', ['label' => 'Lainnya', 'items' => $otherItems]);
        }

        return view('livewire.training-needs-list', [
            'trainingNeeds' => $trainingNeeds,
            'groups' => $groups,
        ]);
    }
}
