<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\TrainingNeed;
use App\Models\Assessment;
use App\Models\Criteria;
use App\Services\SAWService;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function runAnalysis()
    {
        try {
            $sawService = new SAWService();
            $results = $sawService->calculateTrainingNeeds();
            $sawService->saveTrainingNeeds($results);

            return redirect()->route('dashboard')
                ->with('success', 'Analisis kebutuhan pelatihan berhasil dijalankan!');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
