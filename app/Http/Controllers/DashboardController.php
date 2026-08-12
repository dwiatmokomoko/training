<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
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
            $jobFamilies = $this->jobFamilyOptions();
            $jobFamilyCode = $this->normalizeJobFamilyFilter($request->input('job_family'), $jobFamilies);
            $jobFamilyLabel = $jobFamilyCode ? $jobFamilies[$jobFamilyCode] : 'Semua Rumpun';
            $employees = Employee::with(['assessments.scores.criteria', 'position.jobFamily', 'workUnit', 'trainingHistories'])
                ->when($jobFamilyCode, function ($query) use ($jobFamilyCode) {
                    $query->whereHas('position.jobFamily', function ($jobFamilyQuery) use ($jobFamilyCode) {
                        $jobFamilyQuery->where('code', $jobFamilyCode);
                    });
                })
                ->get();
            $sawService = new SAWService();
            $results = $sawService->calculateTrainingNeeds($employees);

            if ($results->isEmpty()) {
                return $this->redirectAfterAnalysis($request, $periodYear, $periodSemester, $jobFamilyCode)
                    ->with('error', "Belum ada hasil SAW untuk {$jobFamilyLabel} {$periodYear} Semester {$periodSemester}. Pastikan pegawai pada rumpun ini sudah memiliki assessment lengkap.");
            }

            $created = $sawService->saveTrainingNeeds($results, $periodYear, $periodSemester, $jobFamilyCode);

            return $this->redirectAfterAnalysis($request, $periodYear, $periodSemester, $jobFamilyCode)
                ->with('success', "Analisis {$jobFamilyLabel} {$periodYear} Semester {$periodSemester} berhasil dijalankan. {$created} rekomendasi tersimpan.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function jobFamilyOptions(): array
    {
        return [
            'HK' => 'Hakim',
            'KP' => 'Kepaniteraan',
            'KS' => 'Kesekretariatan',
        ];
    }

    private function normalizeJobFamilyFilter(?string $jobFamily, array $jobFamilies): ?string
    {
        $jobFamily = trim((string) $jobFamily);

        if ($jobFamily === '' || strtolower($jobFamily) === 'all' || str_contains(strtolower($jobFamily), 'semua')) {
            return null;
        }

        return array_key_exists($jobFamily, $jobFamilies) ? $jobFamily : null;
    }

    private function redirectAfterAnalysis(Request $request, int $periodYear, int $periodSemester, ?string $jobFamilyCode)
    {
        if (! $request->boolean('return_to_training_needs')) {
            return redirect()->back();
        }

        return redirect()->route('training-needs.index', [
            'job_family' => $jobFamilyCode ?? '',
            'training_type' => '',
            'period' => $periodYear . '-S' . $periodSemester,
        ]);
    }
}
