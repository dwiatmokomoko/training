<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Position;
use App\Models\WorkUnit;
use App\Support\Access;

class EmployeeController extends Controller
{
    public function index()
    {
        Access::denyIfCannotAny(['employees.view', 'employees.manage']);

        $employees = Employee::with(['position.jobFamily', 'workUnit'])
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('nip', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('name')
            ->get();

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        Access::denyIfCannot('employees.manage');

        $positions = Position::with('jobFamily')->get();
        $workUnits = WorkUnit::with('parent')->orderBy('parent_id')->orderBy('name')->get();
        return view('employees.create', compact('positions', 'workUnits'));
    }

    public function store(Request $request)
    {
        Access::denyIfCannot('employees.manage');

        $request->validate([
            'nip' => 'required|unique:employees',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees',
            'position_id' => 'required|exists:positions,id',
            'work_unit_id' => 'nullable|exists:work_units,id',
            'education_level' => 'required|in:SMA,D3,S1,S2,S3',
            'work_experience' => 'required|integer|min:0',
            'current_position_start_date' => 'nullable|date',
            'last_promotion_date' => 'nullable|date',
            'last_training_date' => 'nullable|date',
            'birth_date' => 'required|date',
            'gender' => 'required|in:L,P',
            'phone' => 'nullable|string',
            'address' => 'nullable|string'
        ]);

        Employee::create($request->all());

        return redirect()->route('employees.index')
            ->with('success', 'Data pegawai berhasil ditambahkan!');
    }

    public function show(Employee $employee)
    {
        Access::denyIfCannotAny(['employees.view', 'employees.manage']);

        $employee->load([
            'position.jobFamily',
            'workUnit.parent',
            'assessments.scores.criteria',
            'trainingNeeds',
            'trainingHistories.creator',
        ]);

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        Access::denyIfCannot('employees.manage');

        $positions = Position::with('jobFamily')->get();
        $workUnits = WorkUnit::with('parent')->orderBy('parent_id')->orderBy('name')->get();
        return view('employees.edit', compact('employee', 'positions', 'workUnits'));
    }

    public function update(Request $request, Employee $employee)
    {
        Access::denyIfCannot('employees.manage');

        $request->validate([
            'nip' => 'required|unique:employees,nip,' . $employee->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'position_id' => 'required|exists:positions,id',
            'work_unit_id' => 'nullable|exists:work_units,id',
            'education_level' => 'required|in:SMA,D3,S1,S2,S3',
            'work_experience' => 'required|integer|min:0',
            'current_position_start_date' => 'nullable|date',
            'last_promotion_date' => 'nullable|date',
            'last_training_date' => 'nullable|date',
            'birth_date' => 'required|date',
            'gender' => 'required|in:L,P',
            'phone' => 'nullable|string',
            'address' => 'nullable|string'
        ]);

        $employee->update($request->all());

        return redirect()->route('employees.index')
            ->with('success', 'Data pegawai berhasil diperbarui!');
    }

    public function destroy(Employee $employee)
    {
        Access::denyIfCannot('employees.manage');

        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Data pegawai berhasil dihapus!');
    }
}
