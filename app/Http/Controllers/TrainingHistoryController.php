<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\TrainingHistory;
use App\Support\Access;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingHistoryController extends Controller
{
    public function store(Request $request, Employee $employee)
    {
        Access::denyIfCannot('training-history.manage');

        $validated = $request->validate([
            'training_name' => 'required|string|max:255',
            'provider' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'hours' => 'nullable|integer|min:1|max:1000',
            'certificate_number' => 'nullable|string|max:255',
            'result' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $employee->trainingHistories()->create($validated + [
            'created_by' => Auth::id(),
        ]);

        $this->syncLastTrainingDate($employee);

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'Riwayat pelatihan berhasil ditambahkan.');
    }

    public function destroy(TrainingHistory $trainingHistory)
    {
        Access::denyIfCannot('training-history.manage');

        $employee = $trainingHistory->employee;
        $trainingHistory->delete();
        $this->syncLastTrainingDate($employee);

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'Riwayat pelatihan berhasil dihapus.');
    }

    private function syncLastTrainingDate(Employee $employee): void
    {
        $latestDate = $employee->trainingHistories()
            ->selectRaw('MAX(COALESCE(end_date, start_date)) as latest_date')
            ->value('latest_date');

        $employee->update(['last_training_date' => $latestDate]);
    }
}
