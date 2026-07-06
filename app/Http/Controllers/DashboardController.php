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

    public function runAnalysis()
    {
        Access::denyIfCannot('analysis.run');

        try {
            $sawService = new SAWService();
            $results = $sawService->calculateTrainingNeeds();
            $sawService->saveTrainingNeeds($results);

            return redirect()->back()
                ->with('success', 'Analisis kebutuhan pelatihan berhasil dijalankan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
