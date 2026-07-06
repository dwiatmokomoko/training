<?php

namespace App\Http\Controllers;

use App\Models\AssessmentPeriod;
use App\Models\Employee;
use App\Models\EmployeeGroup;
use App\Models\TrainingPlan;
use App\Services\SawCalculator;

class DashboardController extends Controller
{
    public function __invoke(SawCalculator $calculator)
    {
        $period = AssessmentPeriod::query()->latest('started_at')->first();
        $rankings = $calculator->rankings($period);

        return view('dashboard', [
            'period' => $period,
            'rankings' => $rankings->take(8),
            'employeeCount' => Employee::query()->count(),
            'manualScoreMissingCount' => Employee::query()
                ->where('status', 'Aktif')
                ->when($period, fn ($query) => $query->whereDoesntHave('sawScores', fn ($scoreQuery) => $scoreQuery->where('assessment_period_id', $period->id)))
                ->count(),
            'groupStats' => EmployeeGroup::query()->withCount('employees')->get(),
            'mandatoryCount' => $rankings->where('priority', 'Wajib')->count(),
            'priorityCount' => $rankings->where('priority', 'Prioritas')->count(),
            'planCount' => TrainingPlan::query()->count(),
            'averageScore' => $rankings->avg('score') ?: 0,
        ]);
    }
}
