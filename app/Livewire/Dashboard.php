<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Employee;
use App\Models\TrainingNeed;
use App\Models\Assessment;
use App\Models\Criteria;
use App\Services\SAWService;

class Dashboard extends Component
{
    public $stats = [];
    public $recentTrainingNeeds = [];
    public $criteriaWeights = [];
    public $isAnalyzing = false;

    protected $listeners = ['refreshDashboard' => 'loadData'];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->stats = [
            'total_employees' => Employee::count(),
            'total_assessments' => Assessment::count(),
            'pending_training' => TrainingNeed::where('status', 'pending')->count(),
            'completed_training' => TrainingNeed::where('status', 'completed')->count(),
        ];

        $this->recentTrainingNeeds = TrainingNeed::with(['employee.position'])
            ->orderBy('priority_rank')
            ->limit(10)
            ->get()
            ->toArray();

        $this->criteriaWeights = Criteria::select('name', 'weight')->get()->toArray();
    }

    public function runAnalysis()
    {
        $this->isAnalyzing = true;
        
        try {
            $sawService = new SAWService();
            $results = $sawService->calculateTrainingNeeds();
            $sawService->saveTrainingNeeds($results);
            
            $this->loadData(); // Refresh data
            
            session()->flash('success', 'Analisis kebutuhan pelatihan berhasil dijalankan!');
            $this->dispatch('analysisCompleted');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
        
        $this->isAnalyzing = false;
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}