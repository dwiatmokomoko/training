<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrainingNeed;
use App\Models\Employee;
use App\Models\Criteria;
use App\Services\SAWService;

class TrainingNeedController extends Controller
{
    public function index()
    {
        $trainingNeeds = TrainingNeed::with(['employee.position'])
            ->orderBy('priority_rank')
            ->paginate(15);
        $sawData = $this->buildSawViewData();

        return view('training-needs.index', array_merge(compact('trainingNeeds'), $sawData));
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
        $sawData = $this->buildSawViewData();

        $summary = [
            'total' => $trainingNeeds->count(),
            'by_status' => $trainingNeeds->groupBy('status')->map->count(),
            'by_priority' => $trainingNeeds->where('priority_rank', '<=', 10)->count(),
            'avg_score' => $trainingNeeds->avg('saw_score')
        ];

        return view('training-needs.report', array_merge(compact(
            'trainingNeeds',
            'summary'
        ), $sawData));
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

    private function buildSawViewData(): array
    {
        $criteria = Criteria::latestTna()->get();
        $sawService = app(SAWService::class);
        $decisionMatrix = $sawService->buildDecisionMatrix(criteria: $criteria)
            ->map(function ($row, $index) {
                $row['alternative_code'] = 'A' . ($index + 1);
                $row['preference_code'] = 'V' . ($index + 1);

                return $row;
            });
        $criteriaBounds = $sawService->criteriaBounds($decisionMatrix, $criteria);
        $normalizedMatrix = $sawService->normalizeMatrix($decisionMatrix, $criteria, $criteriaBounds)
            ->sortByDesc('saw_score')
            ->values();
        $sawPreview = $normalizedMatrix->take(10);
        $preferenceRows = $sawPreview->map(function ($row) {
            $terms = collect($row['breakdown'])->map(function ($item) {
                return '(' . number_format((float) $item['criteria']->weight, 3) . ' x ' . number_format($item['normalized_score'], 3) . ')';
            })->implode(' + ');

            return [
                'alternative' => $row['alternative_code'] ?? '-',
                'preference' => $row['preference_code'] ?? '-',
                'employee' => $row['employee'],
                'formula' => $terms,
                'score' => $row['saw_score'],
            ];
        });

        return [
            'criteria' => $criteria,
            'decisionMatrix' => $decisionMatrix,
            'criteriaBounds' => $criteriaBounds,
            'normalizedMatrix' => $normalizedMatrix,
            'sawPreview' => $sawPreview,
            'criteriaScaleRows' => $this->criteriaScaleRows(),
            'preferenceRows' => $preferenceRows,
        ];
    }

    private function criteriaScaleRows(): array
    {
        return [
            'C1' => [
                ['label' => '91-100 (Sangat Baik)', 'score' => 1],
                ['label' => '81-90 (Baik)', 'score' => 2],
                ['label' => '71-80 (Cukup)', 'score' => 3],
                ['label' => '61-70 (Kurang)', 'score' => 4],
                ['label' => '<= 60 (Sangat Kurang)', 'score' => 5],
            ],
            'C2' => [
                ['label' => 'Belum pernah atau > 5 tahun', 'score' => 5],
                ['label' => '4-5 tahun', 'score' => 4],
                ['label' => '2-3 tahun', 'score' => 3],
                ['label' => '1 tahun', 'score' => 2],
                ['label' => '< 1 tahun', 'score' => 1],
            ],
            'C3' => [
                ['label' => '> 8 tahun', 'score' => 5],
                ['label' => '6-8 tahun', 'score' => 4],
                ['label' => '4-5 tahun', 'score' => 3],
                ['label' => '2-3 tahun', 'score' => 2],
                ['label' => '< 2 tahun', 'score' => 1],
            ],
            'C4' => [
                ['label' => 'Baru promosi (< 1 tahun)', 'score' => 5],
                ['label' => 'Promosi 1-3 tahun', 'score' => 4],
                ['label' => 'Promosi 3-5 tahun', 'score' => 3],
                ['label' => 'Promosi > 5 tahun', 'score' => 2],
                ['label' => 'Tidak pernah promosi', 'score' => 1],
            ],
            'C5' => [
                ['label' => '<= 30 tahun', 'score' => 5],
                ['label' => '31-40 tahun', 'score' => 4],
                ['label' => '41-50 tahun', 'score' => 3],
                ['label' => '51-55 tahun', 'score' => 2],
                ['label' => '> 55 tahun', 'score' => 1],
            ],
        ];
    }
}
