<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\TrainingNeed;
use App\Models\Assessment;
use App\Models\Criteria;
use App\Services\SAWService;
use App\Support\Access;

class DashboardController extends Controller
{
    public function index()
    {
        Access::denyIfCannot('dashboard.view');

        return view('dashboard');
    }

    public function runAnalysis(Request $request)
    {
        Access::denyIfCannot('analysis.run');

        try {
            $periodYear = (int) $request->input('period_year', now()->year);
            $periodSemester = (int) $request->input('period_semester', now()->month <= 6 ? 1 : 2);
            $sawService = new SAWService();
            $results = $sawService->calculateTrainingNeeds();
            $sawService->saveTrainingNeeds($results, $periodYear, $periodSemester);

            return redirect()->back()
                ->with('success', "Analisis kebutuhan pelatihan {$periodYear} Semester {$periodSemester} berhasil dijalankan!");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
