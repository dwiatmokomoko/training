<?php

namespace App\Http\Controllers;

use App\Models\AssessmentPeriod;
use App\Models\Employee;
use App\Models\PerformanceIndicator;
use App\Models\PerformanceScore;
use App\Models\SawCriterion;
use App\Models\SawScore;
use App\Models\TrainingPlan;
use App\Models\TrainingPlanParticipant;
use App\Services\SawCalculator;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TnaController extends Controller
{
    public function sawScores(Request $request)
    {
        $period = $this->selectedPeriod($request);

        return view('tna.saw-scores.index', [
            'periods' => AssessmentPeriod::query()->latest('started_at')->get(),
            'period' => $period,
            'scores' => SawScore::query()
                ->with(['period', 'employee.position', 'criterion'])
                ->when($period, fn ($query) => $query->where('assessment_period_id', $period->id))
                ->latest('updated_at')
                ->get(),
            'employeesWithoutManualScores' => Employee::query()
                ->with(['position', 'unit'])
                ->where('status', 'Aktif')
                ->when($period, function ($query) use ($period) {
                    $query->whereDoesntHave('sawScores', fn ($scoreQuery) => $scoreQuery->where('assessment_period_id', $period->id));
                })
                ->orderBy('name')
                ->limit(8)
                ->get(),
        ]);
    }

    public function createSawScore(Request $request)
    {
        return view('tna.saw-scores.form', [
            'score' => null,
            'periods' => AssessmentPeriod::query()->latest('started_at')->get(),
            'employees' => Employee::query()->where('status', 'Aktif')->orderBy('name')->get(),
            'criteria' => SawCriterion::query()->where('is_active', true)->orderBy('code')->get(),
            'defaults' => [
                'assessment_period_id' => $request->integer('period') ?: AssessmentPeriod::query()->latest('started_at')->value('id'),
                'employee_id' => $request->integer('employee'),
                'saw_criterion_id' => $request->integer('criterion'),
                'assessed_at' => now()->toDateString(),
            ],
        ]);
    }

    public function storeSawScore(Request $request)
    {
        $data = $this->validatedSawScore($request);

        SawScore::query()->updateOrCreate(
            collect($data)->only(['assessment_period_id', 'employee_id', 'saw_criterion_id'])->all(),
            collect($data)
                ->except(['assessment_period_id', 'employee_id', 'saw_criterion_id'])
                ->merge(['source' => 'Manual'])
                ->all()
        );

        return redirect()->route('tna.saw-scores.index', ['period' => $data['assessment_period_id']])
            ->with('status', 'Nilai SAW berhasil disimpan dan siap dipakai pada analisis.');
    }

    public function editSawScore(SawScore $score)
    {
        return view('tna.saw-scores.form', [
            'score' => $score,
            'periods' => AssessmentPeriod::query()->latest('started_at')->get(),
            'employees' => Employee::query()->where('status', 'Aktif')->orderBy('name')->get(),
            'criteria' => SawCriterion::query()->where('is_active', true)->orderBy('code')->get(),
            'defaults' => [],
        ]);
    }

    public function updateSawScore(Request $request, SawScore $score)
    {
        $data = $this->validatedSawScore($request);

        $score->update($data + ['source' => 'Manual']);

        return redirect()->route('tna.saw-scores.index', ['period' => $data['assessment_period_id']])
            ->with('status', 'Nilai SAW berhasil diperbarui.');
    }

    public function destroySawScore(SawScore $score)
    {
        $periodId = $score->assessment_period_id;
        $score->delete();

        return redirect()->route('tna.saw-scores.index', ['period' => $periodId])
            ->with('status', 'Nilai SAW berhasil dihapus. Analisis akan memakai nilai otomatis/default jika tersedia.');
    }

    public function analysis(Request $request, SawCalculator $calculator)
    {
        $period = $this->selectedPeriod($request);

        return view('tna.analysis', [
            'periods' => AssessmentPeriod::query()->latest('started_at')->get(),
            'period' => $period,
            'criteria' => SawCriterion::query()->where('is_active', true)->orderBy('code')->get(),
            'rankings' => $calculator->rankings($period),
            'showDetails' => $request->boolean('detail'),
        ]);
    }

    public function assessments(Request $request)
    {
        $period = $this->selectedPeriod($request);

        return view('tna.assessments', [
            'periods' => AssessmentPeriod::query()->latest('started_at')->get(),
            'period' => $period,
            'indicators' => PerformanceIndicator::query()->with('group')->orderBy('code')->get(),
            'scores' => PerformanceScore::query()
                ->with(['indicator', 'employee.group', 'employee.position'])
                ->when($period, fn ($query) => $query->where('assessment_period_id', $period->id))
                ->orderByDesc('updated_at')
                ->paginate(20),
        ]);
    }

    public function storeAssessment(Request $request)
    {
        $data = $request->validate([
            'assessment_period_id' => ['required', 'exists:assessment_periods,id'],
            'employee_id' => ['required', 'exists:employees,id'],
            'performance_indicator_id' => ['required', 'exists:performance_indicators,id'],
            'score' => ['required', 'integer', 'min:1', 'max:5'],
            'notes' => ['nullable', 'string'],
        ]);

        PerformanceScore::query()->updateOrCreate(
            collect($data)->only(['assessment_period_id', 'employee_id', 'performance_indicator_id'])->all(),
            collect($data)->only(['score', 'notes'])->merge(['assessed_at' => now()->toDateString()])->all()
        );

        return back()->with('status', 'Nilai penilaian berhasil disimpan.');
    }

    public function planning(Request $request, SawCalculator $calculator)
    {
        $period = $this->selectedPeriod($request);

        return view('tna.planning', [
            'periods' => AssessmentPeriod::query()->latest('started_at')->get(),
            'period' => $period,
            'rankings' => $calculator->rankings($period)->take(20),
            'plans' => TrainingPlan::query()->with(['period', 'participants.employee', 'participants.training'])->latest()->get(),
        ]);
    }

    public function storePlan(Request $request, SawCalculator $calculator)
    {
        $data = $request->validate([
            'assessment_period_id' => ['required', 'exists:assessment_periods,id'],
            'name' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'participant_limit' => ['required', 'integer', 'min:1', 'max:100'],
            'estimated_budget' => ['nullable', 'numeric', 'min:0'],
        ]);

        $period = AssessmentPeriod::query()->findOrFail($data['assessment_period_id']);
        $plan = TrainingPlan::query()->create([
            'assessment_period_id' => $period->id,
            'name' => $data['name'],
            'year' => $data['year'],
            'estimated_budget' => $data['estimated_budget'] ?? null,
            'planned_at' => now()->toDateString(),
            'status' => 'Draft',
            'notes' => 'Dibentuk otomatis dari ranking SAW.',
        ]);

        foreach ($calculator->rankings($period)->take($data['participant_limit']) as $row) {
            TrainingPlanParticipant::query()->create([
                'training_plan_id' => $plan->id,
                'employee_id' => $row['employee']->id,
                'training_id' => $row['training']?->id,
                'saw_score' => $row['score'],
                'rank' => $row['rank'],
                'priority_class' => $row['priority'],
            ]);
        }

        return back()->with('status', 'Rencana pelatihan otomatis berhasil dibuat dari ranking SAW.');
    }

    public function report(Request $request, SawCalculator $calculator)
    {
        $period = $this->selectedPeriod($request);

        return view('tna.report', [
            'periods' => AssessmentPeriod::query()->latest('started_at')->get(),
            'period' => $period,
            'criteria' => SawCriterion::query()->where('is_active', true)->orderBy('code')->get(),
            'rankings' => $calculator->rankings($period),
        ]);
    }

    public function export(Request $request, SawCalculator $calculator): StreamedResponse
    {
        $period = $this->selectedPeriod($request);
        $rankings = $calculator->rankings($period);
        $filename = 'laporan-tna-saw-'.($period?->code ?? 'periode').'.csv';

        return response()->streamDownload(function () use ($rankings) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Ranking', 'NIP', 'Nama', 'Jabatan', 'Unit Kerja', 'Skor SAW', 'Klasifikasi', 'Rekomendasi Pelatihan']);
            foreach ($rankings as $row) {
                fputcsv($handle, [
                    $row['rank'],
                    $row['employee']->nip,
                    $row['employee']->name,
                    $row['employee']->position?->name,
                    $row['employee']->unit?->name,
                    $row['score'],
                    $row['priority'],
                    $row['training']?->name,
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function selectedPeriod(Request $request): ?AssessmentPeriod
    {
        if ($request->filled('period')) {
            return AssessmentPeriod::query()->where('id', $request->integer('period'))->first();
        }

        return AssessmentPeriod::query()->latest('started_at')->first();
    }

    private function validatedSawScore(Request $request): array
    {
        return $request->validate([
            'assessment_period_id' => ['required', 'exists:assessment_periods,id'],
            'employee_id' => ['required', 'exists:employees,id'],
            'saw_criterion_id' => ['required', 'exists:saw_criteria,id'],
            'value' => ['required', 'numeric', 'min:0', 'max:100'],
            'assessor_name' => ['nullable', 'string', 'max:255'],
            'assessed_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
