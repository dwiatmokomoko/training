<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Employee;
use App\Models\TrainingNeed;
use App\Models\Assessment;
use App\Models\Criteria;
use App\Services\SAWService;
use App\Support\Access;

class Dashboard extends Component
{
    public $stats = [];
    public $recentTrainingNeeds = [];
    public $criteriaWeights = [];
    public $competencyLevels = [];
    public $notifications = [];
    public $isAnalyzing = false;

    protected $listeners = ['refreshDashboard' => 'loadData'];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $assessedEmployeeIds = Assessment::query()->distinct()->pluck('employee_id');

        $this->stats = [
            'total_employees' => Employee::count(),
            'total_assessments' => Assessment::count(),
            'pending_training' => TrainingNeed::where('status', 'pending')->count(),
            'completed_training' => TrainingNeed::where('status', 'completed')->count(),
            'approved_training' => TrainingNeed::where('status', 'approved')->count(),
            'unassessed_employees' => Employee::whereNotIn('id', $assessedEmployeeIds)->count(),
        ];

        $this->recentTrainingNeeds = TrainingNeed::with(['employee.position'])
            ->orderBy('priority_rank')
            ->limit(10)
            ->get()
            ->toArray();

        $this->criteriaWeights = Criteria::latestTna()
            ->select('code', 'name', 'weight', 'type', 'importance_rating')
            ->get()
            ->toArray();

        $assessments = Assessment::query()->select('total_score')->get();
        $this->competencyLevels = [
            ['label' => 'Level 1-2', 'count' => $assessments->where('total_score', '<', 2)->count(), 'hint' => 'Perlu penguatan dasar'],
            ['label' => 'Level 3', 'count' => $assessments->whereBetween('total_score', [2, 2.99])->count(), 'hint' => 'Cukup, perlu pendampingan'],
            ['label' => 'Level 4', 'count' => $assessments->whereBetween('total_score', [3, 3.99])->count(), 'hint' => 'Baik'],
            ['label' => 'Level 5', 'count' => $assessments->where('total_score', '>=', 4)->count(), 'hint' => 'Mahir'],
        ];

        $notifications = [];

        if (Access::allows('assessments.manage')) {
            $notifications[] = [
                'label' => 'Belum isi assessment',
                'count' => $this->stats['unassessed_employees'],
                'icon' => 'fa-bell',
                'route' => route('performance'),
            ];
        }

        if (Access::allows('training-needs.manage')) {
            $notifications[] = [
                'label' => 'Menunggu persetujuan pelatihan',
                'count' => $this->stats['pending_training'],
                'icon' => 'fa-clock',
                'route' => route('training-plans'),
            ];
        }

        if (Access::allows('training-needs.view') || Access::allows('training-needs.manage')) {
            $notifications[] = [
                'label' => 'Rencana sudah disetujui',
                'count' => $this->stats['approved_training'],
                'icon' => 'fa-check-double',
                'route' => route('training-needs.index'),
            ];
        }

        $this->notifications = $notifications;
    }

    public function runAnalysis()
    {
        Access::denyIfCannot('analysis.run');

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
