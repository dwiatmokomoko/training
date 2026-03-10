<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\Employee;
use App\Models\Criteria;

class AssessmentController extends Controller
{
    public function index()
    {
        $assessments = Assessment::with(['employee.position', 'scores.criteria'])
            ->orderBy('assessment_date', 'desc')
            ->paginate(15);
        
        return view('assessments.index', compact('assessments'));
    }

    public function create()
    {
        $employees = Employee::with('position')->get();
        $criteria = Criteria::all();
        
        return view('assessments.create', compact('employees', 'criteria'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'assessment_date' => 'required|date',
            'scores' => 'required|array',
            'scores.*' => 'required|integer|min:1|max:5',
            'notes' => 'nullable|string'
        ]);

        // Create the main assessment record
        $assessment = Assessment::create([
            'employee_id' => $request->employee_id,
            'assessment_date' => $request->assessment_date,
            'notes' => $request->notes,
            'total_score' => 0 // Will be calculated below
        ]);

        $totalScore = 0;
        $criteria = Criteria::all();

        // Create assessment scores for each criteria
        foreach ($request->scores as $criteriaId => $score) {
            $criterion = $criteria->find($criteriaId);
            if ($criterion) {
                $assessment->scores()->create([
                    'criteria_id' => $criteriaId,
                    'score' => $score
                ]);
                
                // Calculate weighted score
                $totalScore += ($score * $criterion->weight);
            }
        }

        // Update total score
        $assessment->update(['total_score' => $totalScore]);

        return redirect()->route('assessments.index')
            ->with('success', 'Penilaian berhasil ditambahkan!');
    }

    public function show(Assessment $assessment)
    {
        $assessment->load(['employee.position', 'scores.criteria']);
        return view('assessments.show', compact('assessment'));
    }

    public function edit(Assessment $assessment)
    {
        $employees = Employee::with('position')->get();
        $criteria = Criteria::all();
        
        return view('assessments.edit', compact('assessment', 'employees', 'criteria'));
    }

    public function update(Request $request, Assessment $assessment)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'assessment_date' => 'required|date',
            'scores' => 'required|array',
            'scores.*' => 'required|integer|min:1|max:5',
            'notes' => 'nullable|string'
        ]);

        // Update the main assessment record
        $assessment->update([
            'employee_id' => $request->employee_id,
            'assessment_date' => $request->assessment_date,
            'notes' => $request->notes
        ]);

        $totalScore = 0;
        $criteria = Criteria::all();

        // Update assessment scores for each criteria
        foreach ($request->scores as $criteriaId => $score) {
            $criterion = $criteria->find($criteriaId);
            if ($criterion) {
                $assessment->scores()->updateOrCreate(
                    ['criteria_id' => $criteriaId],
                    ['score' => $score]
                );
                
                // Calculate weighted score
                $totalScore += ($score * $criterion->weight);
            }
        }

        // Update total score
        $assessment->update(['total_score' => $totalScore]);

        return redirect()->route('assessments.show', $assessment)
            ->with('success', 'Penilaian berhasil diperbarui!');
    }

    public function destroy(Assessment $assessment)
    {
        $assessment->delete();

        return redirect()->route('assessments.index')
            ->with('success', 'Penilaian berhasil dihapus!');
    }

    public function bulkCreate()
    {
        $employees = Employee::with('position')->get();
        $criteria = Criteria::all();
        
        return view('assessments.bulk-create', compact('employees', 'criteria'));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'assessment_date' => 'required|date',
            'scores' => 'required|array',
            'scores.*' => 'required|integer|min:1|max:5',
            'notes' => 'nullable|string'
        ]);

        // Check if assessment already exists for this employee
        $existingAssessment = Assessment::where('employee_id', $request->employee_id)
            ->whereDate('assessment_date', $request->assessment_date)
            ->first();

        if ($existingAssessment) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Penilaian untuk pegawai ini pada tanggal tersebut sudah ada!');
        }

        // Create the main assessment record
        $assessment = Assessment::create([
            'employee_id' => $request->employee_id,
            'assessment_date' => $request->assessment_date,
            'notes' => $request->notes,
            'total_score' => 0 // Will be calculated below
        ]);

        $totalScore = 0;
        $criteria = Criteria::all();

        // Create assessment scores for each criteria
        foreach ($request->scores as $criteriaId => $score) {
            $criterion = $criteria->find($criteriaId);
            if ($criterion) {
                $assessment->scores()->create([
                    'criteria_id' => $criteriaId,
                    'score' => $score
                ]);
                
                // Calculate weighted score
                $totalScore += ($score * $criterion->weight);
            }
        }

        // Update total score
        $assessment->update(['total_score' => $totalScore]);

        return redirect()->route('assessments.index')
            ->with('success', 'Penilaian bulk berhasil ditambahkan!');
    }
}
