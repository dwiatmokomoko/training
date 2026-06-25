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
}
