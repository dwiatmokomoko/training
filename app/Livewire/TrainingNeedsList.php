<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TrainingNeed;
use App\Services\SAWService;

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
        $trainingNeed = TrainingNeed::find($trainingNeedId);
        
        if ($trainingNeed) {
            $trainingNeed->delete();
            session()->flash('success', 'Data kebutuhan pelatihan berhasil dihapus!');
            $this->dispatch('trainingNeedDeleted');
        }
    }

    public function runAnalysis()
    {
        $sawService = app(SAWService::class);
        $results = $sawService->calculateTrainingNeeds();
        $sawService->saveTrainingNeeds($results);

        session()->flash('success', 'Analisis SAW berhasil dijalankan dan daftar prioritas diperbarui.');
        $this->resetPage();
    }

    public function render()
    {
        $query = TrainingNeed::with(['employee.position'])
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

        $trainingNeeds = $query->paginate(15);

        return view('livewire.training-needs-list', [
            'trainingNeeds' => $trainingNeeds
        ]);
    }
}
