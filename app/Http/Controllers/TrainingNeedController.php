<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrainingNeed;
use App\Models\Employee;
use App\Services\SAWService;

class TrainingNeedController extends Controller
{
    public function index()
    {
        $trainingNeeds = TrainingNeed::with(['employee.position'])
            ->orderBy('priority_rank')
            ->paginate(15);

        return view('training-needs.index', compact('trainingNeeds'));
    }

    public function show(TrainingNeed $trainingNeed)
    {
        $trainingNeed->load(['employee.position.jobFamily', 'employee.workUnit', 'employee.assessments.scores.criteria']);
        $latestAssessment = $trainingNeed->employee->assessments->sortByDesc('created_at')->first();
        $sawBreakdown = $latestAssessment
            ? app(SAWService::class)->calculateEmployeeBreakdown($trainingNeed->employee, $latestAssessment)
            : [];
        
        return view('training-needs.show', compact('trainingNeed', 'sawBreakdown'));
    }

    public function update(Request $request, TrainingNeed $trainingNeed)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,completed',
            'notes' => 'nullable|string'
        ]);

        $trainingNeed->update([
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        return redirect()->route('training-needs.index')
            ->with('success', 'Status pelatihan berhasil diperbarui!');
    }

    public function destroy(TrainingNeed $trainingNeed)
    {
        $trainingNeed->delete();

        return redirect()->route('training-needs.index')
            ->with('success', 'Data kebutuhan pelatihan berhasil dihapus!');
    }

    public function report()
    {
        $trainingNeeds = TrainingNeed::with(['employee.position'])
            ->orderBy('priority_rank')
            ->get();

        $summary = [
            'total' => $trainingNeeds->count(),
            'by_status' => $trainingNeeds->groupBy('status')->map->count(),
            'by_priority' => $trainingNeeds->where('priority_rank', '<=', 10)->count(),
            'avg_score' => $trainingNeeds->avg('saw_score')
        ];

        return view('training-needs.report', compact('trainingNeeds', 'summary'));
    }

    public function recommendations()
    {
        $trainingNeeds = TrainingNeed::with(['employee.position.jobFamily', 'employee.workUnit'])
            ->orderBy('priority_rank')
            ->get();

        $groupedByTraining = $trainingNeeds
            ->groupBy('training_type')
            ->map(fn ($items) => [
                'total' => $items->count(),
                'top_rank' => $items->min('priority_rank'),
                'avg_score' => $items->avg('saw_score'),
                'families' => $items->pluck('employee.position.jobFamily.name')->filter()->unique()->values(),
                'participants' => $items->take(5),
            ])
            ->sortBy('top_rank');

        $summary = [
            'total_recommendations' => $trainingNeeds->count(),
            'training_types' => $groupedByTraining->count(),
            'priority_participants' => $trainingNeeds->where('priority_rank', '<=', 10)->pluck('employee_id')->unique()->count(),
            'urgent' => $trainingNeeds->filter(fn ($need) => str_contains((string) $need->notes, 'Mendesak'))->count(),
        ];

        return view('training-recommendations', compact('trainingNeeds', 'groupedByTraining', 'summary'));
    }
}
